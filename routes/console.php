<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Hàng ngày lúc 00:00 — vô hiệu hóa gift đã hết hạn
Schedule::command('gift:expire')->daily();

// Mỗi 10 phút — giám sát dung lượng & connections database DBaaS
Schedule::command('dbaas:monitor-activity')->everyTenMinutes();

// Hàng ngày — kiểm tra hết hạn gói Cloud Plan, tạm dừng/xóa resource
Schedule::command('cloud-plan:check-expiry')->daily();

// Hàng ngày lúc 01:00 — kiểm tra VPS hết hạn, xóa server Hetzner
Schedule::command('vps:check-expiration')->dailyAt('01:00');

// Hàng ngày lúc 00:00 — sync HĐH, Location & Server Types từ Hetzner API
Schedule::command('vps:sync-hetzner')->dailyAt('00:00');

