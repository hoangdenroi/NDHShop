<?php

/**
 * DB Servers — Cấu hình kết nối tới server MySQL/PostgreSQL cho DBaaS.
 *
 * Khi mua VPS riêng, chỉ cần thay giá trị trong .env, không sửa code.
 *
 * Ví dụ chuyển sang VPS:
 *   DBAAS_MYSQL_HOST=10.0.0.2
 *   DBAAS_MYSQL_PUBLIC_HOST=mysql.ndhshop.com
 */

return [
    'mysql' => [
        'host'           => env('DBAAS_MYSQL_HOST', '127.0.0.1'),
        'port'           => (int) env('DBAAS_MYSQL_PORT', 3306),
        'admin_user'     => env('DBAAS_MYSQL_ADMIN_USER', 'root'),
        'admin_password' => env('DBAAS_MYSQL_ADMIN_PASSWORD', ''),
        'public_host'    => env('DBAAS_MYSQL_PUBLIC_HOST', '127.0.0.1'),
    ],

    'postgresql' => [
        'host'           => env('DBAAS_PG_HOST', '127.0.0.1'),
        'port'           => (int) env('DBAAS_PG_PORT', 5432),
        'admin_user'     => env('DBAAS_PG_ADMIN_USER', 'postgres'),
        'admin_password' => env('DBAAS_PG_ADMIN_PASSWORD', ''),
        'public_host'    => env('DBAAS_PG_PUBLIC_HOST', '127.0.0.1'),
    ],
];
