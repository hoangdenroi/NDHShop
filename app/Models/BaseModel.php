<?php

namespace App\Models;

use App\Models\Traits\HasUnitcode;
use Illuminate\Database\Eloquent\Model;

/**
 * BaseModel — Lớp cơ sở cho tất cả model trong hệ thống.
 *
 * Tự động xử lý:
 * - Tạo unitcode (ULID) khi tạo mới
 * - Scope loại bỏ bản ghi đã xóa mềm (is_deleted)
 * - Helper method softDelete / restore
 *
 * Lưu ý: User kế thừa Authenticatable nên dùng trait HasUnitcode thay vì BaseModel.
 */
abstract class BaseModel extends Model
{
    use HasUnitcode;

    /**
     * Các cột base mặc định — tự động merge vào $fillable của model con.
     */
    protected static array $baseFields = ['unitcode', 'is_deleted'];

    /**
     * Ghi đè getFillable để tự merge baseFields.
     */
    public function getFillable(): array
    {
        return array_unique(array_merge(static::$baseFields, parent::getFillable()));
    }

    /**
     * Boot: mặc định chỉ lấy bản ghi chưa bị xóa mềm.
     */
    protected static function booted(): void
    {
        // Global scope: chỉ lấy bản ghi chưa xóa
        static::addGlobalScope('not_deleted', function ($query) {
            $query->where('is_deleted', false);
        });
    }

    /**
     * Xóa mềm — đánh dấu is_deleted = true.
     */
    public function softDelete(): bool
    {
        $this->is_deleted = true;
        return $this->save();
    }

    /**
     * Khôi phục bản ghi đã xóa mềm.
     */
    public function restore(): bool
    {
        $this->is_deleted = false;
        return $this->save();
    }

    /**
     * Scope: bao gồm cả bản ghi đã xóa.
     */
    public function scopeWithDeleted($query)
    {
        return $query->withoutGlobalScope('not_deleted');
    }

    /**
     * Scope: chỉ lấy bản ghi đã xóa.
     */
    public function scopeOnlyDeleted($query)
    {
        return $query->withoutGlobalScope('not_deleted')->where('is_deleted', true);
    }
}
