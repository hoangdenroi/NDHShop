<?php

namespace App\Services;

use App\Models\CloudDatabase;
use App\Models\CloudPlanOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CloudPlanService — Xử lý toàn bộ logic Cloud Plan.
 *
 * Bao gồm:
 * - Tính giá theo gói + chu kỳ (có chiết khấu)
 * - Nâng cấp / Gia hạn (cộng dồn) / Downgrade (hoàn 70%)
 * - Kiểm tra quota, hết hạn, grace period
 *
 * Sử dụng DB::transaction (Atomicity) cho mọi thao tác thay đổi balance.
 */
class CloudPlanService
{
    // ──── Tính Giá ────

    /**
     * Tính giá thanh toán theo gói + chu kỳ (đã áp dụng chiết khấu).
     *
     * @return array{original_amount: int, discount_percent: int, discount_amount: int, final_amount: int, months: int}
     */
    public function calculatePrice(string $plan, string $cycle): array
    {
        $planConfig  = config("cloud_plan.plans.{$plan}");
        $cycleConfig = config("cloud_plan.billing_cycles.{$cycle}");

        if (!$planConfig || !$cycleConfig) {
            throw new \InvalidArgumentException("Gói dịch vụ hoặc chu kỳ không hợp lệ.");
        }

        $pricePerMonth = $planConfig['price'];
        $months        = $cycleConfig['months'];
        $discount      = $cycleConfig['discount'];

        $originalAmount = $pricePerMonth * $months;
        $discountAmount = (int) ($originalAmount * $discount / 100);
        $finalAmount    = $originalAmount - $discountAmount;

        return [
            'original_amount'  => $originalAmount,
            'discount_percent' => $discount,
            'discount_amount'  => $discountAmount,
            'final_amount'     => $finalAmount,
            'months'           => $months,
        ];
    }

    // ──── Nâng Cấp ────

