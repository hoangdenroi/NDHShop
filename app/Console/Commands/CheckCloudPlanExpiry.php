<?php

namespace App\Console\Commands;

use App\Models\CloudDatabase;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * CheckCloudPlanExpiry — Cron job kiểm tra hết hạn gói Cloud Plan.
 *
 * Chạy hàng ngày: php artisan cloud-plan:check-expiry
 *
 * 3 giai đoạn:
 * 1. Nhắc gia hạn (3 ngày trước khi hết hạn)
 * 2. Hết hạn → tạm dừng resource + bắt đầu grace period (7 ngày)
 * 3. Hết grace period → xóa vĩnh viễn resource bị tạm dừng
 */
class CheckCloudPlanExpiry extends Command
{
    protected $signature = 'cloud-plan:check-expiry';
    protected $description = 'Kiểm tra hết hạn gói Cloud Plan, tạm dừng/xóa resource.';

    public function handle(): int
    {
        $this->info('Bắt đầu kiểm tra Cloud Plan expiry...');

        $this->sendRenewalReminders();
        $this->handleExpired();
        $this->handleGraceExpired();

        $this->info('Hoàn tất kiểm tra Cloud Plan expiry.');
        return self::SUCCESS;
    }

    /**
     * Giai đoạn 1: Nhắc gia hạn 3 ngày trước khi hết hạn.
     */
    private function sendRenewalReminders(): void
    {
        $reminderDays = config('cloud_plan.renewal_reminder_days', 3);

        $users = User::where('cloud_plan', '!=', 'free')
            ->whereNotNull('cloud_plan_expires_at')
            ->whereBetween('cloud_plan_expires_at', [
                now(),
                now()->addDays($reminderDays),
            ])
            ->get();

        foreach ($users as $user) {
            $daysLeft = (int) now()->diffInDays($user->cloud_plan_expires_at, false);

            Notification::create([
                'user_id' => $user->id,
                'type'    => 'cloud_plan_reminder',
                'title'   => 'Gói dịch vụ sắp hết hạn',
                'message' => "Gói {$user->cloud_plan} sẽ hết hạn sau {$daysLeft} ngày. Hãy gia hạn để tránh mất dữ liệu.",
                'data'    => json_encode([
                    'plan'       => $user->cloud_plan,
                    'expires_at' => $user->cloud_plan_expires_at->format('d/m/Y'),
                ]),
            ]);

            $this->line("  → Nhắc gia hạn: User #{$user->id} ({$user->email}) - còn {$daysLeft} ngày");
        }

        Log::info("Cloud Plan: Đã gửi {$users->count()} nhắc gia hạn.");
    }

    /**
     * Giai đoạn 2: Hết hạn → tạm dừng resource + bắt đầu grace period.
     */
    private function handleExpired(): void
    {
        $graceDays = config('cloud_plan.grace_period_days', 7);

        $users = User::where('cloud_plan', '!=', 'free')
            ->whereNotNull('cloud_plan_expires_at')
            ->where('cloud_plan_expires_at', '<=', now())
            ->whereNull('cloud_plan_grace_ends_at') // Chưa vào grace
            ->get();

        foreach ($users as $user) {
            $oldPlan = $user->cloud_plan;

            // Chuyển về Free + bắt đầu grace
            $user->update([
                'cloud_plan'               => 'free',
                'cloud_plan_billing_cycle'  => 'monthly',
                'cloud_plan_grace_ends_at'  => now()->addDays($graceDays),
            ]);

            // Tạm dừng DB vượt quota Free
            $freeQuota = config('cloud_plan.plans.free');
            $maxFreeDb = $freeQuota['max_databases'];

            $userDbs = CloudDatabase::byUser($user->id)
                ->active()
                ->orderBy('created_at', 'asc')
                ->get();

            $toSuspend = $userDbs->slice($maxFreeDb);
            foreach ($toSuspend as $db) {
                $db->update(['status' => CloudDatabase::STATUS_SUSPENDED]);
            }

            Notification::create([
                'user_id' => $user->id,
                'type'    => 'cloud_plan_expired',
                'title'   => 'Gói dịch vụ đã hết hạn',
                'message' => "Gói {$oldPlan} đã hết hạn. Bạn có {$graceDays} ngày để gia hạn trước khi dữ liệu bị xóa.",
                'data'    => json_encode([
                    'old_plan'       => $oldPlan,
                    'grace_ends_at'  => now()->addDays($graceDays)->format('d/m/Y'),
                    'suspended_dbs'  => $toSuspend->count(),
                ]),
            ]);

            $this->line("  → Hết hạn: User #{$user->id} ({$user->email}) - suspend {$toSuspend->count()} DB");
        }

        Log::info("Cloud Plan: Đã xử lý {$users->count()} user hết hạn.");
    }

    /**
     * Giai đoạn 3: Hết grace period → xóa vĩnh viễn resource bị tạm dừng.
     */
    private function handleGraceExpired(): void
    {
        $users = User::whereNotNull('cloud_plan_grace_ends_at')
            ->where('cloud_plan_grace_ends_at', '<=', now())
            ->get();

        foreach ($users as $user) {
            // Xóa vĩnh viễn DB đang bị tạm dừng
            $suspendedDbs = CloudDatabase::byUser($user->id)
                ->suspended()
                ->get();

            foreach ($suspendedDbs as $db) {
                // TODO: Dispatch DeleteDatabaseJob để xóa DB thật trên engine
                $db->update([
                    'status'     => CloudDatabase::STATUS_DELETED,
                    'is_deleted' => true,
                ]);
            }

            // Xóa grace period
            $user->update(['cloud_plan_grace_ends_at' => null]);

            if ($suspendedDbs->count() > 0) {
                Notification::create([
                    'user_id' => $user->id,
                    'type'    => 'cloud_plan_data_deleted',
                    'title'   => 'Dữ liệu đã bị xóa',
                    'message' => "Do không gia hạn, {$suspendedDbs->count()} database đã bị xóa vĩnh viễn.",
                    'data'    => json_encode([
                        'deleted_dbs' => $suspendedDbs->pluck('db_name')->toArray(),
                    ]),
                ]);
            }

            $this->line("  → Grace hết: User #{$user->id} ({$user->email}) - xóa {$suspendedDbs->count()} DB");
        }

        Log::info("Cloud Plan: Đã xử lý {$users->count()} user hết grace period.");
    }
}
