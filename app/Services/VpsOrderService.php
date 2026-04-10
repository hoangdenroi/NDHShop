<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;
use App\Models\VpsCategory;
use App\Models\VpsOrder;
use App\Models\VpsOrderLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Service xử lý logic nghiệp vụ VPS
 *
 * Mua, hủy, gia hạn, restart, đổi mật khẩu, rebuild
 * Tất cả thao tác tài chính sử dụng DB::transaction với lockForUpdate
 */
class VpsOrderService
{
    private HetznerService $hetzner;

    public function __construct(HetznerService $hetzner)
    {
        $this->hetzner = $hetzner;
    }

    /**
     * Đặt mua VPS — Trừ balance → Tạo server trên Hetzner → Lưu kết quả.
     *
     * @param VpsCategory $category  Gói VPS
     * @param string      $os        HĐH (hetzner_name, VD: "ubuntu-22.04")
     * @param string      $location  Location (hetzner_name, VD: "fsn1")
     * @param int         $months    Số tháng
     * @param string|null $couponCode Mã giảm giá
     * @param string|null $note      Ghi chú
     * @return VpsOrder
     * @throws \Exception
     */
    public function purchase(
        VpsCategory $category,
        string $os,
        string $location,
        int $months,
        string $connectionMethod = 'password',
        ?string $sshKeyName = null,
        ?string $sshKeyContent = null,
        ?string $couponCode = null,
        ?string $note = null
    ): VpsOrder {
        $user = Auth::user();
        $orderCode = 'VPS-' . strtoupper(Str::random(6));

        // Tính giá
        $basePrice = $category->price * $months;
        $discount = 0;

        if ($couponCode) {
            $coupon = Coupon::where('code', strtoupper(trim($couponCode)))->first();
            if ($coupon && $coupon->isValid($basePrice)) {
                $discount = (int) $coupon->calculateDiscount($basePrice);
            }
        }

        $totalPrice = max(0, $basePrice - $discount);

        // Bắt đầu transaction — trừ tiền + tạo đơn
        $order = DB::transaction(function () use (
            $user, $category, $orderCode, $totalPrice, $discount,
            $couponCode, $os, $location, $months, $note
        ) {
            // Lock user row tránh race condition
            $lockedUser = User::lockForUpdate()->findOrFail($user->id);

            if ($lockedUser->balance < $totalPrice) {
                $shortage = $totalPrice - $lockedUser->balance;
                throw new \Exception(
                    'Số dư không đủ. Vui lòng nạp thêm ' . number_format($shortage, 0, ',', '.') . 'đ.'
                );
            }

            // Trừ balance
            $oldBalance = $lockedUser->balance;
            User::where('id', $lockedUser->id)->decrement('balance', $totalPrice);

            // Tạo đơn hàng (pending)
            $order = VpsOrder::create([
                'user_id' => $lockedUser->id,
                'vps_category_id' => $category->id,
                'order_code' => $orderCode,
                'price' => $totalPrice,
                'duration_months' => $months,
                'operating_system' => $os,
                'location' => $location,
                'note' => $note,
                'status' => 'pending',
                'coupon_code' => $couponCode ? strtoupper(trim($couponCode)) : null,
                'discount_amount' => $discount,
            ]);

            // Log tạo đơn
            VpsOrderLog::create([
                'vps_order_id' => $order->id,
                'user_id' => $lockedUser->id,
                'action' => 'created',
                'detail' => "Đặt mua gói {$category->name}, {$months} tháng, HĐH: {$os}, Location: {$location}",
                'amount' => $totalPrice,
            ]);

            // Tăng used_count coupon nếu có
            if ($couponCode) {
                Coupon::where('code', strtoupper(trim($couponCode)))->increment('used_count');
            }

            // Tăng sold_count cho gói VPS
            VpsCategory::where('id', $category->id)->increment('sold_count');

            // Audit log
            if (class_exists(\App\Services\AuditLogService::class)) {
                \App\Services\AuditLogService::log(
                    'vps_purchased',
                    $order,
                    ['balance' => (float) $oldBalance],
                    ['balance' => (float) ($oldBalance - $totalPrice), 'order_code' => $orderCode],
                    $lockedUser->id
                );
            }

            return $order;
        });

        // Phân luồng theo provision_type
        if ($category->isAuto()) {
            // === AUTO: Gọi Hetzner API tạo server ===
            try {
                $order->update(['status' => 'provisioning']);

                // Mặc định tên cho sshKeyHetzner
                $sshKeysArray = [];

                // Xử lý tạo SSH Key bên Hetzner nếu user chọn kết nối bằng SSH
                if ($connectionMethod === 'ssh' && $sshKeyName && $sshKeyContent) {
                    $uniqueKeyName = 'ndhshop-' . strtolower($order->order_code) . '-' . Str::slug($sshKeyName);
                    // Tạo SSH Key trên Hetzner
                    $sshKeyId = $this->hetzner->createSshKey($uniqueKeyName, trim($sshKeyContent));
                    if ($sshKeyId) {
                        $sshKeysArray[] = $sshKeyId;
                        // Lưu SSH key name vào note để admin hoặc user theo dõi sau
                        $order->update(['note' => trim($order->note . "\n\n[SSH Key Name]: " . $sshKeyName)]);
                    }
                }

                $serverName = 'ndhshop-' . strtolower($order->order_code);
                $result = $this->hetzner->createServer(
                    $serverName,
                    $category->hetzner_server_type,
                    $os,
                    $location,
                    $sshKeysArray
                );

                $order->update([
                    'hetzner_server_id' => $result['server_id'],
                    'ip_address' => $result['ipv4'],
                    'ipv6_address' => $result['ipv6'],
                    'username' => 'root',
                    'root_password' => $result['root_password'],
                    'status' => 'active',
                    'expires_at' => now()->addMonths($order->duration_months),
                ]);

                VpsOrderLog::create([
                    'vps_order_id' => $order->id,
                    'user_id' => $user->id,
                    'action' => 'provisioned',
                    'detail' => "Server tạo thành công. IP: {$result['ipv4']}, ID: {$result['server_id']}",
                ]);

            } catch (\Exception $e) {
                // Tạo server thất bại → hoàn tiền
                DB::transaction(function () use ($order, $user, $e) {
                    User::where('id', $user->id)->increment('balance', $order->price);
                    $order->update(['status' => 'failed']);

                    VpsOrderLog::create([
                        'vps_order_id' => $order->id,
                        'user_id' => $user->id,
                        'action' => 'failed',
                        'detail' => 'Tạo server thất bại, đã hoàn tiền. Lỗi: ' . $e->getMessage(),
                        'amount' => $order->price,
                    ]);
                });

                throw new \Exception(
                    'Không thể tạo VPS. Số tiền đã được hoàn vào tài khoản. Vui lòng thử lại sau.'
                );
            }
        } else {
            // === MANUAL: Giữ pending, chờ admin giao thủ công ===
            VpsOrderLog::create([
                'vps_order_id' => $order->id,
                'user_id' => $user->id,
                'action' => 'pending_manual',
                'detail' => 'Đơn hàng VPS thủ công. Chờ admin giao thông tin kết nối.',
            ]);
        }

        return $order->fresh();
    }

