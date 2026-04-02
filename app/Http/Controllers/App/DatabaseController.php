<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\CloudDatabase;
use App\Models\ApiKey;

/**
 * DatabaseController — Trang quản lý Cloud Database.
 *
 * Truyền toàn bộ dữ liệu thật (databases, api keys, quota, plan)
 * để các blade partial hiển thị đúng theo gói dịch vụ.
 */
class DatabaseController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Lấy databases của user (không bao gồm đã xóa)
        $databases = CloudDatabase::byUser($user->id)
            ->whereNotIn('status', [CloudDatabase::STATUS_DELETED])
            ->orderBy('created_at', 'desc')
            ->get();

        // Lấy API keys của user
        $apiKeys = ApiKey::byUser($user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Quota & plan info
        $dbQuota     = $user->getDbQuota();
        $currentPlan = $user->cloud_plan ?? 'free';
        $planLabel   = config("cloud_plan.plans.{$currentPlan}.label", 'Free');

        // Thống kê
        $totalDbs  = $databases->count();
        $activeDbs = $databases->where('status', CloudDatabase::STATUS_ACTIVE)->count();
        $totalStorageUsed = $databases->sum('storage_used_mb');

        return view('pages.app.databases.database-index', compact(
            'databases',
            'apiKeys',
            'dbQuota',
            'currentPlan',
            'planLabel',
            'totalDbs',
            'activeDbs',
            'totalStorageUsed',
        ));
    }
}
