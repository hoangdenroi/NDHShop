<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    {{-- Tên gói --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tên gói
            <span class="text-rose-500">*</span></label>
        <input type="text" name="name" x-model="form.name" required
            class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
            placeholder="VD: VPS Starter">
    </div>
    {{-- Nhóm cấu hình --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nhóm
            Cấu Hình <span class="text-rose-500">*</span></label>
        <select name="server_group" x-model="form.server_group"
            class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white">
            <option value="regular">Hiệu Suất Tiêu Chuẩn (Regular Performance)</option>
            <option value="cost-optimized">Tối Ưu Chi Phí (Cost-Optimized)</option>
            <option value="general-purpose">Đa Dụng (General Purpose)</option>
        </select>
    </div>
    {{-- Loại provision --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Loại
            giao hàng <span class="text-rose-500">*</span></label>
        <select name="provision_type" x-model="form.provision_type"
            class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white">
            <option value="auto">🤖 Tự động (Hetzner API)</option>
            <option value="manual">✋ Thủ công (Admin giao)</option>
        </select>
    </div>
    {{-- Hetzner Server Type (chỉ show khi auto) --}}
    <div x-show="form.provision_type === 'auto'">
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Hetzner
            Server Type <span class="text-rose-500">*</span></label>
        <input type="text" name="hetzner_server_type" x-model="form.hetzner_server_type"
            :required="form.provision_type === 'auto'"
            class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
            placeholder="VD: cpx11, cpx21, cpx31">
    </div>
    {{-- Giá / tháng --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Giá
            /tháng (VNĐ) <span class="text-rose-500">*</span></label>
        <input type="number" name="price" x-model="form.price" required min="1000"
            class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white">
    </div>
    {{-- Giá /năm --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Giá
            /năm (VNĐ)</label>
        <input type="number" name="annual_price" x-model="form.annual_price" min="1000"
            class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white">
    </div>
    {{-- CPU --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">CPU
            <span class="text-rose-500">*</span></label>
        <input type="text" name="cpu" x-model="form.cpu" required
            class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
            placeholder="VD: 2 vCPU">
    </div>
    {{-- RAM --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">RAM
            <span class="text-rose-500">*</span></label>
        <input type="text" name="ram" x-model="form.ram" required
            class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
            placeholder="VD: 2 GB">
    </div>
    {{-- Storage --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Storage
            <span class="text-rose-500">*</span></label>
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
    <div class="flex items-center justify-between mb-2">
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Hệ điều
            hành hỗ trợ <span class="text-rose-500">*</span></label>
        <button type="button"
            @click="form.operating_system_ids = form.operating_system_ids.length === {{ $operatingSystems->count() }} ? [] : {{ $operatingSystems->pluck('id')->toJson() }}"
            class="text-xs font-semibold text-primary hover:text-primary/80 transition-colors">
            Chọn / Bỏ chọn tất cả
        </button>
    </div>
    <div
        class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-40 overflow-y-auto p-2 border border-slate-200 dark:border-border-dark rounded-lg">
        @foreach($operatingSystems->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE) as $os)
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="operating_system_ids[]" :value="{{ $os->id }}"
                    x-model="form.operating_system_ids"
                    class="rounded border-slate-300 text-primary focus:ring-primary">
                <span class="text-slate-700 dark:text-slate-300 truncate">{{ $os->name }}</span>
            </label>
        @endforeach
    </div>
</div>

{{-- Location hỗ trợ --}}
<div>
    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Vị trí
        Datacenter <span class="text-rose-500">*</span></label>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-2 border border-slate-200 dark:border-border-dark rounded-lg">
        @foreach($locations as $location)
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="location_ids[]" value="{{ $location->id }}"
                    :checked="form.location_ids.includes({{ $location->id }})"
                    class="rounded border-slate-300 text-primary focus:ring-primary">
                <span class="text-slate-700 dark:text-slate-300">{{ $location->name }}
                    ({{ $location->country }})</span>
            </label>
        @endforeach
    </div>
</div>

{{-- Các tháng cho phép đăng ký --}}
<div class="mt-4">
    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Các tháng cho phép đăng ký <span
            class="text-rose-500">*</span></label>
    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 p-2 border border-slate-200 dark:border-border-dark rounded-lg">
        @foreach([1, 2, 3, 6, 9, 12, 24, 36] as $month)
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="metadata[available_months][]" value="{{ $month }}"
                    :checked="form.metadata.available_months.includes('{{ $month }}')" @change="
                                    if ($event.target.checked) {
                                        if (!form.metadata.available_months.includes('{{ $month }}')) {
                                            form.metadata.available_months.push('{{ $month }}');
                                        }
                                    } else {
                                        form.metadata.available_months = form.metadata.available_months.filter(m => m !== '{{ $month }}');
                                    }
                                " class="rounded border-slate-300 text-primary focus:ring-primary">
                <span class="text-slate-700 dark:text-slate-300">{{ $month }} tháng</span>
            </label>
        @endforeach
    </div>
</div>

{{-- Các phương thức kết nối --}}
<div class="mt-4">
    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Phương thức kết nối hỗ trợ <span class="text-rose-500">*</span></label>
    <div class="flex items-center gap-6 p-2 border border-slate-200 dark:border-border-dark rounded-lg">
        <label class="flex items-center gap-2 text-sm cursor-pointer">
            <input type="checkbox" name="metadata[connection_methods][]" value="password" x-model="form.metadata.connection_methods"
                class="rounded border-slate-300 text-primary focus:ring-primary">
            <span class="text-slate-700 dark:text-slate-300">Password (Tự động gửi qua mail)</span>
        </label>
        <label class="flex items-center gap-2 text-sm cursor-pointer">
            <input type="checkbox" name="metadata[connection_methods][]" value="ssh" x-model="form.metadata.connection_methods"
                class="rounded border-slate-300 text-primary focus:ring-primary">
            <span class="text-slate-700 dark:text-slate-300">SSH Keys</span>
        </label>
    </div>
</div>

{{-- Các thông tin khác --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">IP</label>
        <input type="text" name="metadata[ip]" x-model="form.metadata.ip"
            class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
            placeholder="VD: 1 IPv4 Public, 1 IPv6 Public" value="1 IPv4 Public, 1 IPv6 Public">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Firewall</label>
        <input type="text" name="metadata[firewall]" x-model="form.metadata.firewall"
            class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
            placeholder="VD: DDoS Protection" value="DDoS Protection">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Backup</label>
        <input type="text" name="metadata[backup]" x-model="form.metadata.backup"
            class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
            placeholder="VD: Có (Tùy chọn)" value="Có (Tùy chọn)">
    </div>
</div>

{{-- Options --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Trạng
            thái</label>
        <select name="status" x-model="form.status"
            class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Thứ
            tự</label>
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
    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mô
        tả</label>
    <textarea name="description" x-model="form.description" rows="3"
        class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
        placeholder="Mô tả chi tiết gói VPS..."></textarea>
</div>