    /**
     * Nâng cấp gói Cloud Plan.
     *
     * Flow:
     * 1. Validate gói + chu kỳ hợp lệ
     * 2. Tính giá (có chiết khấu)
     * 3. Kiểm tra số dư
     * 4. DB::transaction: trừ tiền → tạo order → cập nhật user → kích hoạt resource
     *
     * @throws \Exception Nếu không đủ số dư hoặc gói không hợp lệ
     */
    public function upgrade(User $user, string $plan, string $cycle): CloudPlanOrder
    {
        $this->validatePlan($plan);
        $this->validateCycle($cycle);

        // Không cho "upgrade" sang gói thấp hơn hoặc bằng
        $planOrder = ['free' => 0, 'pro' => 1, 'max' => 2];
        $currentPlanLevel = $planOrder[$user->cloud_plan] ?? 0;
        $newPlanLevel     = $planOrder[$plan] ?? 0;

        if ($newPlanLevel <= $currentPlanLevel && $user->cloud_plan !== 'free') {
            throw new \Exception("Không thể nâng cấp sang gói thấp hơn hoặc bằng gói hiện tại.");
        }

        // Nếu đang có gói trả phí → hoàn 70% giá trị còn lại trước
        $credit = 0;
        if (!$user->isFreePlan() && $user->isCloudPlanActive()) {
            $credit = $this->calculateRefund($user);
        }

        $pricing = $this->calculatePrice($plan, $cycle);
        $amountToPay = $pricing['final_amount'] - $credit;

        // Nếu credit > giá gói mới, phần dư cộng vào số dư
        if ($amountToPay < 0) {
            $amountToPay = 0;
            // Phần thừa sẽ được hoàn vào balance trong transaction
        }

        if ($amountToPay > 0 && (int) $user->balance < $amountToPay) {
            throw new \Exception(
                "Số dư tài khoản không đủ. Cần {$amountToPay}đ, hiện có " . number_format($user->balance) . "đ."
            );
        }

        return DB::transaction(function () use ($user, $plan, $cycle, $pricing, $credit, $amountToPay) {
            $balanceBefore = (float) $user->balance;

            // Hoàn credit từ gói cũ (nếu có)
            if ($credit > 0) {
                $user->increment('balance', $credit);
                $user->refresh();

                // Tạo record hoàn tiền
                CloudPlanOrder::create([
                    'user_id'          => $user->id,
                    'order_code'       => CloudPlanOrder::generateOrderCode(),
                    'plan'             => $user->cloud_plan,
                    'action'           => CloudPlanOrder::ACTION_REFUND,
                    'billing_cycle'    => $user->cloud_plan_billing_cycle,
                    'months'           => 0,
                    'original_amount'  => 0,
                    'discount_percent' => 0,
                    'amount'           => -$credit, // Số âm = hoàn tiền
                    'balance_before'   => $balanceBefore,
                    'balance_after'    => (float) $user->balance,
                    'starts_at'        => now(),
                    'expires_at'       => now(),
                    'note'             => "Hoàn 70% gói {$user->cloud_plan} khi upgrade sang {$plan}",
                ]);

                $balanceBefore = (float) $user->balance;
            }

            // Trừ tiền thanh toán gói mới
            if ($pricing['final_amount'] > 0) {
                $user->decrement('balance', $pricing['final_amount']);
                $user->refresh();
            }

            $startsAt  = now();
            $expiresAt = now()->addMonths($pricing['months']);

            // Tạo order nâng cấp
            $order = CloudPlanOrder::create([
                'user_id'          => $user->id,
                'order_code'       => CloudPlanOrder::generateOrderCode(),
                'plan'             => $plan,
                'action'           => CloudPlanOrder::ACTION_UPGRADE,
                'billing_cycle'    => $cycle,
                'months'           => $pricing['months'],
                'original_amount'  => $pricing['original_amount'],
                'discount_percent' => $pricing['discount_percent'],
                'amount'           => $pricing['final_amount'],
                'balance_before'   => $balanceBefore,
                'balance_after'    => (float) $user->balance,
                'starts_at'        => $startsAt,
                'expires_at'       => $expiresAt,
            ]);

            // Cập nhật user
            $user->update([
                'cloud_plan'               => $plan,
                'cloud_plan_billing_cycle'  => $cycle,
                'cloud_plan_expires_at'     => $expiresAt,
                'cloud_plan_grace_ends_at'  => null, // Xóa grace period
            ]);

            // Kích hoạt lại resource bị tạm dừng (nếu có)
            $this->reactivateSuspendedResources($user);

            Log::info('Cloud Plan upgrade success', [
                'user_id' => $user->id,
                'plan'    => $plan,
                'cycle'   => $cycle,
                'amount'  => $pricing['final_amount'],
                'credit'  => $credit,
            ]);

            AuditLogService::log(
                'cloud_plan_upgraded',
                $order,
                ['cloud_plan' => $user->getOriginal('cloud_plan'), 'balance' => $balanceBefore],
                ['cloud_plan' => $plan, 'balance' => (float) $user->balance, 'cycle' => $cycle],
                $user->id
            );

            return $order;
        });
    }

    // ──── Gia Hạn (Cộng Dồn) ────

    /**
     * Gia hạn gói Cloud Plan hiện tại — cộng dồn thời gian.
     *
     * VD: Gói Pro còn 15 ngày + gia hạn 3 tháng → hết hạn = 15 ngày + 90 ngày.
     */
    public function renew(User $user, string $cycle): CloudPlanOrder
    {
        if ($user->isFreePlan()) {
            throw new \Exception("Gói Free không cần gia hạn. Hãy nâng cấp lên Pro hoặc Max.");
        }

        $this->validateCycle($cycle);

        $plan    = $user->cloud_plan;
        $pricing = $this->calculatePrice($plan, $cycle);

        if ((int) $user->balance < $pricing['final_amount']) {
            throw new \Exception("Số dư tài khoản không đủ để gia hạn.");
        }

        return DB::transaction(function () use ($user, $plan, $cycle, $pricing) {
            $balanceBefore = (float) $user->balance;

            $user->decrement('balance', $pricing['final_amount']);
            $user->refresh();

            // Cộng dồn: lấy ngày hết hạn cũ (hoặc now nếu đã hết) + thêm tháng
            $baseDate  = ($user->cloud_plan_expires_at && $user->cloud_plan_expires_at->isFuture())
                ? $user->cloud_plan_expires_at
                : now();
            $expiresAt = $baseDate->copy()->addMonths($pricing['months']);

            $order = CloudPlanOrder::create([
                'user_id'          => $user->id,
                'order_code'       => CloudPlanOrder::generateOrderCode(),
                'plan'             => $plan,
                'action'           => CloudPlanOrder::ACTION_RENEW,
                'billing_cycle'    => $cycle,
                'months'           => $pricing['months'],
                'original_amount'  => $pricing['original_amount'],
                'discount_percent' => $pricing['discount_percent'],
                'amount'           => $pricing['final_amount'],
                'balance_before'   => $balanceBefore,
                'balance_after'    => (float) $user->balance,
                'starts_at'        => now(),
                'expires_at'       => $expiresAt,
            ]);

            $user->update([
                'cloud_plan_expires_at'    => $expiresAt,
                'cloud_plan_grace_ends_at' => null, // Xóa grace nếu đang trong grace
            ]);

            // Kích hoạt lại nếu đang suspended
            $this->reactivateSuspendedResources($user);

            Log::info('Cloud Plan renewed', [
                'user_id'    => $user->id,
                'plan'       => $plan,
                'cycle'      => $cycle,
                'expires_at' => $expiresAt->toDateTimeString(),
            ]);

            AuditLogService::log(
                'cloud_plan_renewed',
                $order,
                ['balance' => $balanceBefore],
                ['balance' => (float) $user->balance, 'expires_at' => $expiresAt->toDateTimeString()],
                $user->id
            );

            return $order;
        });
    }

