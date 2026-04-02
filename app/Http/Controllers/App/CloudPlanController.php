<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\CloudDatabase;
use App\Models\Coupon;
use App\Services\CloudPlanService;
use App\Services\DatabaseProvisioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * CloudPlanController — Xử lý nâng cấp / gia hạn / downgrade gói Cloud Plan.
 *
 * Tất cả routes yêu cầu auth (middleware 'auth').
 * Thanh toán qua số dư tài khoản — không redirect ra cổng thanh toán.
 */
class CloudPlanController extends Controller
{
    public function __construct(
        private CloudPlanService $planService
    ) {}

    /**
     * API: Tính giá theo gói + chu kỳ (dùng cho UI hiển thị giá real-time).
     *
     * GET /apps/cloud-plan/calculate-price?plan=pro&cycle=quarterly
     */
    public function calculatePrice(Request $request)
    {
        $request->validate([
            'plan'  => 'required|string|in:pro,max',
            'cycle' => 'required|string|in:monthly,quarterly,semiannual,annual',
        ]);

        try {
            $pricing = $this->planService->calculatePrice(
                $request->input('plan'),
                $request->input('cycle'),
            );

            return response()->json([
                'success' => true,
                'data'    => $pricing,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Nâng cấp gói Cloud Plan.
     *
     * POST /apps/cloud-plan/upgrade
     * Body: { plan: 'pro'|'max', cycle: 'monthly'|'quarterly'|'semiannual'|'annual' }
     */
    public function upgrade(Request $request)
    {
        $request->validate([
            'plan'  => 'required|string|in:pro,max',
            'cycle' => 'required|string|in:monthly,quarterly,semiannual,annual',
        ]);

        try {
            $order = $this->planService->upgrade(
                $request->user(),
                $request->input('plan'),
                $request->input('cycle'),
            );

            return response()->json([
                'success' => true,
                'message' => 'Nâng cấp gói thành công!',
                'data'    => [
                    'order_code' => $order->order_code,
                    'plan'       => $order->plan,
                    'amount'     => $order->amount,
                    'expires_at' => $order->expires_at?->format('d/m/Y'),
                ],
            ]);
        } catch (\Exception $e) {
            Log::warning('Cloud Plan upgrade failed', [
                'user_id' => $request->user()->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Gia hạn gói hiện tại (cộng dồn thời gian).
     *
     * POST /apps/cloud-plan/renew
     * Body: { cycle: 'monthly'|'quarterly'|'semiannual'|'annual' }
     */
    public function renew(Request $request)
    {
        $request->validate([
            'cycle' => 'required|string|in:monthly,quarterly,semiannual,annual',
        ]);

        try {
            $order = $this->planService->renew(
                $request->user(),
                $request->input('cycle'),
            );

            return response()->json([
                'success' => true,
                'message' => 'Gia hạn thành công!',
                'data'    => [
                    'order_code' => $order->order_code,
                    'plan'       => $order->plan,
                    'amount'     => $order->amount,
                    'expires_at' => $order->expires_at?->format('d/m/Y'),
                ],
            ]);
        } catch (\Exception $e) {
            Log::warning('Cloud Plan renew failed', [
                'user_id' => $request->user()->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Hạ gói Cloud Plan (hoàn 70% giá trị còn lại).
     *
     * POST /apps/cloud-plan/downgrade
     * Body: { new_plan: 'free'|'pro' (optional, default 'free') }
     */
    public function downgrade(Request $request)
    {
        $request->validate([
            'new_plan' => 'nullable|string|in:free,pro',
        ]);

        try {
            $refundPreview = $this->planService->calculateRefund($request->user());

            $order = $this->planService->downgrade(
                $request->user(),
                $request->input('new_plan', 'free'),
            );

            return response()->json([
                'success' => true,
                'message' => "Đã hạ gói thành công. Hoàn " . number_format($refundPreview) . "đ vào số dư.",
                'data'    => [
                    'order_code'    => $order->order_code,
                    'new_plan'      => $order->plan,
                    'refund_amount' => abs($order->amount),
                    'balance_after' => $order->balance_after,
                ],
            ]);
        } catch (\Exception $e) {
            Log::warning('Cloud Plan downgrade failed', [
                'user_id' => $request->user()->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * API: Xem trước số tiền hoàn khi downgrade.
     *
     * GET /apps/cloud-plan/refund-preview
     */
    public function refundPreview(Request $request)
    {
        $refund = $this->planService->calculateRefund($request->user());

        return response()->json([
            'success'       => true,
            'refund_amount' => $refund,
            'refund_rate'   => config('cloud_plan.refund_rate'),
        ]);
    }

    /**
     * API: Lấy thông tin gói hiện tại của user.
     *
     * GET /apps/cloud-plan/current
     */
    public function current(Request $request)
    {
        $user = $request->user();
        $dbQuota      = $user->getDbQuota();
        $storageQuota = $user->getStorageQuota();

        return response()->json([
            'success' => true,
            'data'    => [
                'plan'          => $user->cloud_plan,
                'billing_cycle' => $user->cloud_plan_billing_cycle,
                'expires_at'    => $user->cloud_plan_expires_at?->format('d/m/Y'),
                'is_active'     => $user->isCloudPlanActive(),
                'in_grace'      => $user->isInGracePeriod(),
                'db_quota'      => $dbQuota,
                'storage_quota' => $storageQuota,
                'db_plan'       => $user->getDbPlan(),
                'storage_plan'  => $user->getStoragePlan(),
            ],
        ]);
    }

    /**
     * API: Áp dụng mã giảm giá cho Cloud Plan.
     *
     * POST /apps/cloud-plan/apply-coupon
     * Body: { code: 'ABC123', plan: 'pro', cycle: 'monthly' }
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code'  => 'required|string|max:50',
            'plan'  => 'required|string|in:pro,max',
            'cycle' => 'required|string|in:monthly,quarterly,semiannual,annual',
        ]);

        $coupon = Coupon::where('code', strtoupper(trim($request->input('code'))))->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không tồn tại.'], 422);
        }

        $pricing    = $this->planService->calculatePrice($request->input('plan'), $request->input('cycle'));
        $orderTotal = $pricing['final_amount'];

        if (!$coupon->isValid($orderTotal)) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.'], 422);
        }

        $discount   = (int) $coupon->calculateDiscount($orderTotal);
        $finalPrice = max(0, $orderTotal - $discount);

        return response()->json([
            'success'  => true,
            'message'  => 'Áp dụng mã giảm giá thành công!',
            'data'     => [
                'coupon_code'     => $coupon->code,
                'coupon_type'     => $coupon->type,
                'coupon_value'    => $coupon->value,
                'discount_amount' => $discount,
                'original_price'  => $orderTotal,
                'final_price'     => $finalPrice,
            ],
        ]);
    }

    /**
     * API: Tạo Database mới.
     *
     * POST /apps/cloud-plan/create-database
     * Body: { engine: 'mysql'|'postgresql', name: 'my_db' }
     */
    public function createDatabase(Request $request, DatabaseProvisioningService $provisioningService)
    {
        $request->validate([
            'engine' => 'required|string|in:mysql,postgresql',
            'name'   => 'required|string|regex:/^[a-zA-Z0-9_]{1,50}$/',
        ]);

        $user   = $request->user();
        $engine = $request->input('engine');
        $dbName = strtolower($request->input('name'));
        $quota  = $user->getDbQuota();

        // Kiểm tra quyền (engine theo gói)
        if (!in_array($engine, $quota['engines'])) {
            return response()->json(['success' => false, 'message' => "Gói {$user->cloud_plan} không hỗ trợ {$engine}."], 403);
        }

        // Kiểm tra quota DB số lượng
        if (!$this->planService->canCreateDatabase($user)) {
            return response()->json(['success' => false, 'message' => 'Bạn đã đạt giới hạn số lượng database cho gói hiện tại.'], 403);
        }

        try {
            // 1. Dùng Provisioning Service để tạo DB thật
            $connInfo = $provisioningService->createDatabase($engine, $dbName, $user);

            // 2. Lưu metadata vào bảng cloud_databases
            $cloudDb = CloudDatabase::create([
                'user_id'               => $user->id,
                'engine'                => $engine,
                'db_name'               => $connInfo['db_name'],
                'db_user'               => $connInfo['db_user'],
                'db_password_encrypted' => \Illuminate\Support\Facades\Crypt::encryptString($connInfo['password']),
                'host'                  => $connInfo['host'],
                'port'                  => $connInfo['port'],
                'status'                => CloudDatabase::STATUS_ACTIVE,
                'max_connections'       => $quota['max_connections'],
                'max_storage_mb'        => $quota['max_db_storage_mb'],
                'storage_used_mb'       => 0,
                'last_activity_at'      => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo database thành công!',
                'data'    => [
                    'database' => $cloudDb,
                    // Chỉ trả password 1 lần
                    'password' => $connInfo['password'],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * API: Xóa Database.
     *
     * DELETE /apps/cloud-plan/database/{cloudDatabase}
     */
    public function deleteDatabase(CloudDatabase $cloudDatabase, DatabaseProvisioningService $provisioningService)
    {
        $user = auth()->user();

        // Kiểm tra đúng chủ sở hữu
        if ($cloudDatabase->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thao tác trên database này.'], 403);
        }

        // Đã xóa rồi thì thôi
        if ($cloudDatabase->status === CloudDatabase::STATUS_DELETED) {
            return response()->json(['success' => false, 'message' => 'Database này đã được xóa trước đó.'], 400);
        }

        try {
            // 1. Xóa trên Server thật
            $provisioningService->deleteDatabase($cloudDatabase);

            // 2. Cập nhật state trên DB app
            $cloudDatabase->update([
                'status'     => CloudDatabase::STATUS_DELETED,
                'is_deleted' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Xóa database thành công!',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
