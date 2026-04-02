<?php

namespace App\Console\Commands;

use App\Models\CloudDatabase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PDO;

/**
 * MonitorDatabaseActivity — Cron job giám sát dung lượng & connections.
 *
 * Chạy mỗi 10 phút: php artisan dbaas:monitor-activity
 *
 * Kết nối Admin vào MySQL/PostgreSQL server để lấy:
 * - Dung lượng thực tế (storage_used_mb) theo db_name
 * - Số connections đang hoạt động (active_connections) theo db_user
 * - Cập nhật last_activity_at nếu có connections
 */
class MonitorDatabaseActivity extends Command
{
    protected $signature = 'dbaas:monitor-activity';
    protected $description = 'Giám sát dung lượng và connections của các database DBaaS.';

    public function handle(): int
    {
        $this->info('Bắt đầu giám sát database activity...');

        $this->monitorMySQL();
        $this->monitorPostgreSQL();

        $this->info('Hoàn tất giám sát database activity.');
        return self::SUCCESS;
    }

    /**
     * Giám sát MySQL: dung lượng + connections.
     */
    private function monitorMySQL(): void
    {
        $config = config('db_servers.mysql');
        if (!$config) {
            $this->warn('Chưa cấu hình MySQL server, bỏ qua.');
            return;
        }

        try {
            $pdo = $this->getAdminConnection('mysql', $config);

            // 1. Quét dung lượng theo db_name (Dùng LEFT JOIN để bắt được DB rỗng chưa có table)
            $storageSql = "
                SELECT s.SCHEMA_NAME AS db_name,
                       COALESCE(ROUND(SUM(t.data_length + t.index_length) / 1024 / 1024, 2), 0) AS size_mb
                FROM information_schema.SCHEMATA s
                LEFT JOIN information_schema.TABLES t ON s.SCHEMA_NAME = t.TABLE_SCHEMA
                WHERE s.SCHEMA_NAME LIKE 'ndh_%'
                GROUP BY s.SCHEMA_NAME
            ";
            $storageData = $pdo->query($storageSql)->fetchAll();

            foreach ($storageData as $row) {
                CloudDatabase::where('db_name', $row['db_name'])
                    ->where('engine', CloudDatabase::ENGINE_MYSQL)
                    ->whereNotIn('status', [CloudDatabase::STATUS_DELETED])
                    ->update(['storage_used_mb' => $row['size_mb']]);
            }

            $this->line("  → MySQL: cập nhật dung lượng " . count($storageData) . " databases.");

            // 2. Quét connections theo db_user
            $connSql = "
                SELECT USER AS db_user, COUNT(*) AS conn_count
                FROM information_schema.PROCESSLIST
                WHERE USER LIKE 'ndh_%'
                GROUP BY USER
            ";
            $connData = $pdo->query($connSql)->fetchAll();

            // Map db_user => conn_count
            $connMap = [];
            foreach ($connData as $row) {
                $connMap[$row['db_user']] = (int) $row['conn_count'];
            }

            // Cập nhật active_connections cho tất cả DB MySQL
            $mysqlDbs = CloudDatabase::where('engine', CloudDatabase::ENGINE_MYSQL)
                ->whereNotIn('status', [CloudDatabase::STATUS_DELETED])
                ->get();

            foreach ($mysqlDbs as $db) {
                $activeConns = $connMap[$db->db_user] ?? 0;
                $updateData = ['active_connections' => $activeConns];

                // Cập nhật last_activity_at nếu có connections
                if ($activeConns > 0) {
                    $updateData['last_activity_at'] = now();
                }

                $db->update($updateData);
            }

            $this->line("  → MySQL: cập nhật connections " . $mysqlDbs->count() . " databases.");

        } catch (\Exception $e) {
            Log::error('DBaaS Monitor MySQL failed', ['error' => $e->getMessage()]);
            $this->error("  ✗ MySQL: " . $e->getMessage());
        }
    }

    /**
     * Giám sát PostgreSQL: dung lượng + connections.
     */
    private function monitorPostgreSQL(): void
    {
        $config = config('db_servers.postgresql');
        if (!$config) {
            $this->warn('Chưa cấu hình PostgreSQL server, bỏ qua.');
            return;
        }

        try {
            $pdo = $this->getAdminConnection('postgresql', $config);

            // 1. Quét dung lượng theo db_name
            $storageSql = "
                SELECT datname AS db_name,
                       ROUND(pg_database_size(datname)::numeric / 1024 / 1024, 2) AS size_mb
                FROM pg_database
                WHERE datname LIKE 'ndh_%'
            ";
            $storageData = $pdo->query($storageSql)->fetchAll();

            foreach ($storageData as $row) {
                CloudDatabase::where('db_name', $row['db_name'])
                    ->where('engine', CloudDatabase::ENGINE_POSTGRESQL)
                    ->whereNotIn('status', [CloudDatabase::STATUS_DELETED])
                    ->update(['storage_used_mb' => $row['size_mb']]);
            }

            $this->line("  → PostgreSQL: cập nhật dung lượng " . count($storageData) . " databases.");

            // 2. Quét connections theo db_user
            $connSql = "
                SELECT usename AS db_user, COUNT(*) AS conn_count
                FROM pg_stat_activity
                WHERE usename LIKE 'ndh_%' AND state IS NOT NULL
                GROUP BY usename
            ";
            $connData = $pdo->query($connSql)->fetchAll();

            // Map db_user => conn_count
            $connMap = [];
            foreach ($connData as $row) {
                $connMap[$row['db_user']] = (int) $row['conn_count'];
            }

            // Cập nhật active_connections cho tất cả DB PostgreSQL
            $pgDbs = CloudDatabase::where('engine', CloudDatabase::ENGINE_POSTGRESQL)
                ->whereNotIn('status', [CloudDatabase::STATUS_DELETED])
                ->get();

            foreach ($pgDbs as $db) {
                $activeConns = $connMap[$db->db_user] ?? 0;
                $updateData = ['active_connections' => $activeConns];

                if ($activeConns > 0) {
                    $updateData['last_activity_at'] = now();
                }

                $db->update($updateData);
            }

            $this->line("  → PostgreSQL: cập nhật connections " . $pgDbs->count() . " databases.");

        } catch (\Exception $e) {
            Log::error('DBaaS Monitor PostgreSQL failed', ['error' => $e->getMessage()]);
            $this->error("  ✗ PostgreSQL: " . $e->getMessage());
        }
    }

    /**
     * Tạo PDO connection admin tới server (dùng chung pattern với DatabaseProvisioningService).
     */
    private function getAdminConnection(string $engine, array $config): PDO
    {
        if ($engine === 'mysql') {
            $dsn = "mysql:host={$config['host']};port={$config['port']}";
        } else {
            $dsn = "pgsql:host={$config['host']};port={$config['port']}";
        }

        return new PDO($dsn, $config['admin_user'], $config['admin_password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 15,
        ]);
    }
}