    // ──── Downgrade (Hoàn 70%) ────

    /**
     * Hạ gói Cloud Plan — hoàn 70% giá trị còn lại.
     *
     * Flow:
     * 1. Tính giá trị còn lại → hoàn 70%
     * 2. Cộng tiền hoàn vào số dư
     * 3. Tạm dừng resource vượt quota gói mới
     * 4. Chuyển user sang gói Free (hoặc gói user chọn)
     *
     * @param string|null $newPlan Gói mới muốn chuyển sang (null = free)
     */
    public function downgrade(User $user, ?string $newPlan = 'free'): CloudPlanOrder
    {
        if ($user->isFreePlan()) {
            throw new \Exception("Bạn đang dùng gói Free, không thể hạ thêm.");
        }

        $newPlan = $newPlan ?: 'free';
        $refundAmount = $this->calculateRefund($user);

        return DB::transaction(function () use ($user, $newPlan, $refundAmount) {
            $balanceBefore = (float) $user->balance;
            $oldPlan       = $user->cloud_plan;

            // Hoàn tiền
            if ($refundAmount > 0) {
                $user->increment('balance', $refundAmount);
                $user->refresh();
            }

            $order = CloudPlanOrder::create([
                'user_id'          => $user->id,
                'order_code'       => CloudPlanOrder::generateOrderCode(),
                'plan'             => $newPlan,
                'action'           => CloudPlanOrder::ACTION_DOWNGRADE,
                'billing_cycle'    => $user->cloud_plan_billing_cycle,
                'months'           => 0,
                'original_amount'  => 0,
                'discount_percent' => 0,
                'amount'           => -$refundAmount,
                'balance_before'   => $balanceBefore,
                'balance_after'    => (float) $user->balance,
                'starts_at'        => now(),
                'expires_at'       => now(),
                'note'             => "Hoàn 70% giá trị còn lại gói {$oldPlan}",
            ]);

            // Cập nhật user sang gói mới
            $updateData = [
                'cloud_plan'               => $newPlan,
                'cloud_plan_grace_ends_at'  => null,
            ];

            if ($newPlan === 'free') {
                $updateData['cloud_plan_expires_at']    = null;
                $updateData['cloud_plan_billing_cycle'] = 'monthly';
            }

            $user->update($updateData);

            // Tạm dừng resource vượt quota gói mới
            $this->suspendExcessResources($user);

            Log::info('Cloud Plan downgraded', [
                'user_id'   => $user->id,
                'from'      => $oldPlan,
                'to'        => $newPlan,
                'refund'    => $refundAmount,
            ]);

            AuditLogService::log(
                'cloud_plan_downgraded',
                $order,
                ['cloud_plan' => $oldPlan, 'balance' => $balanceBefore],
                ['cloud_plan' => $newPlan, 'balance' => (float) $user->balance, 'refund' => $refundAmount],
                $user->id
            );

            return $order;
        });
    }

    // ──── Tính Hoàn Tiền ────