    /**
     * Hủy đơn hàng VPS — Xóa server Hetzner + Hoàn tiền theo tỷ lệ.
     *
     * @param VpsOrder $order
     * @return VpsOrder
     * @throws \Exception
     */
    public function cancel(VpsOrder $order): VpsOrder
    {
        if (!$order->canCancel()) {
            throw new \Exception('Đơn hàng không thể hủy ở trạng thái hiện tại.');
        }

        $refundAmount = $order->refundAmount();
        $user = Auth::user();

        // Xóa server trên Hetzner nếu đã provision
        if ($order->hetzner_server_id) {
            try {
                $this->hetzner->deleteServer($order->hetzner_server_id);
            } catch (\Exception $e) {
                // Log lỗi nhưng vẫn tiếp tục hủy đơn
                VpsOrderLog::create([
                    'vps_order_id' => $order->id,
                    'user_id' => $user->id,
                    'action' => 'delete_server_error',
                    'detail' => 'Lỗi xóa server Hetzner: ' . $e->getMessage(),
                ]);
            }
        }

        DB::transaction(function () use ($order, $refundAmount, $user) {
            // Hoàn tiền
            if ($refundAmount > 0) {
                User::where('id', $order->user_id)->increment('balance', $refundAmount);
            }

            $order->update(['status' => 'cancelled']);

            VpsOrderLog::create([
                'vps_order_id' => $order->id,
                'user_id' => $user->id,
                'action' => 'cancelled',
                'detail' => "Đã hủy đơn hàng. Hoàn tiền: " . number_format($refundAmount, 0, ',', '.') . 'đ',
                'amount' => $refundAmount,
            ]);

            if (class_exists(\App\Services\AuditLogService::class)) {
                \App\Services\AuditLogService::log(
                    'vps_cancelled',
                    $order,
                    ['status' => 'active'],
                    ['status' => 'cancelled', 'refund' => $refundAmount],
                    $user->id
                );
            }
        });

        return $order->fresh();
    }

