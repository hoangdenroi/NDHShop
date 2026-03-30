<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    /**
     * Upload ảnh lên Cloudinary — trả URL.
     * Dùng cho form tạo/sửa thiệp (async upload).
     */
    public function upload(Request $request, CloudinaryService $cloudinary): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:20480', // Max 20MB (giới hạn Cloudinary Free)
        ]);

        try {
            $result = $cloudinary->upload($request->file('file'), 'gifts');

            return response()->json([
                'success' => true,
                'url'     => $result['url'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }
}