    /**
     * Tính số tiền hoàn khi downgrade giữa chừng (70% giá trị còn lại).
     */
    public function calculateRefund(User $user): int
    {
        if ($user->isFreePlan() || !$user->cloud_plan_expires_at) {
            return 0;
        }

        // Tìm order gốc gần nhất (upgrade hoặc renew)
        $latestOrder = CloudPlanOrder::where('user_id', $user->id)
            ->whereIn('action', [CloudPlanOrder::ACTION_UPGRADE, CloudPlanOrder::ACTION_RENEW])
            ->where('amount', '>', 0)
            ->latest()
            ->first();

        if (!$latestOrder || !$latestOrder->expires_at) {
            return 0;
        }

        // Tính tỉ lệ thời gian còn lại
        $totalDays    = $latestOrder->starts_at->diffInDays($latestOrder->expires_at);
        $remainDays   = max(0, now()->diffInDays($latestOrder->expires_at, false));

        if ($totalDays <= 0 || $remainDays <= 0) {
            return 0;
        }

        $ratio       = $remainDays / $totalDays;
        $remainValue = (int) ($latestOrder->amount * $ratio);
        $refundRate  = config('cloud_plan.refund_rate', 70);

        return (int) ($remainValue * $refundRate / 100);
    }

    // ──── Quota ────

    /**
     * Lấy quota DB cho user (tính cả override).
     */
    public function getDbQuota(User $user): array
    {
        return $user->getDbQuota();
    }

    /**
     * Kiểm tra user có thể tạo thêm DB không.
     */
    public function canCreateDatabase(User $user): bool
    {
        $quota = $user->getDbQuota();
        $currentCount = CloudDatabase::byUser($user->id)
            ->whereNotIn('status', [CloudDatabase::STATUS_DELETED])
            ->count();

        return $currentCount < $quota['max_databases'];
    }

    /**
     * Kiểm tra user có thể dùng engine này không.
     */
    public function canUseEngine(User $user, string $engine): bool
    {
        $quota = $user->getDbQuota();
        return in_array($engine, $quota['engines']);
    }

    // ──── Resource Management ────

    /**
     * Kích hoạt lại resource bị tạm dừng (sau khi nâng cấp/gia hạn).
     */
    private function reactivateSuspendedResources(User $user): void
    {
        $suspendedDbs = CloudDatabase::byUser($user->id)->suspended()->get();
        $provisioningService = app(DatabaseProvisioningService::class);

        foreach ($suspendedDbs as $db) {
            $provisioningService->reactivateDatabase($db);
        }
    }

    /**
     * Tạm dừng resource vượt quota gói mới (khi downgrade).
     *
     * Giữ lại DB theo thứ tự cũ nhất, suspend các DB mới nhất vượt quota.
     */
    private function suspendExcessResources(User $user): void
    {
        $quota = $user->getDbQuota();
        $maxDb = $quota['max_databases'];
        $allowedEngines = $quota['engines'];
        $provisioningService = app(DatabaseProvisioningService::class);

        // 1. Áp đặt cấu hình Connection & Storage mới cho TẤT CẢ các DB của user
        CloudDatabase::byUser($user->id)->update([
            'max_connections' => $quota['max_connections'],
            'max_storage_mb'  => $quota['max_db_storage_mb'] ?? 50,
        ]);

        $activeDbs = CloudDatabase::byUser($user->id)
            ->active()
            ->orderBy('created_at', 'asc')
            ->get();

        $activeCount = 0;

        foreach ($activeDbs as $db) {
            // 2. Chặn DB sử dụng Database Engine vi phạm gói (VD: Postgres ở gói Free)
            if (!in_array($db->engine, $allowedEngines)) {
                $provisioningService->suspendDatabase($db);
                continue;
            }

            // 3. Chặn các DB mới nhất bị vượt quota tổng số DB
            if ($activeCount >= $maxDb) {
                $provisioningService->suspendDatabase($db);
                continue;
            }

            // DB hợp lệ theo hệ quy chiếu mới được giữ lại
            $activeCount++;
        }
    }

    // ──── Validation ────

    /**
     * Validate tên gói hợp lệ (chỉ cho phép pro/max khi thanh toán).
     */
    private function validatePlan(string $plan): void
    {
        if (!in_array($plan, ['pro', 'max'])) {
            throw new \InvalidArgumentException("Gói dịch vụ không hợp lệ: {$plan}");
        }
    }

    /**
     * Validate chu kỳ thanh toán hợp lệ.
     */
    private function validateCycle(string $cycle): void
    {
        $validCycles = array_keys(config('cloud_plan.billing_cycles', []));
        if (!in_array($cycle, $validCycles)) {
            throw new \InvalidArgumentException("Chu kỳ thanh toán không hợp lệ: {$cycle}");
        }
    }
}