    /**
     * Gia hạn VPS — Trừ balance + cộng thời hạn.
     *
     * @param VpsOrder $order
     * @param int      $months   Số tháng gia hạn
     * @return VpsOrder
     * @throws \Exception
     */
    public function renew(VpsOrder $order, int $months): VpsOrder
    {
        if (!$order->canRenew()) {
            throw new \Exception('VPS này không thể gia hạn.');
        }

        $category = $order->vpsCategory;
        $renewPrice = $category->price * $months;
        $user = Auth::user();

        DB::transaction(function () use ($order, $renewPrice, $months, $user) {
            $lockedUser = User::lockForUpdate()->findOrFail($user->id);

            if ($lockedUser->balance < $renewPrice) {
                $shortage = $renewPrice - $lockedUser->balance;
                throw new \Exception(
                    'Số dư không đủ để gia hạn. Cần thêm ' . number_format($shortage, 0, ',', '.') . 'đ.'
                );
            }

            User::where('id', $lockedUser->id)->decrement('balance', $renewPrice);

            // Cộng dồn thời hạn (nếu còn hạn thì cộng tiếp, hết hạn thì tính từ bây giờ)
            $baseDate = ($order->expires_at && $order->expires_at->isFuture())
                ? $order->expires_at
                : now();

            $order->update([
                'expires_at' => $baseDate->copy()->addMonths($months),
                'duration_months' => $order->duration_months + $months,
            ]);

            VpsOrderLog::create([
                'vps_order_id' => $order->id,
                'user_id' => $lockedUser->id,
                'action' => 'renewed',
                'detail' => "Gia hạn thêm {$months} tháng. Hết hạn mới: {$order->fresh()->expires_at}",
                'amount' => $renewPrice,
            ]);
        });

        return $order->fresh();
    }

    /**
     * Restart VPS (soft reboot) — chỉ áp dụng cho gói auto Hetzner.
     */
    public function reboot(VpsOrder $order): void
    {
        $this->validateActiveOrder($order);
        $this->requireHetznerServer($order);

        $this->hetzner->rebootServer($order->hetzner_server_id);

        VpsOrderLog::create([
            'vps_order_id' => $order->id,
            'user_id' => Auth::id(),
            'action' => 'restarted',
            'detail' => 'VPS đã được restart.',
        ]);
    }

    /**
     * Reset mật khẩu root VPS — chỉ áp dụng cho gói auto Hetzner.
     */
    public function resetPassword(VpsOrder $order): string
    {
        $this->validateActiveOrder($order);
        $this->requireHetznerServer($order);

        $result = $this->hetzner->resetPassword($order->hetzner_server_id);
        $newPassword = $result['root_password'];

        $order->update(['root_password' => $newPassword]);

        VpsOrderLog::create([
            'vps_order_id' => $order->id,
            'user_id' => Auth::id(),
            'action' => 'password_reset',
            'detail' => 'Đã đổi mật khẩu root VPS.',
        ]);

        return $newPassword;
    }

    /**
     * Rebuild VPS — Cài lại HĐH mới — chỉ áp dụng cho gói auto Hetzner.
     */
    public function rebuild(VpsOrder $order, string $image): void
    {
        $this->validateActiveOrder($order);
        $this->requireHetznerServer($order);

        $result = $this->hetzner->rebuildServer($order->hetzner_server_id, $image);

        $order->update([
            'operating_system' => $image,
            'root_password' => $result['root_password'],
        ]);

        VpsOrderLog::create([
            'vps_order_id' => $order->id,
            'user_id' => Auth::id(),
            'action' => 'rebuilt',
            'detail' => "Đã cài lại HĐH: {$image}. Mật khẩu root mới đã được cập nhật.",
        ]);
    }

    /**
     * Guard: yêu cầu đơn hàng phải có Hetzner server_id (gói auto).
     */
    private function requireHetznerServer(VpsOrder $order): void
    {
        if (!$order->hetzner_server_id) {
            throw new \Exception('VPS thủ công không hỗ trợ chức năng này. Liên hệ admin.');
        }
    }

    /**
     * Admin giao VPS thủ công — Điền IP, username, password cho đơn pending manual.
     *
     * @param VpsOrder $order
     * @param string   $ipAddress
     * @param string   $username
     * @param string   $password
     * @param string|null $ipv6Address
     * @param string|null $adminNote
     * @return VpsOrder
     * @throws \Exception
     */
    public function fulfill(
        VpsOrder $order,
        string $ipAddress,
        string $username,
        string $password,
        ?string $ipv6Address = null,
        ?string $adminNote = null
    ): VpsOrder {
        if ($order->status !== 'pending') {
            throw new \Exception('Chỉ có thể giao đơn hàng ở trạng thái chờ xử lý.');
        }

        $order->update([
            'ip_address' => $ipAddress,
            'ipv6_address' => $ipv6Address,
            'username' => $username,
            'root_password' => $password,
            'admin_note' => $adminNote,
            'status' => 'active',
            'expires_at' => now()->addMonths($order->duration_months),
        ]);

        VpsOrderLog::create([
            'vps_order_id' => $order->id,
            'user_id' => Auth::id(),
            'action' => 'fulfilled',
            'detail' => "Admin đã giao VPS thủ công. IP: {$ipAddress}, Username: {$username}",
        ]);

        return $order->fresh();
    }

    /**
     * Kiểm tra đơn hàng có thể thực hiện action.
     * Hỗ trợ cả gói auto (Hetzner) và manual.
     */
    private function validateActiveOrder(VpsOrder $order): void
    {
        if ($order->status !== 'active') {
            throw new \Exception('VPS không ở trạng thái hoạt động.');
        }

        if ($order->isExpired()) {
            throw new \Exception('VPS đã hết hạn sử dụng.');
        }
    }
}
