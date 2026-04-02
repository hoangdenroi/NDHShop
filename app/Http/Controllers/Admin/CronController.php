<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * CronController — Quản lý và chạy thủ công các Cron Job từ giao diện Admin.
 *
 * Cho phép Admin click chạy ngay 1 command mà không cần SSH vào server.
 */
class CronController extends Controller
{
    /**
     * Danh sách các Cron Job có sẵn trong hệ thống.
     */
    private function getAvailableJobs(): array
    {
        return [
            [
                'command'     => 'dbaas:monitor-activity',
                'name'        => 'Giám sát Database',
                'description' => 'Quét dung lượng và connections của tất cả database DBaaS.',
                'schedule'    => 'Mỗi 10 phút',
                'icon'        => 'monitoring',
                'color'       => 'blue',
            ],
            [
                'command'     => 'cloud-plan:check-expiry',
                'name'        => 'Kiểm tra hết hạn gói',
                'description' => 'Nhắc gia hạn, tạm dừng resource hết hạn, xóa sau grace period.',
                'schedule'    => 'Hàng ngày',
                'icon'        => 'event_busy',
                'color'       => 'amber',
            ],
            [
                'command'     => 'gift:expire',
                'name'        => 'Vô hiệu Gift hết hạn',
                'description' => 'Tự động vô hiệu hóa các gift template đã quá hạn sử dụng.',
                'schedule'    => 'Hàng ngày',
                'icon'        => 'redeem',
                'color'       => 'emerald',
            ],
        ];
    }

    /**
     * Hiển thị danh sách Cron Job.
     */
    public function index()
    {
        $jobs = $this->getAvailableJobs();

        return view('pages.admin.cron-index', compact('jobs'));
    }

    /**
     * Chạy 1 command Artisan.
     */
    public function run(Request $request)
    {
        $request->validate([
            'command' => 'required|string|max:100',
        ]);

        $command = $request->input('command');

        // Chỉ cho phép chạy các command đã đăng ký
        $allowedCommands = array_column($this->getAvailableJobs(), 'command');
        if (!in_array($command, $allowedCommands)) {
            return response()->json([
                'success' => false,
                'message' => 'Command không được phép chạy.',
            ], 403);
        }

        try {
            $exitCode = Artisan::call($command);
            $output = Artisan::output();

            Log::info("Admin chạy Cron thủ công: {$command}", [
                'admin_id' => $request->user()->id,
                'exit_code' => $exitCode,
            ]);

            return response()->json([
                'success' => $exitCode === 0,
                'message' => $exitCode === 0
                    ? "Chạy [{$command}] thành công!"
                    : "Command kết thúc với mã lỗi: {$exitCode}",
                'output'  => trim($output),
            ]);
        } catch (\Exception $e) {
            Log::error("Admin Cron failed: {$command}", [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
