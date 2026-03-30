<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;

/**
 * CloudinaryService — Service upload file lên Cloudinary.
 *
 * Dùng được ở bất kỳ controller/command nào:
 *   app(CloudinaryService::class)->upload($file, 'gifts');
 *
 * Tự chống trùng lặp bằng hash MD5 file → cùng file = cùng public_id.
 */
class CloudinaryService
{
    /**
     * Folder gốc trên Cloudinary.
     */
    private const ROOT_FOLDER = 'NDHShop';

    /**
     * Upload file lên Cloudinary.
     * Nếu file đã tồn tại (cùng hash) → trả URL cũ, không upload lại.
     *
     * @param UploadedFile $file      File cần upload
     * @param string       $subfolder Subfolder trong NDHShop/ (vd: 'gifts', 'products')
     * @param array        $options   Tùy chọn bổ sung cho Cloudinary
     * @return array{url: string, public_id: string, existing: bool}
     */
    public function upload(UploadedFile $file, string $subfolder = '', array $options = []): array
    {
        $folder = self::ROOT_FOLDER;
        if ($subfolder) {
            $folder .= '/' . trim($subfolder, '/');
        }

        // Hash MD5 file → dùng làm public_id để chống trùng lặp
        $fileHash = md5_file($file->getRealPath());
        $publicId = $folder . '/' . $fileHash;

        // Kiểm tra file đã tồn tại trên Cloudinary chưa
        try {
            $existing = Cloudinary::adminApi()->asset($publicId);
            if (!empty($existing['secure_url'])) {
                return [
                    'url'       => $existing['secure_url'],
                    'public_id' => $existing['public_id'],
                    'existing'  => true,
                ];
            }
        } catch (\Exception $e) {
            // File chưa tồn tại → upload mới (bình thường)
        }

        // Upload mới với public_id = hash để lần sau nhận ra
        // Thêm transformation: auto quality + giới hạn kích thước để tối ưu dung lượng
        $result = Cloudinary::uploadApi()->upload($file->getRealPath(), array_merge([
            'folder'          => $folder,
            'public_id'       => $fileHash,
            'unique_filename' => false,
            'overwrite'       => false,
            'resource_type'   => 'auto',
            'transformation'  => [
                'quality'    => 'auto',       // Cloudinary tự chọn quality tối ưu
                'width'      => 2000,         // Giới hạn chiều rộng tối đa 2000px
                'height'     => 2000,         // Giới hạn chiều cao tối đa 2000px
                'crop'       => 'limit',      // Chỉ resize nếu ảnh lớn hơn, không phóng to
            ],
        ], $options));

        return [
            'url'       => $result['secure_url'],
            'public_id' => $result['public_id'],
            'existing'  => false,
        ];
    }

    /**
     * Xóa file trên Cloudinary theo public_id.
     */
    public function delete(string $publicId): bool
    {
        $result = Cloudinary::uploadApi()->destroy($publicId);
        return ($result['result'] ?? '') === 'ok';
    }
}
