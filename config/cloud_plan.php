<?php

/**
 * Cloud Plan — Cấu hình gói dịch vụ chung cho DBaaS + Storage.
 *
 * Gộp pricing Database và Storage thành 1 Cloud Plan duy nhất.
 * User nâng cấp → thanh toán qua số dư → mở khóa quota cho cả 2 dịch vụ.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Chu kỳ thanh toán & chiết khấu
    |--------------------------------------------------------------------------
    */
    'billing_cycles' => [
        'monthly'    => ['label' => '1 tháng',  'months' => 1,  'discount' => 0],
        'quarterly'  => ['label' => '3 tháng',  'months' => 3,  'discount' => 5],
        'semiannual' => ['label' => '6 tháng',  'months' => 6,  'discount' => 10],
        'annual'     => ['label' => '1 năm',    'months' => 12, 'discount' => 20],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tỉ lệ hoàn tiền khi downgrade giữa chừng (%)
    |--------------------------------------------------------------------------
    | Hoàn 70% giá trị còn lại khi user hạ gói trước khi hết hạn.
    */
    'refund_rate' => 70,

    /*
    |--------------------------------------------------------------------------
    | Grace period — Thời gian ân hạn sau hết hạn (ngày)
    |--------------------------------------------------------------------------
    | Sau khi hết hạn, resource bị tạm dừng. Hết grace period → xóa vĩnh viễn.
    */
    'grace_period_days' => 7,

    /*
    |--------------------------------------------------------------------------
    | Nhắc gia hạn trước khi hết hạn (ngày)
    |--------------------------------------------------------------------------
    */
    'renewal_reminder_days' => 3,

    /*
    |--------------------------------------------------------------------------
    | Cleanup resource không hoạt động — chỉ áp dụng gói Free
    |--------------------------------------------------------------------------
    */
    'inactive_warning_days' => 30, // Cảnh báo sau 30 ngày không hoạt động
    'inactive_delete_days'  => 37, // Xóa sau 37 ngày (30 + 7 ngày chờ)

    /*
    |--------------------------------------------------------------------------
    | Gói dịch vụ (Plans)
    |--------------------------------------------------------------------------
    | Giá tính theo VNĐ/tháng. Chiết khấu áp dụng khi mua nhiều tháng.
    */
    'plans' => [
        'free' => [
            'price'             => 0,
            'label'             => 'Free',
            // Database
            'max_databases'     => 1,
            'max_db_storage_mb' => 50,
            'max_connections'   => 3,
            'engines'           => ['mysql'],
            'backup'            => null,
            // Storage (Phase 2)
            'max_buckets'       => 2,
            'max_storage_mb'    => 200,
            'max_file_size_mb'  => 5,
            'cdn_enabled'       => false,
            // Chung
            'max_api_keys'      => 1,
        ],

        'pro' => [
            'price'             => 99000,
            'label'             => 'Pro',
            // Database
            'max_databases'     => 5,
            'max_db_storage_mb' => 500,
            'max_connections'   => 20,
            'engines'           => ['mysql', 'postgresql'],
            'backup'            => 'weekly',
            // Storage (Phase 2)
            'max_buckets'       => 10,
            'max_storage_mb'    => 5120,
            'max_file_size_mb'  => 100,
            'cdn_enabled'       => true,
            // Chung
            'max_api_keys'      => 3,
        ],

        'max' => [
            'price'             => 399000,
            'label'             => 'Max',
            // Database
            'max_databases'     => 15,
            'max_db_storage_mb' => 3072,
            'max_connections'   => 50,
            'engines'           => ['mysql', 'postgresql'],
            'backup'            => 'daily',
            // Storage (Phase 2)
            'max_buckets'       => 30,
            'max_storage_mb'    => 30720,
            'max_file_size_mb'  => 500,
            'cdn_enabled'       => true,
            // Chung
            'max_api_keys'      => 10,
        ],
    ],
];
