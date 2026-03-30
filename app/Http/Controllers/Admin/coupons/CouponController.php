<?php

namespace App\Http\Controllers\Admin\coupons;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Coupon::query();

        // Tìm kiếm theo code hoặc giá trị
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('code', 'like', "%{$search}%")
                  ->orWhere('value', 'like', "%{$search}%");
        }
        
        // Lọc theo Status (is_active)
        if ($request->filled('status')) {
            if ($request->status === '1') {
                $query->where('is_active', true);
            } elseif ($request->status === '0') {
                $query->where('is_active', false);
            }
        }

        $coupons = $query->latest()->paginate(10)->withQueryString();

        return view('pages.admin.coupons.index', compact('coupons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'min_order' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->has('is_active');

        Coupon::create($validated);

        return back()->with('success', 'Thêm mã giảm giá thành công!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'min_order' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        return back()->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        // Kiểm tra xem đã được dùng chưa, nếu rồi mà muốn xóa thì vẫn xóa được hoặc báo lỗi
        // Theo yêu cầu "quản lý cơ bản", cứ cho xóa bình thường (hoặc bạn có thể dùng soft delete sau)
        if ($coupon->used_count > 0) {
            return back()->with('error', 'Không thể xóa mã giảm giá đã được sử dụng!');
        }

        $coupon->delete();

        return back()->with('success', 'Xóa mã giảm giá thành công!');
    }
}
