<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Hàng ngày lúc 00:00 — vô hiệu hóa gift đã hết hạn
Schedule::command('gift:expire')->daily();
