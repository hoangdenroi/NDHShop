<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GiftAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MusicController extends Controller
{
    /**
     * Lấy danh sách nhạc (type = audio) có cache.
     * Hỗ trợ tìm kiếm phía client qua query param ?q=
     */
    public function index(Request $request)
    {
        // Cache toàn bộ danh sách nhạc trong 30 phút
        $tracks = Cache::remember('music_tracks', 60 * 30, function () {
            return GiftAsset::active()
                ->byType('audio')
                ->ordered()
                ->select('id', 'name', 'url', 'thumbnail', 'file_size', 'description')
                ->get();
        });

        // Nếu có query tìm kiếm, lọc phía server (không ảnh hưởng cache)
        $query = $request->input('q');
        if ($query) {
            $query = mb_strtolower(trim($query));
            $tracks = $tracks->filter(function ($track) use ($query) {
                return str_contains(mb_strtolower($track->name), $query);
            })->values();
        }

        return response()->json([
            'success' => true,
            'tracks'  => $tracks,
            'total'   => $tracks->count(),
        ]);
    }
}
