<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách Users.
     */
    public function index(Request $request)
    {
        $query = User::where('is_deleted', false);

        // Lọc theo status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Tìm kiếm theo tên hoặc email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        // Thống kê tổng hợp (chỉ tính user chưa bị xóa)
        $stats = [
            'totalUsers'   => User::where('is_deleted', false)->count(),
            'totalBalance' => User::where('is_deleted', false)->sum('balance'),
            'adminCount'   => User::where('is_deleted', false)->where('role', 'admin')->count(),
            'userCount'    => User::where('is_deleted', false)->where('role', 'user')->count(),
            'lockedCount'  => User::where('is_deleted', false)->where('status', 'locked')->count(),
        ];

        return view('pages.admin.users.user-index', compact('users', 'stats'));
    }

    /**
     * Tạo user mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'user',
            'status'   => 'active',
            'balance'  => 0,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Tạo người dùng thành công!');
    }

    /**
     * Cập nhật thông tin user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'status'     => 'required|string|in:active,unactive,locked',
            'role'       => 'required|string|in:admin,user',
            'balance'    => 'nullable|numeric|min:0',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật người dùng thành công!');
    }

    /**
     * Soft delete user (cập nhật is_deleted = true).
     */
    public function destroy(User $user)
    {
        $user->update(['is_deleted' => true]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Đã vô hiệu hoá người dùng thành công!');
    }
}
