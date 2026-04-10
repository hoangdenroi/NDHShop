<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\PendingRequest;

/**
 * Service gọi Hetzner Cloud REST API
 *
 * Quản lý server lifecycle: tạo, xóa, restart, rebuild, đổi mật khẩu
 * Sync dữ liệu: server_types, images, locations
 *
 * @see https://docs.hetzner.cloud/
 */
class HetznerService
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->token = config('services.hetzner.token', '');
        $this->baseUrl = config('services.hetzner.base_url', 'https://api.hetzner.cloud/v1');
    }

    /**
     * Tạo HTTP client với Bearer token.
     */
    private function client(): PendingRequest
    {
        return Http::withToken($this->token)
            ->baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout(30);
    }

    // =========================================================================
    // SERVER MANAGEMENT
    // =========================================================================

    /**
     * Tạo server mới trên Hetzner.
     *
     * @param string $name          Tên server (VD: "ndhshop-vps-abc123")
     * @param string $serverType    Loại server (VD: "cpx11")
     * @param string $image         HĐH (VD: "ubuntu-22.04")
     * @param string $location      Vị trí (VD: "fsn1")
     * @return array                ['server_id', 'ipv4', 'ipv6', 'root_password']
     * @throws \Exception           Khi API trả lỗi
     */
    public function createServer(
        string $name,
        string $serverType,
        string $image,
        string $location,
        array $sshKeys = []
    ): array {
        $payload = [
            'name' => $name,
            'server_type' => strtolower(trim($serverType)),
            'image' => $image,
            'location' => $location,
            'start_after_create' => true,
        ];

        if (!empty($sshKeys)) {
            $payload['ssh_keys'] = $sshKeys;
        }

        $response = $this->client()->post('/servers', $payload);

        if (!$response->successful()) {
            $body = $response->json();
            $errorCode = $body['error']['code'] ?? '';
            $errorMessage = $body['error']['message'] ?? 'Không thể tạo server trên Hetzner.';

            Log::error('Hetzner createServer failed', [
                'status' => $response->status(),
                'body' => $body,
            ]);

            if ($errorCode === 'resource_limit_exceeded') {
                throw new \Exception("Tài khoản API Hetzner của bạn đã bị giới hạn tài nguyên (Limit Exceeded) cho loại máy chủ chuyên dụng này. Bạn cần vào bảng điều khiển Hetzner -> Limits để xin tăng hạng mức.");
            }

            throw new \Exception("Hetzner API Error: {$errorMessage}");
        }

        $data = $response->json();
        $server = $data['server'] ?? [];
        $rootPassword = $data['root_password'] ?? null;

        // Lấy IPv4 từ public_net
        $ipv4 = $server['public_net']['ipv4']['ip'] ?? null;
        $ipv6 = $server['public_net']['ipv6']['ip'] ?? null;

        return [
            'server_id' => $server['id'],
            'ipv4' => $ipv4,
            'ipv6' => $ipv6,
            'root_password' => $rootPassword,
        ];
    }

    /**
     * Xóa server trên Hetzner.
     *
     * @param int $serverId    ID server trên Hetzner
     * @return bool
     */
    public function deleteServer(int $serverId): bool
    {
        $response = $this->client()->delete("/servers/{$serverId}");

        if (!$response->successful()) {
            Log::error('Hetzner deleteServer failed', [
                'server_id' => $serverId,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Lấy thông tin server.
     *
     * @param int $serverId
     * @return array|null
     */
    public function getServer(int $serverId): ?array
    {
        $response = $this->client()->get("/servers/{$serverId}");

        if (!$response->successful()) {
            return null;
        }

        return $response->json('server');
    }

    // =========================================================================
    // SSH KEYS
    // =========================================================================

    /**
     * Tạo SSH Key mới trên Hetzner.
     *
     * @param string $name Tên SSH Key
     * @param string $publicKey Nội dung Public Key (ssh-rsa ...)
     * @return int|null ID của SSH Key (hoặc null nếu lỗi)
     */
    public function createSshKey(string $name, string $publicKey): ?int
    {
        $response = $this->client()->post('/ssh_keys', [
            'name' => $name,
            'public_key' => $publicKey,
        ]);

        if ($response->successful()) {
            return $response->json('ssh_key.id');
        }

        // Nếu lỗi 409 (Conflict - Key Name hoặc Key Content đã tồn tại), 
        // ta có thể lấy danh sách ssh keys để tìm ID của nó,
        // nhưng ở đây lấy ID được return.
        $body = $response->json();
        $errorCode = $body['error']['code'] ?? '';

        if ($errorCode === 'uniqueness_error') {
            // Đã tồn tại Key có Nội dung hoặc Tên trùng, ta GET /ssh_keys để tìm
            $allKeys = $this->client()->get('/ssh_keys')->json('ssh_keys', []);
            foreach ($allKeys as $keyItem) {
                if ($keyItem['public_key'] === trim($publicKey) || $keyItem['name'] === $name) {
                    return $keyItem['id'];
                }
            }
        }

        Log::error('Hetzner createSshKey failed', [
            'status' => $response->status(),
            'body' => $body,
        ]);

        return null;
    }

    // =========================================================================
    // SERVER ACTIONS
    // =========================================================================

    /**
     * Soft reboot server.
     */
    public function rebootServer(int $serverId): array
    {
        return $this->serverAction($serverId, 'reboot');
    }

    /**
     * Hard reset server.
     */
    public function resetServer(int $serverId): array
    {
        return $this->serverAction($serverId, 'reset');
    }

    /**
     * Bật server.
     */
    public function powerOnServer(int $serverId): array
    {
        return $this->serverAction($serverId, 'poweron');
    }

    /**
     * Tắt server.
     */
    public function powerOffServer(int $serverId): array
    {
        return $this->serverAction($serverId, 'shutdown');
    }

    /**
     * Reset mật khẩu root — Hetzner trả về password mới.
     *
     * @return array ['root_password' => '...']
     */
    public function resetPassword(int $serverId): array
    {
        $response = $this->client()->post("/servers/{$serverId}/actions/reset_password");

        if (!$response->successful()) {
            $error = $response->json('error.message', 'Không thể đổi mật khẩu.');
            throw new \Exception("Hetzner API Error: {$error}");
        }

        return [
            'root_password' => $response->json('root_password'),
        ];
    }

    /**
     * Rebuild server với HĐH mới — Hetzner trả về password mới.
     *
     * @param int    $serverId
     * @param string $image     VD: "ubuntu-22.04"
     * @return array ['root_password' => '...']
     */
    public function rebuildServer(int $serverId, string $image): array
    {
        $response = $this->client()->post("/servers/{$serverId}/actions/rebuild", [
            'image' => $image,
        ]);

        if (!$response->successful()) {
            $error = $response->json('error.message', 'Không thể cài lại HĐH.');
            throw new \Exception("Hetzner API Error: {$error}");
        }

        return [
            'root_password' => $response->json('root_password'),
        ];
    }

    // =========================================================================
    // SYNC DATA (Server Types, Images, Locations)
    // =========================================================================

    /**
     * Lấy danh sách server types từ Hetzner.
     * Dùng để admin chọn khi tạo gói VPS.
     *
     * @return array [['name' => 'cpx11', 'cores' => 2, 'memory' => 2, 'disk' => 40, ...], ...]
     */
    public function getServerTypes(): array
    {
        $allTypes = [];
        $page = 1;

        do {
            $response = $this->client()->get('/server_types', [
                'page' => $page,
                'per_page' => 50,
            ]);

            if (!$response->successful()) {
                Log::error('Hetzner getServerTypes failed', ['page' => $page]);
                break;
            }

            $data = $response->json();
            $types = $data['server_types'] ?? [];
            $allTypes = array_merge($allTypes, $types);

            $lastPage = $data['meta']['pagination']['last_page'] ?? 1;
            $page++;
        } while ($page <= $lastPage);

        return $allTypes;
    }

    /**
     * Lấy danh sách OS images từ Hetzner.
     *
     * @return array [['name' => 'ubuntu-22.04', 'description' => 'Ubuntu 22.04', ...], ...]
     */
    public function getImages(): array
    {
        $allImages = [];
        $page = 1;

        do {
            $response = $this->client()->get('/images', [
                'type' => 'system',
                'status' => 'available',
                'page' => $page,
                'per_page' => 50,
            ]);

            if (!$response->successful()) {
                Log::error('Hetzner getImages failed', ['page' => $page]);
                break;
            }

            $data = $response->json();
            $images = $data['images'] ?? [];
            $allImages = array_merge($allImages, $images);

            $lastPage = $data['meta']['pagination']['last_page'] ?? 1;
            $page++;
        } while ($page <= $lastPage);

        return $allImages;
    }

    /**
     * Lấy danh sách locations từ Hetzner.
     *
     * @return array [['name' => 'fsn1', 'city' => 'Falkenstein', ...], ...]
     */
    public function getLocations(): array
    {
        $response = $this->client()->get('/locations');

        if (!$response->successful()) {
            Log::error('Hetzner getLocations failed');
            return [];
        }

        return $response->json('locations', []);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Thực hiện server action chung (reboot, reset, poweron, shutdown).
     */
    private function serverAction(int $serverId, string $action): array
    {
        $response = $this->client()->post("/servers/{$serverId}/actions/{$action}");

        if (!$response->successful()) {
            $error = $response->json('error.message', "Không thể thực hiện {$action}.");
            throw new \Exception("Hetzner API Error: {$error}");
        }

        return $response->json('action', []);
    }
}
