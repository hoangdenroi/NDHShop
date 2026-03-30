<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AuditLogService
{
    /**
     * Ghi log thao tác.
     *
     * @param string $action Hành động (vd: 'purchased_item', 'balance_deducted')
     * @param Model|null $model Model liên quan (vd: $order)
     * @param array|null $oldValues Giá trị cũ
     * @param array|null $newValues Giá trị mới
     * @param int|null $userId ID người dùng (mặc định Auth::id())
     * @return AuditLog|null
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null
    ): ?AuditLog {
        // Nếu qua command line hoặc job thì request() có thể k có IP
        $ipAddress = request()->ip();
        $userAgent = request()->userAgent();

        // Cắt dữ liệu quá dài trước khi lưu để tránh database lình kềnh
        $oldValues = self::truncateLongStrings($oldValues);
        $newValues = self::truncateLongStrings($newValues);

        return AuditLog::create([
            'user_id' => $userId ?: Auth::id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Giới hạn độ dài các chuỗi nội dung trong mảng JSON để tránh log bị phình to.
     */
    protected static function truncateLongStrings(?array $data, int $maxLength = 200): ?array
    {
        if (empty($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            if (is_string($value) && mb_strlen($value) > $maxLength) {
                // Cắt chuỗi và thêm '...'
                $data[$key] = mb_substr($value, 0, $maxLength) . '... [truncated]';
            } elseif (is_array($value)) {
                // Đệ quy nếu mảng phức tạp
                $data[$key] = self::truncateLongStrings($value, $maxLength);
            }
            
            // Ẩn mật khẩu hoặc token (nếu có vô tình truyền vào)
            if (in_array(strtolower($key), ['password', 'password_confirmation', 'token'])) {
                $data[$key] = '********';
            }
        }

        return $data;
    }
}
