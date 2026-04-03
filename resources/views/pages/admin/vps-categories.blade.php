<x-admin-layout title="NDHShop - Admin - Quản lý Gói VPS">
    <div class="flex flex-col gap-6" x-data="vpsCategoryManager()">

        {{-- Thanh công cụ --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white/80 dark:bg-surface-dark border border-slate-200 dark:border-border-dark p-4 rounded-xl backdrop-blur-sm">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Quản lý Gói VPS</h2>
                <p class="text-sm text-slate-500 mt-1">Tạo và quản lý các gói VPS bán cho khách hàng</p>
            </div>
            <button @click="showCreateModal = true"
                class="flex items-center gap-2 px-3 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-primary/25 whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Thêm gói VPS
            </button>
        </div>

        {{-- Bảng danh sách gói VPS --}}
        <div class="rounded-lg border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-background-dark/50 border-b border-slate-200 dark:border-border-dark">
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Gói VPS</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Loại</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Hetzner Type</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Cấu hình</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Giá /tháng</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Đã bán</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-border-dark">
                        @forelse($categories as $category)
                            <tr class="hover:bg-slate-50 dark:hover:bg-background-dark/30 transition-colors">
                                <td class="p-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $category->name }}</span>
                                        @if($category->is_best_seller)
                                            <span class="text-xs text-amber-500 font-medium">⭐ Best Seller</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4">
                                    @if($category->provision_type === 'auto')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-500/10 text-blue-500 border border-blue-500/20">🤖 Tự động</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-500 border border-amber-500/20">✋ Thủ công</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($category->hetzner_server_type)
                                        <code class="text-xs bg-slate-100 dark:bg-background-dark px-2 py-1 rounded text-primary font-mono">{{ $category->hetzner_server_type }}</code>
                                    @else
                                        <span class="text-xs text-slate-400 italic">N/A</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="text-xs text-slate-600 dark:text-slate-400 space-y-0.5">
                                        <div>CPU: <span class="font-medium text-slate-800 dark:text-slate-200">{{ $category->cpu }}</span></div>
                                        <div>RAM: <span class="font-medium text-slate-800 dark:text-slate-200">{{ $category->ram }}</span></div>
                                        <div>SSD: <span class="font-medium text-slate-800 dark:text-slate-200">{{ $category->storage }}</span></div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="text-sm font-bold text-primary">{{ number_format($category->price, 0, ',', '.') }}đ</span>
                                </td>
                                <td class="p-4">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $category->orders_count }}</span>
                                </td>
                                <td class="p-4">
                                    @if($category->status === 'active')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                            <span class="size-1.5 rounded-full bg-emerald-500"></span> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                            <span class="size-1.5 rounded-full bg-slate-500"></span> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="editCategory({{ json_encode($category->load(['operatingSystems', 'locations'])) }})"
                                            class="p-1.5 text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition-colors" title="Sửa">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <form method="POST" action="{{ route('admin.vps-categories.destroy', $category) }}"
                                            onsubmit="return confirm('Xác nhận xóa gói {{ $category->name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded transition-colors" title="Xóa">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600">dns</span>
                                        <p class="text-slate-500 text-sm">Chưa có gói VPS nào. Nhấn "Thêm gói VPS" để tạo.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal tạo/sửa Gói VPS --}}
        <template x-teleport="body">
            <div x-show="showCreateModal || showEditModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                @click.self="closeModals()" style="display: none;">

                <div class="bg-white dark:bg-surface-dark rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-slate-200 dark:border-border-dark">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-border-dark">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white"
                            x-text="showEditModal ? 'Sửa Gói VPS' : 'Thêm Gói VPS'"></h3>
                    </div>

                    <form :action="showEditModal ? `{{ url('admin/vps/categories') }}/${editId}` : '{{ route('admin.vps-categories.store') }}'"
                        method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="_method" value="PUT" x-bind:disabled="!showEditModal">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Tên gói --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tên gói <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" x-model="form.name" required
                                    class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
                                    placeholder="VD: VPS Starter">
                            </div>
                            {{-- Loại provision --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Loại giao hàng <span class="text-rose-500">*</span></label>
                                <select name="provision_type" x-model="form.provision_type"
                                    class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white">
                                    <option value="auto">🤖 Tự động (Hetzner API)</option>
                                    <option value="manual">✋ Thủ công (Admin giao)</option>
                                </select>
                            </div>
                            {{-- Hetzner Server Type (chỉ show khi auto) --}}
                            <div x-show="form.provision_type === 'auto'">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Hetzner Server Type <span class="text-rose-500">*</span></label>
                                <input type="text" name="hetzner_server_type" x-model="form.hetzner_server_type"
                                    :required="form.provision_type === 'auto'"
                                    class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
                                    placeholder="VD: cpx11, cpx21, cpx31">
                            </div>
                            {{-- Giá / tháng --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Giá /tháng (VNĐ) <span class="text-rose-500">*</span></label>
                                <input type="number" name="price" x-model="form.price" required min="1000"
                                    class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white">
                            </div>
                            {{-- Giá /năm --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Giá /năm (VNĐ)</label>
                                <input type="number" name="annual_price" x-model="form.annual_price" min="1000"
                                    class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white">
                            </div>
                            {{-- CPU --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">CPU <span class="text-rose-500">*</span></label>
                                <input type="text" name="cpu" x-model="form.cpu" required
                                    class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
                                    placeholder="VD: 2 vCPU">
                            </div>
                            {{-- RAM --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">RAM <span class="text-rose-500">*</span></label>
                                <input type="text" name="ram" x-model="form.ram" required
                                    class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
                                    placeholder="VD: 2 GB">
                            </div>
                            {{-- Storage --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Storage <span class="text-rose-500">*</span></label>
                                <input type="text" name="storage" x-model="form.storage" required
                                    class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
                                    placeholder="VD: 40 GB NVMe">
                            </div>
                            {{-- Bandwidth --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Bandwidth</label>
                                <input type="text" name="bandwidth" x-model="form.bandwidth"
                                    class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
                                    placeholder="VD: 20 TB">
                            </div>
                        </div>

                        {{-- HĐH hỗ trợ --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Hệ điều hành hỗ trợ <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-40 overflow-y-auto p-2 border border-slate-200 dark:border-border-dark rounded-lg">
                                @foreach($operatingSystems as $os)
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="checkbox" name="operating_system_ids[]" value="{{ $os->id }}"
                                            :checked="form.operating_system_ids.includes({{ $os->id }})"
                                            class="rounded border-slate-300 text-primary focus:ring-primary">
                                        <span class="text-slate-700 dark:text-slate-300 truncate">{{ $os->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Location hỗ trợ --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Vị trí Datacenter <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-2 border border-slate-200 dark:border-border-dark rounded-lg">
                                @foreach($locations as $location)
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="checkbox" name="location_ids[]" value="{{ $location->id }}"
                                            :checked="form.location_ids.includes({{ $location->id }})"
                                            class="rounded border-slate-300 text-primary focus:ring-primary">
                                        <span class="text-slate-700 dark:text-slate-300">{{ $location->name }} ({{ $location->country }})</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Options --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Trạng thái</label>
                                <select name="status" x-model="form.status"
                                    class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Thứ tự</label>
                                <input type="number" name="sort_order" x-model="form.sort_order" min="0"
                                    class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white">
                            </div>
                            <div class="flex flex-col gap-2 pt-6">
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox" name="is_best_seller" x-model="form.is_best_seller" value="1"
                                        class="rounded border-slate-300 text-primary focus:ring-primary">
                                    <span class="text-slate-700 dark:text-slate-300">⭐ Best Seller</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox" name="is_renewable" x-model="form.is_renewable" value="1"
                                        class="rounded border-slate-300 text-primary focus:ring-primary">
                                    <span class="text-slate-700 dark:text-slate-300">Cho phép gia hạn</span>
                                </label>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mô tả</label>
                            <textarea name="description" x-model="form.description" rows="3"
                                class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
                                placeholder="Mô tả chi tiết gói VPS..."></textarea>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex justify-end gap-3 pt-2 border-t border-slate-200 dark:border-border-dark">
                            <button type="button" @click="closeModals()"
                                class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-background-dark rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                                Hủy
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg transition-colors shadow-sm shadow-primary/25">
                                <span x-text="showEditModal ? 'Cập nhật' : 'Tạo gói VPS'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

    <script>
        function vpsCategoryManager() {
            return {
                showCreateModal: false,
                showEditModal: false,
                editId: null,
                form: {
                    name: '', provision_type: 'auto', hetzner_server_type: '', price: '', annual_price: '',
                    cpu: '', ram: '', storage: '', bandwidth: '20 TB',
                    status: 'active', sort_order: 0,
                    is_best_seller: false, is_renewable: true,
                    description: '',
                    operating_system_ids: [], location_ids: [],
                },
                editCategory(category) {
                    this.editId = category.id;
                    this.form.name = category.name;
                    this.form.provision_type = category.provision_type || 'auto';
                    this.form.hetzner_server_type = category.hetzner_server_type || '';
                    this.form.price = category.price;
                    this.form.annual_price = category.annual_price;
                    this.form.cpu = category.cpu;
                    this.form.ram = category.ram;
                    this.form.storage = category.storage;
                    this.form.bandwidth = category.bandwidth;
                    this.form.status = category.status;
                    this.form.sort_order = category.sort_order;
                    this.form.is_best_seller = category.is_best_seller;
                    this.form.is_renewable = category.is_renewable;
                    this.form.description = category.description || '';
                    this.form.operating_system_ids = (category.operating_systems || []).map(os => os.id);
                    this.form.location_ids = (category.locations || []).map(loc => loc.id);
                    this.showEditModal = true;
                },
                closeModals() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.editId = null;
                    this.form = {
                        name: '', provision_type: 'auto', hetzner_server_type: '', price: '', annual_price: '',
                        cpu: '', ram: '', storage: '', bandwidth: '20 TB',
                        status: 'active', sort_order: 0,
                        is_best_seller: false, is_renewable: true,
                        description: '',
                        operating_system_ids: [], location_ids: [],
                    };
                }
            };
        }
    </script>
</x-admin-layout>
