<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServerController extends Controller
{
    /**
     * Trang giám sát Server Health.
     * Trả view + API lấy dữ liệu realtime.
     */
    public function index(Request $request)
    {
        // Nếu là AJAX request -> trả JSON dữ liệu hệ thống
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($this->getServerMetrics());
        }

        // Lần đầu render trang: trả về View kèm dữ liệu khởi tạo
        $metrics = $this->getServerMetrics();

        return view('pages.admin.server', compact('metrics'));
    }

    /**
     * Lấy thông số cấu hình hệ thống
     */
    private function getServerMetrics(): array
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return $this->getWindowsMetrics();
        }
        
        return $this->getLinuxMetrics();
    }

    /**
     * Lấy Metrics cực mượt cho Windows (Môi trường Dev)
     * - Dùng Cache TTL 2s để chống ngập request (bottleneck)
     * - Gộp tất cả commands thành 1 lần gọi Powershell duy nhất
     */
    private function getWindowsMetrics(): array
    {
        return Cache::remember('admin_server_win_metrics_2s', 2, function () {
            // Script powershell nén, gom dữ liệu CPU, RAM, Network, Uptime trong 1 scope
            $psCommand = '$c=Get-CimInstance Win32_Processor;$cpu=($c|Measure-Object -Property LoadPercentage -Average).Average;$cores=($c|Measure-Object -Property NumberOfLogicalProcessors -Sum).Sum;$r=Get-CimInstance Win32_OperatingSystem;$rt=[math]::Round((Get-CimInstance Win32_ComputerSystem).TotalPhysicalMemory/1KB);$rf=[math]::Round($r.FreePhysicalMemory);$rn=Get-CimInstance Win32_PerfFormattedData_Tcpip_NetworkInterface;$rx=0;$tx=0;if($rn){$rx=[math]::Round(($rn|Measure-Object -Property BytesReceivedPersec -Sum).Sum/1MB,2);$tx=[math]::Round(($rn|Measure-Object -Property BytesSentPersec -Sum).Sum/1MB,2);}$up=[math]::Round(((Get-Date)-$r.LastBootUpTime).TotalSeconds);Write-Output (\'{0}|{1}|{2}|{3}|{4}|{5}|{6}\' -f $cpu,$cores,$rt,$rf,$rx,$tx,$up)';
            
            $output = shell_exec('powershell -NoProfile -NonInteractive -Command "' . $psCommand . '" 2>nul');
            
            $cpuPercent = 0; $cores = 1; 
            $ramTotalKb = 0; $ramFreeKb = 0; 
            $rxSpeed = 0; $txSpeed = 0; 
            $uptimeSeconds = 0;

            if ($output && trim($output) !== '') {
                $parts = explode('|', trim($output));
                if (count($parts) >= 7) {
                    $cpuPercent = (float) $parts[0];
                    $cores = (int) $parts[1];
                    $ramTotalKb = (int) $parts[2];
                    $ramFreeKb = (int) $parts[3];
                    $rxSpeed = (float) $parts[4];
                    $txSpeed = (float) $parts[5];
                    $uptimeSeconds = (int) $parts[6];
                }
            } else {
                $cores = (int) ($_ENV['NUMBER_OF_PROCESSORS'] ?? getenv('NUMBER_OF_PROCESSORS') ?: 1);
            }

            // Tính toán logic RAM
            $ramTotal = round($ramTotalKb / 1048576, 1);
            $ramAvail = round($ramFreeKb / 1048576, 1);
            $ramUsed = round(($ramTotalKb - $ramFreeKb) / 1048576, 1);
            $ramPercent = $ramTotalKb > 0 ? round(($ramTotalKb - $ramFreeKb) / $ramTotalKb * 100, 1) : 0;

            // Xếp hạng CPU
            $cpuStatus = 'Hoạt động ổn định';
            if ($cpuPercent >= 85) $cpuStatus = 'Tải cao!';
            elseif ($cpuPercent >= 60) $cpuStatus = 'Tải trung bình';

            // Dịch Uptime
            $days = floor($uptimeSeconds / 86400);
            $hours = floor(($uptimeSeconds % 86400) / 3600);
            $mins = floor(($uptimeSeconds % 3600) / 60);
            $uptimeStr = $uptimeSeconds > 0 ? "{$days}d {$hours}h {$mins}m" : 'N/A';

            return [
                'cpu' => [
                    'percent' => $cpuPercent,
                    'cores' => (int)$cores,
                    'status' => $cpuStatus,
                ],
                'ram' => [
                    'total_gb' => $ramTotal,
                    'used_gb' => $ramUsed,
                    'available_gb' => $ramAvail,
                    'percent' => $ramPercent,
                ],
                'disk' => $this->getDiskInfo(),
                'network' => [
                    'rx_mbps' => $rxSpeed,
                    'tx_mbps' => $txSpeed,
                ],
                'uptime' => $uptimeStr,
                'hostname' => gethostname(),
                'timestamp' => now()->format('H:i:s d/m/Y'),
            ];
        });
    }

    /**
     * Lấy Metrics cho Linux (Môi trường Producton trên VPS)
     * - Chỉ mất 0.5s do gộp luồng chờ CPU và Network
     */
    private function getLinuxMetrics(): array
    {
        // Gộp thời gian chờ của CPU và Network thành 1 lần chờ duy nhất (0.5s)
        $cpu1 = $this->readCpuStat();
        $net1 = $this->readNetStats();
        
        usleep(500000); // Ngủ đúng 0.5s
        
        $cpu2 = $this->readCpuStat();
        $net2 = $this->readNetStats();
        
        // 1. Tính toán CPU
        $cpuPercent = 0;
        if ($cpu1 && $cpu2) {
            $diffIdle = $cpu2['idle'] - $cpu1['idle'];
            $diffTotal = $cpu2['total'] - $cpu1['total'];
            $cpuPercent = $diffTotal > 0 ? round((1 - $diffIdle / $diffTotal) * 100, 1) : 0;
        }

        $cores = (int) trim(shell_exec('nproc 2>/dev/null') ?? '1');
        $cpuStatus = 'Hoạt động ổn định';
        if ($cpuPercent >= 85) {
            $cpuStatus = 'Tải cao!';
        } elseif ($cpuPercent >= 60) {
            $cpuStatus = 'Tải trung bình';
        }

        // 2. Tính toán Network
        $rxSpeed = 0; $txSpeed = 0;
        if ($net1 && $net2) {
            $diffRx = $net2['rx'] - $net1['rx'];
            $diffTx = $net2['tx'] - $net1['tx'];
            $rxSpeed = round(($diffRx * 2) / 1048576, 2); // Nhân đôi vì đo trong mốc 0.5s
            $txSpeed = round(($diffTx * 2) / 1048576, 2);
        }

        // 3. Tính toán RAM
        $meminfo = @file_get_contents('/proc/meminfo');
        $ramTotal = 0; $ramAvail = 0; $ramUsed = 0; $ramPercent = 0;
        if ($meminfo) {
            preg_match('/MemTotal:\s+(\d+)/', $meminfo, $mTotal);
            preg_match('/MemAvailable:\s+(\d+)/', $meminfo, $mAvailable);
            
            $totalKb = (int) ($mTotal[1] ?? 0);
            $availKb = (int) ($mAvailable[1] ?? 0);
            
            $ramTotal = round($totalKb / 1048576, 1);
            $ramAvail = round($availKb / 1048576, 1);
            $ramUsed = round(($totalKb - $availKb) / 1048576, 1);
            $ramPercent = $totalKb > 0 ? round(($totalKb - $availKb) / $totalKb * 100, 1) : 0;
        }

        // 4. Tính toán Uptime
        $uptimeSeconds = 0;
        $content = trim(@file_get_contents('/proc/uptime') ?: '0');
        $uptimeSeconds = (int) explode(' ', $content)[0];
        
        $uptimeStr = 'N/A';
        if ($uptimeSeconds > 0) {
            $days = floor($uptimeSeconds / 86400);
            $hours = floor(($uptimeSeconds % 86400) / 3600);
            $mins = floor(($uptimeSeconds % 3600) / 60);
            $uptimeStr = "{$days}d {$hours}h {$mins}m";
        }

        return [
            'cpu' => [
                'percent' => $cpuPercent,
                'cores' => (int)$cores,
                'status' => $cpuStatus,
            ],
            'ram' => [
                'total_gb' => $ramTotal,
                'used_gb' => $ramUsed,
                'available_gb' => $ramAvail,
                'percent' => $ramPercent,
            ],
            'disk' => $this->getDiskInfo(),
            'network' => [
                'rx_mbps' => $rxSpeed,
                'tx_mbps' => $txSpeed,
            ],
            'uptime' => $uptimeStr,
            'hostname' => gethostname(),
            'timestamp' => now()->format('H:i:s d/m/Y'),
        ];
    }

    /**
     * Disk: Dùng hàm PHP built-in (Nhanh và hoạt động chuẩn trên mọi OS)
     */
    private function getDiskInfo(): array
    {
        $path = PHP_OS_FAMILY === 'Windows' ? 'C:' : '/';
        $totalBytes = @disk_total_space($path);
        $freeBytes = @disk_free_space($path);

        $total = $totalBytes ? round($totalBytes / 1073741824, 1) : 0;
        $free = $freeBytes ? round($freeBytes / 1073741824, 1) : 0;
        $used = round($total - $free, 1);
        $percent = $total > 0 ? round($used / $total * 100, 1) : 0;

        return [
            'total_gb' => $total,
            'used_gb' => $used,
            'free_gb' => $free,
            'percent' => $percent,
        ];
    }

    /**
     * Linux: Đọc dòng "cpu" đầu tiên từ /proc/stat
     */
    private function readCpuStat(): ?array
    {
        $line = @file_get_contents('/proc/stat');
        if (! $line) return null;

        $firstLine = strtok($line, "\n");
        $parts = preg_split('/\s+/', trim($firstLine));
        if (count($parts) < 5) return null;

        $user = (int) $parts[1];
        $nice = (int) $parts[2];
        $system = (int) $parts[3];
        $idle = (int) $parts[4];
        $iowait = (int) ($parts[5] ?? 0);
        $irq = (int) ($parts[6] ?? 0);
        $softirq = (int) ($parts[7] ?? 0);
        $steal = (int) ($parts[8] ?? 0);

        $total = $user + $nice + $system + $idle + $iowait + $irq + $softirq + $steal;
        $idleAll = $idle + $iowait;

        return ['idle' => $idleAll, 'total' => $total];
    }

    /**
     * Linux: Đọc tổng RX/TX bytes từ tất cả interface (trừ lo)
     */
    private function readNetStats(): ?array
    {
        $content = @file_get_contents('/proc/net/dev');
        if (! $content) return null;

        $lines = explode("\n", $content);
        $totalRx = 0;
        $totalTx = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (str_contains($line, ':') && ! str_starts_with($line, 'lo:')) {
                $parts = preg_split('/[\s:]+/', $line);
                if (count($parts) >= 10) {
                    $totalRx += (int) $parts[1];
                    $totalTx += (int) $parts[9];
                }
            }
        }

        return ['rx' => $totalRx, 'tx' => $totalTx];
    }
}
