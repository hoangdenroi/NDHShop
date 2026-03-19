<x-admin-layout title="NDHShop - Admin - Người dùng">
    <div class="flex flex-col gap-6">

        {{-- Thẻ thống kê --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Tổng thành viên --}}
            <div class="rounded-xl p-4 text-white shadow-lg"
                style="background: linear-gradient(135deg, #f59e0b, #ea580c);">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg" style="background: rgba(255,255,255,0.2);">
                        <span class="material-symbols-outlined text-[24px]">group</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: rgba(255,255,255,0.8);">Tổng thành viên</p>
                        <p class="text-xl font-bold text-white">{{ number_format($stats['totalUsers']) }} thành viên</p>
                    </div>
                </div>
            </div>
            {{-- Số dư thành viên --}}
            <div class="rounded-xl p-4 text-white shadow-lg"
                style="background: linear-gradient(135deg, #10b981, #16a34a);">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg" style="background: rgba(255,255,255,0.2);">
                        <span class="material-symbols-outlined text-[24px]">account_balance_wallet</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: rgba(255,255,255,0.8);">Số dư thành viên</p>
                        <p class="text-xl font-bold text-white">
                            {{ number_format($stats['totalBalance'], 0, ',', '.') }}đ
                        </p>
                    </div>
                </div>
            </div>
            {{-- Staff --}}
            <div class="rounded-xl p-4 text-white shadow-lg"
                style="background: linear-gradient(135deg, #06b6d4, #0d9488);">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg" style="background: rgba(255,255,255,0.2);">
                        <span class="material-symbols-outlined text-[24px]">badge</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: rgba(255,255,255,0.8);">Staff</p>
                        <p class="text-xl font-bold text-white">Admin: {{ $stats['adminCount'] }} / User:
                            {{ $stats['userCount'] }}
                        </p>
                    </div>
                </div>
            </div>
            {{-- Tài khoản bị khóa --}}
            <div class="rounded-xl p-4 text-white shadow-lg"
                style="background: linear-gradient(135deg, #ec4899, #e11d48);">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg" style="background: rgba(255,255,255,0.2);">
                        <span class="material-symbols-outlined text-[24px]">lock</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: rgba(255,255,255,0.8);">Tài khoản bị vô hiệu hoá
                        </p>
                        <p class="text-xl font-bold text-white">{{ number_format($stats['lockedCount']) }} tài khoản</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Thanh lọc & công cụ --}}
        <div
            class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 bg-white/80 dark:bg-surface-dark border border-slate-200 dark:border-border-dark p-4 rounded-xl backdrop-blur-sm">
            <div class="flex flex-col sm:flex-row flex-wrap items-center gap-2 w-full xl:w-auto flex-1">
                {{-- Lọc theo Status --}}
                <div class="relative flex-1 sm:flex-none w-full sm:w-44">
                    <span
                        class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">filter_list</span>
                    <select id="filterStatus"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-9 pr-8 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer appearance-none transition-colors"
                        onchange="applyFilters()">
                        <option value="" {{ request('status') == '' ? 'selected' : '' }}>Tất cả</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="unactive" {{ request('status') == 'unactive' ? 'selected' : '' }}>Chưa kích hoạt
                        </option>
                        <option value="locked" {{ request('status') == 'locked' ? 'selected' : '' }}>Khóa</option>
                    </select>
                    <span
                        class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                </div>
                {{-- Lọc theo Role --}}
                <div class="relative flex-1 sm:flex-none w-full sm:w-44">
                    <span
                        class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">card_membership</span>
                    <select id="filterRole"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-9 pr-8 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer appearance-none transition-colors"
                        onchange="applyFilters()">
                        <option value="" {{ request('role') == '' ? 'selected' : '' }}>Tất cả</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Người dùng</option>
                    </select>
                    <span
                        class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                </div>
                {{-- search --}}
                <div class="relative flex-1 w-full sm:min-w-[200px]">
                    <span
                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">search</span>
                    <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên, email..."
                        value="{{ request('search') }}"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-10 pr-4 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                        oninput="debounceSearch()">
                </div>
            </div>

            {{-- Thêm nút xuất dữ liệu --}}
            <div class="flex items-center gap-2 shrink-0">
                <button x-data x-on:click="$dispatch('open-modal', 'create-user')"
                    class="flex items-center gap-2 px-3 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-primary/25 whitespace-nowrap">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Thêm mới
                </button>
                <button
                    class="flex items-center gap-2 px-3 py-2 bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 text-sm font-medium rounded-lg transition-colors whitespace-nowrap">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Xuất dữ liệu
                </button>
            </div>
        </div>

        {{-- Bảng danh sách Users --}}
        <div
            class="rounded-lg border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark overflow-hidden flex flex-col">
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-background-dark/50 border-b border-slate-200 dark:border-border-dark">
                            <th class="p-4 w-12">
                                <input
                                    class="checkbox-custom rounded bg-slate-100 dark:bg-background-dark border-slate-300 dark:border-border-dark text-primary focus:ring-0 w-4 h-4 cursor-pointer"
                                    type="checkbox" />
                            </th>
                            <th
                                class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                STT</th>
                            <th
                                class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Tên</th>
                            <th
                                class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Email</th>
                            <th
                                class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Trạng thái</th>
                            <th
                                class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Vai trò</th>
                            <th
                                class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Số dư</th>
                            <th
                                class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Cuối lần đăng nhập</th>
                            <th
                                class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">
                                Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-border-dark">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50 dark:hover:bg-background-dark/30 transition-colors group">
                                <td class="p-4">
                                    <input
                                        class="checkbox-custom rounded bg-slate-100 dark:bg-background-dark border-slate-300 dark:border-border-dark text-primary focus:ring-0 w-4 h-4 cursor-pointer"
                                        type="checkbox" value="{{ $user->id }}" />
                                </td>
                                <td class="p-4 text-center">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        @if($user->avatar_url)
                                            <div class="size-9 rounded-full bg-slate-200 dark:bg-slate-700 bg-center bg-cover border border-slate-200 dark:border-border-dark"
                                                style="background-image: url('{{ $user->avatar_url }}');">
                                            </div>
                                        @else
                                            {{-- Hiển thị chữ cái đầu nếu không có avatar --}}
                                            <div
                                                class="size-9 rounded-full bg-slate-200 dark:bg-slate-700 border border-slate-200 dark:border-border-dark flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-300">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-slate-900 dark:text-white text-sm font-medium">{{ $user->name }}
                                            </p>
                                            <p class="text-slate-500 text-xs">{{ '#' . $user->unitcode }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-sm text-slate-600 dark:text-slate-400">{{ $user->email }}</td>
                                <td class="p-4">
                                    @php
                                        $statusConfig = match ($user->status) {
                                            'active' => [
                                                'badge' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                                'dot' => 'bg-emerald-500',
                                                'label' => 'Hoạt động',
                                            ],
                                            'inactive' => [
                                                'badge' => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                                                'dot' => 'bg-slate-500',
                                                'label' => 'Không hoạt động',
                                            ],
                                            'locked' => [
                                                'badge' => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
                                                'dot' => 'bg-rose-500',
                                                'label' => 'Khóa',
                                            ],
                                            default => [
                                                'badge' => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                                                'dot' => 'bg-slate-500',
                                                'label' => ucfirst($user->status ?? 'Không xác định'),
                                            ],
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusConfig['badge'] }} border">
                                        <span class="size-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        @if($user->role === 'admin')
                                            <span
                                                class="material-symbols-outlined text-purple-400 text-[18px]">shield_person</span>
                                            <span class="text-sm text-slate-900 dark:text-white">Admin</span>
                                        @else
                                            <span class="material-symbols-outlined text-blue-400 text-[18px]">person</span>
                                            <span class="text-sm text-slate-900 dark:text-white">Người dùng</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ number_format($user->balance, 0, ',', '.') }} VND
                                </td>
                                <td class="p-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Chưa đăng nhập' }}
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button x-data
                                            x-on:click="$dispatch('open-edit-user', {{ json_encode(['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone, 'gender' => $user->gender ?? 'other', 'address' => $user->address ?? '', 'status' => $user->status, 'role' => $user->role, 'balance' => $user->balance, 'avatar_url' => $user->avatar_url]) }})"
                                            class="p-1.5 text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition-colors"
                                            title="Sửa">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <button x-data
                                            x-on:click="$dispatch('open-delete-user', {{ json_encode(['id' => $user->id, 'name' => $user->name]) }})"
                                            class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded transition-colors"
                                            title="Xóa">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span
                                            class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600">group_off</span>
                                        <p class="text-slate-500 text-sm">Không tìm thấy user nào.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($users->hasPages())
                <div class="flex flex-col sm:flex-row items-center justify-between p-4 border-t border-slate-200 dark:border-border-dark bg-slate-50 dark:bg-background-dark/30 gap-4">
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        Hiển thị <span class="font-bold text-slate-900 dark:text-white">{{ $users->firstItem() }}</span>
                        đến
                        <span class="font-bold text-slate-900 dark:text-white">{{ $users->lastItem() }}</span> trong <span
                            class="font-bold text-slate-900 dark:text-white">{{ $users->total() }}</span> người dùng
                    </div>
                    <div>
                        {{ $users->links('pagination::tailwind') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Script xử lý filter --}}
    <script>
        let searchTimeout = null;

        // Giữ focus và đưa con trỏ về cuối do mình reload trang thủ công
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            if (searchInput && searchInput.value) {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }
        });

        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        }

        function applyFilters() {
            const status = document.getElementById('filterStatus').value;
            const role = document.getElementById('filterRole').value;
            const search = document.getElementById('searchInput') ? document.getElementById('searchInput').value : '';
            const params = new URLSearchParams(window.location.search);

            // Cập nhật status
            if (status) {
                params.set('status', status);
            } else {
                params.delete('status');
            }

            // Cập nhật role
            if (role) {
                params.set('role', role);
            } else {
                params.delete('role');
            }

            // Cập nhật search
            if (search) {
                params.set('search', search);
            } else {
                params.delete('search');
            }

            // Reset về trang 1 khi filter
            params.delete('page');

            window.location.href = '{{ route("admin.users.index") }}?' + params.toString();
        }
    </script>

    {{-- Modal CRUD --}}
    <x-admin.user-crud.modal-create />
    <x-admin.user-crud.modal-edit />
    <x-admin.user-crud.modal-delete />
</x-admin-layout>