<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Khởi tạo kết nối Polling lấy thông báo mới
     */
    public function pull(Request $request)
    {
        // Yêu cầu đăng nhập
        if (!$request->user()) {
            abort(401);
        }

        $userId = $request->user()->id;
        $lastId = (int) $request->query('last_id', 0);

        // Nếu client request lần đầu tiên (last_id = 0), chỉ trả về ID mới nhất để làm mốc, tránh pop-up đống thông báo cũ.
        if ($lastId === 0) {
            $maxId = Notification::where('user_id', $userId)->max('id') ?? 0;
            return response()->json([
                'success' => true,
                'notifications' => [],
                'last_id' => $maxId
            ]);
        }

        // Lấy các thông báo sinh ra SAU MỐC lastId
        $notifications = Notification::where('user_id', $userId)
            ->where('id', '>', $lastId)
            ->orderBy('id', 'asc')
            ->get();

        $payloads = [];
        $maxId = $lastId;

        foreach ($notifications as $notification) {
            $payload = [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type ?? 'info',
            ];

            // Nếu thông báo có gắn action (ví dụ: cập nhật số dư)
            if (is_array($notification->data) && isset($notification->data['action'])) {
                $payload['action'] = $notification->data['action'];
                if ($payload['action'] === 'update_balance') {
                    $payload['balance'] = $request->user()->fresh()->balance;
                }
            }

            $payloads[] = $payload;
            $maxId = max($maxId, $notification->id);

            // Tùy chọn: Đánh dấu là đã đọc
            $notification->update(['is_read' => true, 'read_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'notifications' => $payloads,
            'last_id' => $maxId
        ]);
    }
}
