<?php

namespace App\Services;

use App\Models\CloudDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PDO;
use PDOException;

/**
 * DatabaseProvisioningService — Tạo/Xóa/Suspend database thật trên server.
 *
 * Kết nối tới MySQL/PostgreSQL server qua PDO raw (không dùng Laravel DB connection).
 * Server config đọc từ config/db_servers.php → .env.
 *
 * Khi mua VPS riêng: chỉ cần đổi .env, không sửa code.
 */
class DatabaseProvisioningService
{
    /**
     * Tạo database + user thật trên server.
     *
     * @return array{db_name: string, db_user: string, password: string, host: string, port: int}
     *
     * @throws \RuntimeException Khi tạo DB thất bại
     */
    public function createDatabase(string $engine, string $dbName, User $user): array
    {
        // Validate tên DB (chống SQL injection cho DDL)
        $this->validateDbName($dbName);

        $serverConfig = $this->getServerConfig($engine);
        $fullDbName = 'ndh_'.$dbName;

        // Tái sử dụng user nếu đã tạo trước đó
        $existingDb = \App\Models\CloudDatabase::where('user_id', $user->id)
            ->where('engine', $engine)
            ->where('status', '!=', \App\Models\CloudDatabase::STATUS_DELETED)
            ->first();

        $isNewUser = true;
        if ($existingDb) {
            $dbUser = $existingDb->db_user;
            $password = $existingDb->getDecryptedPassword();
            $isNewUser = false;
        } else {
            $dbUser = 'ndh_u'.$user->id.'_'.\Illuminate\Support\Str::random(4);
            $password = \Illuminate\Support\Str::random(24);
        }

        try {
            $pdo = $this->getAdminConnection($engine, $serverConfig);

            if ($engine === CloudDatabase::ENGINE_MYSQL) {
                $this->provisionMySQL($pdo, $fullDbName, $dbUser, $password, $isNewUser);
            } else {
                $this->provisionPostgreSQL($pdo, $fullDbName, $dbUser, $password, $isNewUser);
            }

            Log::info('Database provisioned', [
                'engine' => $engine,
                'db_name' => $fullDbName,
                'db_user' => $dbUser,
                'user_id' => $user->id,
            ]);

            return [
                'db_name' => $fullDbName,
                'db_user' => $dbUser,
                'password' => $password,
                'host' => $serverConfig['public_host'],
                'port' => $serverConfig['port'],
            ];
        } catch (PDOException $e) {
            Log::error('Database provisioning failed', [
                'engine' => $engine,
                'db_name' => $fullDbName,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Không thể tạo database. Vui lòng thử lại sau.');
        }
    }

    /**
     * Xóa database + user thật trên server.
     */
    public function deleteDatabase(CloudDatabase $db): void
    {
        $serverConfig = $this->getServerConfig($db->engine);

        $remainingDbs = \App\Models\CloudDatabase::where('user_id', $db->user_id)
            ->where('engine', $db->engine)
            ->where('status', '!=', \App\Models\CloudDatabase::STATUS_DELETED)
            ->where('id', '!=', $db->id)
            ->count();
        $dropUser = ($remainingDbs === 0);

        try {
            $pdo = $this->getAdminConnection($db->engine, $serverConfig);

            if ($db->isMysql()) {
                $this->dropMySQL($pdo, $db->db_name, $db->db_user, $dropUser);
            } else {
                $this->dropPostgreSQL($pdo, $db->db_name, $db->db_user, $dropUser);
            }

            Log::info('Database deleted', [
                'engine' => $db->engine,
                'db_name' => $db->db_name,
            ]);
        } catch (PDOException $e) {
            Log::error('Database deletion failed', [
                'db_name' => $db->db_name,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Không thể xóa database. Vui lòng thử lại sau.');
        }
    }

    /**
     * Tạm dừng database — REVOKE quyền truy cập.
     */
    public function suspendDatabase(CloudDatabase $db): void
    {
        $serverConfig = $this->getServerConfig($db->engine);

        try {
            $pdo = $this->getAdminConnection($db->engine, $serverConfig);

            if ($db->isMysql()) {
                $quotedUser = $pdo->quote($db->db_user);
                $pdo->exec("REVOKE ALL PRIVILEGES ON `{$this->quoteIdentifier($db->db_name)}`.* FROM {$quotedUser}@'%'");
                $pdo->exec('FLUSH PRIVILEGES');
            } else {
                $pdo->exec("REVOKE ALL PRIVILEGES ON DATABASE \"{$this->quoteIdentifier($db->db_name)}\" FROM \"{$this->quoteIdentifier($db->db_user)}\"");
            }

            $db->update(['status' => CloudDatabase::STATUS_SUSPENDED]);

            Log::info('Database suspended', ['db_name' => $db->db_name]);
        } catch (PDOException $e) {
            Log::error('Database suspend failed', [
                'db_name' => $db->db_name,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Kích hoạt lại database — GRANT lại quyền truy cập.
     */
    public function reactivateDatabase(CloudDatabase $db): void
    {
        $serverConfig = $this->getServerConfig($db->engine);

        try {
            $pdo = $this->getAdminConnection($db->engine, $serverConfig);

            if ($db->isMysql()) {
                $quotedUser = $pdo->quote($db->db_user);
                $pdo->exec("GRANT ALL PRIVILEGES ON `{$this->quoteIdentifier($db->db_name)}`.* TO {$quotedUser}@'%'");
                $pdo->exec('FLUSH PRIVILEGES');
            } else {
                $pdo->exec("GRANT ALL PRIVILEGES ON DATABASE \"{$this->quoteIdentifier($db->db_name)}\" TO \"{$this->quoteIdentifier($db->db_user)}\"");
            }

            $db->update(['status' => CloudDatabase::STATUS_ACTIVE]);

            Log::info('Database reactivated', ['db_name' => $db->db_name]);
        } catch (PDOException $e) {
            Log::error('Database reactivate failed', [
                'db_name' => $db->db_name,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Kiểm tra server có sẵn sàng không (test connection).
     */
    public function isServerAvailable(string $engine): bool
    {
        try {
            $serverConfig = $this->getServerConfig($engine);
            $pdo = $this->getAdminConnection($engine, $serverConfig);
            $pdo->query('SELECT 1');

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // ═══════════════════════════════════════════
    // PRIVATE: MySQL Provisioning
    // ═══════════════════════════════════════════

    private function provisionMySQL(PDO $pdo, string $dbName, string $dbUser, string $password, bool $isNewUser): void
    {
        $quotedDb = $this->quoteIdentifier($dbName);
        $quotedUser = $pdo->quote($dbUser);
        $quotedPass = $pdo->quote($password);

        // Tạo database
        $pdo->exec("CREATE DATABASE `{$quotedDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        if ($isNewUser) {
            $pdo->exec("CREATE USER IF NOT EXISTS {$quotedUser}@'%' IDENTIFIED WITH mysql_native_password BY {$quotedPass}");
            $pdo->exec("CREATE USER IF NOT EXISTS {$quotedUser}@'localhost' IDENTIFIED BY {$quotedPass}");
        }

        // Lệnh cấp cho mọi IP truy cập từ xa (dùng cho VPS)
        $pdo->exec("GRANT ALL PRIVILEGES ON `{$quotedDb}`.* TO {$quotedUser}@'%'");
        // Lệnh cấp riêng cho 'localhost'
        $pdo->exec("GRANT ALL PRIVILEGES ON `{$quotedDb}`.* TO {$quotedUser}@'localhost'");

        $pdo->exec('FLUSH PRIVILEGES');
    }

    private function dropMySQL(PDO $pdo, string $dbName, string $dbUser, bool $dropUser): void
    {
        $quotedDb = $this->quoteIdentifier($dbName);
        $quotedUser = $pdo->quote($dbUser);

        $pdo->exec("DROP DATABASE IF EXISTS `{$quotedDb}`");
        if ($dropUser) {
            $pdo->exec("DROP USER IF EXISTS {$quotedUser}@'%'");
            $pdo->exec("DROP USER IF EXISTS {$quotedUser}@'localhost'");
        }
        $pdo->exec('FLUSH PRIVILEGES');
    }

    // ═══════════════════════════════════════════
    // PRIVATE: PostgreSQL Provisioning
    // ═══════════════════════════════════════════

    private function provisionPostgreSQL(PDO $pdo, string $dbName, string $dbUser, string $password, bool $isNewUser): void
    {
        $quotedDb = $this->quoteIdentifier($dbName);
        $quotedUser = $this->quoteIdentifier($dbUser);
        $quotedPass = $pdo->quote($password);

        // Tạo user
        if ($isNewUser) {
            $pdo->exec("CREATE USER \"{$quotedUser}\" WITH PASSWORD {$quotedPass}");
        }

        // Tạo database với owner
        $pdo->exec("CREATE DATABASE \"{$quotedDb}\" OWNER \"{$quotedUser}\" ENCODING 'UTF8'");

        // GRANT
        $pdo->exec("GRANT ALL PRIVILEGES ON DATABASE \"{$quotedDb}\" TO \"{$quotedUser}\"");
    }

    private function dropPostgreSQL(PDO $pdo, string $dbName, string $dbUser, bool $dropUser): void
    {
        $quotedDb = $this->quoteIdentifier($dbName);
        $quotedUser = $this->quoteIdentifier($dbUser);

        // Disconnect tất cả sessions trước khi drop
        $pdo->exec("SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = '{$quotedDb}' AND pid <> pg_backend_pid()");
        $pdo->exec("DROP DATABASE IF EXISTS \"{$quotedDb}\"");
        
        if ($dropUser) {
            $pdo->exec("DROP USER IF EXISTS \"{$quotedUser}\"");
        }
    }

    // ═══════════════════════════════════════════
    // PRIVATE: Connection + Validation
    // ═══════════════════════════════════════════

    /**
     * Tạo PDO connection admin tới server.
     */
    private function getAdminConnection(string $engine, array $config): PDO
    {
        if ($engine === CloudDatabase::ENGINE_MYSQL) {
            $dsn = "mysql:host={$config['host']};port={$config['port']}";
        } else {
            $dsn = "pgsql:host={$config['host']};port={$config['port']}";
        }

        return new PDO($dsn, $config['admin_user'], $config['admin_password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 10,
        ]);
    }

    /**
     * Lấy config server theo engine.
     */
    private function getServerConfig(string $engine): array
    {
        $config = config("db_servers.{$engine}");

        if (! $config) {
            throw new \InvalidArgumentException("Engine không được hỗ trợ: {$engine}");
        }

        return $config;
    }

    /**
     * Validate tên DB — chỉ cho phép [a-zA-Z0-9_], chống SQL injection cho DDL.
     */
    private function validateDbName(string $name): void
    {
        if (! preg_match('/^[a-zA-Z0-9_]{1,50}$/', $name)) {
            throw new \InvalidArgumentException('Tên database chỉ được chứa chữ cái, số và dấu gạch dưới (tối đa 50 ký tự).');
        }
    }

    /**
     * Quote identifier — loại bỏ ký tự nguy hiểm cho DDL statements.
     * DDL (CREATE DATABASE, CREATE USER) không hỗ trợ prepared statements,
     * nên phải whitelist + sanitize thủ công.
     */
    private function quoteIdentifier(string $identifier): string
    {
        // Chỉ giữ lại [a-zA-Z0-9_] — đảm bảo an toàn tuyệt đối
        return preg_replace('/[^a-zA-Z0-9_]/', '', $identifier);
    }
}
