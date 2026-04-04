<x-admin-layout title="NDHShop - Admin - Quản lý Gói VPS">
    <div class="flex flex-col gap-6">

        {{-- Thanh công cụ --}}
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white/80 dark:bg-surface-dark border border-slate-200 dark:border-border-dark p-4 rounded-xl backdrop-blur-sm">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Quản lý Gói VPS</h2>
                <p class="text-sm text-slate-500 mt-1">Tạo và quản lý các gói VPS bán cho khách hàng</p>
            </div>
            <button x-data x-on:click="$dispatch('open-create-vps-category')"
                class="flex items-center gap-2 px-3 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-primary/25 whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Thêm gói VPS
            </button>
        </div>

        {{-- Bảng danh sách gói VPS --}}
        <div
            class="rounded-lg border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-background-dark/50 border-b border-slate-200 dark:border-border-dark">
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Gói VPS</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nhóm cấu hình</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Loại</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Hetzner Type</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Cấu hình</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Giá /tháng</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Đã bán</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Hành
                                động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-border-dark">
                        @forelse($categories as $category)
                            <tr class="hover:bg-slate-50 dark:hover:bg-background-dark/30 transition-colors">
                                <td class="p-4">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="text-sm font-bold text-slate-900 dark:text-white">{{ $category->name }}</span>
                                        @if($category->is_best_seller)
                                            <span class="text-xs text-amber-500 font-medium">⭐ Best Seller</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                        @if($category->server_group == 'cost-optimized')
                                            Tối Ưu Chi Phí
                                        @elseif($category->server_group == 'general-purpose')
                                            Đa Dụng
                                        @else
                                            Hiệu Suất Tiêu Chuẩn
                                        @endif
                                    </span>
                                </td>
                                <td class="p-4">
                                    @if($category->provision_type === 'auto')
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-500/10 text-blue-500 border border-blue-500/20">🤖
                                            Tự động</span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-500 border border-amber-500/20">✋
                                            Thủ công</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($category->hetzner_server_type)
                                        <code
                                            class="text-xs bg-slate-100 dark:bg-background-dark px-2 py-1 rounded text-primary font-mono">{{ $category->hetzner_server_type }}</code>
                                    @else
                                        <span class="text-xs text-slate-400 italic">N/A</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="text-xs text-slate-600 dark:text-slate-400 space-y-0.5">
                                        <div>CPU: <span
                                                class="font-medium text-slate-800 dark:text-slate-200">{{ $category->cpu }}</span>
                                        </div>
                                        <div>RAM: <span
                                                class="font-medium text-slate-800 dark:text-slate-200">{{ $category->ram }}</span>
                                        </div>
                                        <div>SSD: <span
                                                class="font-medium text-slate-800 dark:text-slate-200">{{ $category->storage }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span
                                        class="text-sm font-bold text-primary">{{ number_format($category->price, 0, ',', '.') }}đ</span>
                                </td>
                                <td class="p-4">
                                    <span
                                        class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $category->orders_count }}</span>
                                </td>
                                <td class="p-4">
                                    @if($category->status === 'active')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                            <span class="size-1.5 rounded-full bg-emerald-500"></span> Active
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                            <span class="size-1.5 rounded-full bg-slate-500"></span> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button x-data
                                            x-on:click="$dispatch('open-edit-vps-category', {{ json_encode($category->load(['operatingSystems', 'locations'])) }})"
                                            class="p-1.5 text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition-colors"
                                            title="Sửa">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <button x-data
                                            x-on:click="$dispatch('open-delete-vps-category', { id: {{ $category->id }}, slug: '{{ $category->slug }}', name: '{{ addslashes($category->name) }}' })"
                                            class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded transition-colors"
                                            title="Xóa">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span
                                            class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600">dns</span>
                                        <p class="text-slate-500 text-sm">Chưa có gói VPS nào. Nhấn "Thêm gói VPS" để tạo.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modals --}}
        <x-admin.vps-category-crud.modal-create :operatingSystems="$operatingSystems" :locations="$locations" />
        <x-admin.vps-category-crud.modal-edit :operatingSystems="$operatingSystems" :locations="$locations" />
        <x-admin.vps-category-crud.modal-delete />
</x-admin-layout>