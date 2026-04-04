@extends('layouts.app.app-layout')

@section('content')
    <section class="py-12 px-6">
        <div class="max-w-screen-xl mx-auto">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8">
                <a href="{{ route('app.vps') }}" class="hover:text-primary transition-colors">Cloud VPS</a>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                <span class="text-slate-900 dark:text-white font-medium">{{ $category->name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Cột trái: Thông tin gói VPS --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-border-dark">
                        @if($category->is_best_seller)
                            <span
                                class="inline-flex items-center gap-1 px-3 py-1 bg-amber-500/10 text-amber-500 text-xs font-bold rounded-full mb-4">
                                ⭐ Phổ Biến Nhất
                            </span>
                        @endif

                        <h1 class="text-3xl font-extrabold font-manrope text-slate-900 dark:text-white mb-2">
                            {{ $category->name }}</h1>

                        <div class="flex items-baseline gap-1 mb-6">
                            <span
                                class="text-4xl font-extrabold text-primary">{{ number_format($category->price, 0, ',', '.') }}đ</span>
                            <span class="text-sm font-medium text-slate-500">/ tháng</span>
                        </div>

                        {{-- Thông số kỹ thuật --}}
                        <div class="space-y-4 pb-6 border-b border-slate-200 dark:border-border-dark">
                            @php
                                $specs = [
                                    ['icon' => 'memory', 'label' => 'CPU', 'value' => $category->cpu],
                                    ['icon' => 'database', 'label' => 'RAM', 'value' => $category->ram],
                                    ['icon' => 'storage', 'label' => 'Storage', 'value' => $category->storage],
                                    ['icon' => 'speed', 'label' => 'Bandwidth', 'value' => $category->bandwidth],
                                ];
                                if (!empty($category->metadata['ip'])) {
                                    $specs[] = ['icon' => 'public', 'label' => 'IP', 'value' => $category->metadata['ip']];
                                }
                                if (!empty($category->metadata['firewall'])) {
                                    $specs[] = ['icon' => 'security', 'label' => 'Firewall', 'value' => $category->metadata['firewall']];
                                }
                                if (!empty($category->metadata['backup'])) {
                                    $specs[] = ['icon' => 'backup', 'label' => 'Backup', 'value' => $category->metadata['backup']];
                                }
                            @endphp
                            @foreach($specs as $spec)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-primary text-xl">{{ $spec['icon'] }}</span>
                                        <span class="text-sm text-slate-500">{{ $spec['label'] }}</span>
                                    </div>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $spec['value'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Server Type Hetzner --}}
                        {{-- <div class="pt-4 flex items-center justify-between">
                            <span class="text-xs text-slate-400">Hetzner Server Type</span>
                            <code
                                class="text-xs bg-slate-100 dark:bg-background-dark px-2 py-1 rounded font-mono text-primary">{{ $category->hetzner_server_type }}</code>
                        </div> --}}

                        @if($category->warranty)
                            <div class="mt-3 flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-emerald-500 text-[18px]">verified</span>
                                <span class="text-slate-600 dark:text-slate-400">{{ $category->warranty }}</span>
                            </div>
                        @endif

                        @if($category->description)
                            <p class="mt-4 text-sm text-slate-500 leading-relaxed">{{ $category->description }}</p>
                        @endif
                    </div>
                </div>

                {{-- Cột phải: Form đặt mua --}}
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-border-dark">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">shopping_cart</span>
                            Đặt Mua VPS
                        </h2>

                        <form method="POST" action="{{ route('app.vps.purchase', $category->slug) }}"
                            x-data="vpsPurchaseForm()">
                            @csrf

                            <div class="space-y-6">
                                @php
                                    $sortedOS = $operatingSystems->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
                                    $groupedOS = [];
                                    foreach ($sortedOS as $os) {
                                        $nameLower = strtolower($os->name);
                                        if (str_contains($nameLower, 'ubuntu'))
                                            $flavor = 'Ubuntu';
                                        elseif (str_contains($nameLower, 'debian'))
                                            $flavor = 'Debian';
                                        elseif (str_contains($nameLower, 'centos'))
                                            $flavor = 'CentOS';
                                        elseif (str_contains($nameLower, 'rocky'))
                                            $flavor = 'Rocky Linux';
                                        elseif (str_contains($nameLower, 'alma'))
                                            $flavor = 'AlmaLinux';
                                        elseif (str_contains($nameLower, 'fedora'))
                                            $flavor = 'Fedora';
                                        elseif (str_contains($nameLower, 'suse'))
                                            $flavor = 'openSUSE';
                                        elseif (str_contains($nameLower, 'windows'))
                                            $flavor = 'Windows';
                                        else
                                            $flavor = explode(' ', $os->name)[0];

                                        $groupedOS[$flavor][] = $os;
                                    }
                                    ksort($groupedOS, SORT_NATURAL | SORT_FLAG_CASE);
                                    $firstFlavor = !empty($groupedOS) ? array_key_first($groupedOS) : '';
                                @endphp

                                {{-- Chọn Hệ Điều Hành --}}
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                                        Hệ điều hành <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="hidden" name="operating_system" x-model="selectedOs">

                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                                        @foreach($groupedOS as $flavor => $osList)
                                            <label
                                                class="relative cursor-pointer group flex flex-col bg-white dark:bg-background-dark/50 border rounded-xl transition-all"
                                                :class="selectedFlavor === '{{ $flavor }}' ? 'border-primary ring-1 ring-primary/30 z-10' : 'border-slate-200 dark:border-border-dark hover:border-slate-300 dark:hover:border-slate-600'">
                                                <input type="radio" value="{{ $flavor }}" x-model="selectedFlavor"
                                                    @change="selectedOs = '{{ $osList[0]->hetzner_name }}'" class="sr-only">

                                                <div class="px-4 py-3 flex items-center justify-between border-b border-slate-100 dark:border-border-dark bg-slate-50/50 dark:bg-slate-800/30 rounded-t-xl"
                                                    :class="selectedFlavor === '{{ $flavor }}' ? 'bg-primary/5 dark:bg-primary/10' : ''">
                                                    <div class="flex items-center gap-2">
                                                        {{-- OS Icons giả lập thay thế ảnh base64/svg cho giống hình (Sử dụng
                                                        biểu tượng màu chữ làm fallback tinh gọn) --}}
                                                        <div class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs shadow-sm bg-white dark:bg-slate-900 border"
                                                            :class="selectedFlavor === '{{ $flavor }}' ? 'border-primary text-primary' : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400'">
                                                            {{ substr($flavor, 0, 1) }}
                                                        </div>
                                                        <span
                                                            class="font-bold text-sm text-slate-900 dark:text-white">{{ $flavor }}</span>
                                                    </div>
                                                    <span
                                                        class="material-symbols-outlined text-sm text-slate-400 group-hover:text-amber-500 transition-colors"
                                                        :class="selectedFlavor === '{{ $flavor }}' ? 'text-amber-500' : ''">bolt</span>
                                                </div>

                                                <div class="p-2 relative bg-white dark:bg-surface-dark rounded-b-xl">
                                                    @if(count($osList) > 1)
                                                        <span
                                                            class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-[16px] text-slate-500">expand_more</span>
                                                        <select x-show="selectedFlavor === '{{ $flavor }}'" x-model="selectedOs"
                                                            class="w-full bg-slate-100 dark:bg-slate-800 border-none rounded text-xs font-medium text-slate-700 dark:text-slate-300 py-2 pl-3 pr-8 focus:ring-0 appearance-none cursor-pointer">
                                                            @foreach($osList as $os)
                                                                <option value="{{ $os->hetzner_name }}">
                                                                    {{ trim(str_ireplace([$flavor, 'Linux'], '', $os->name)) ?: $os->name }}
                                                                    ({{ $os->architecture }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div x-show="selectedFlavor !== '{{ $flavor }}'"
                                                            class="text-xs text-center text-slate-500 font-medium py-2 rounded border border-transparent px-2 truncate">
                                                            {{ trim(str_ireplace([$flavor, 'Linux'], '', $osList[0]->name)) ?: $osList[0]->name }}
                                                        </div>
                                                    @else
                                                        <div
                                                            class="text-xs text-center font-medium py-2 rounded focus:ring-0 bg-transparent text-slate-700 dark:text-slate-300">
                                                            {{ trim(str_ireplace([$flavor, 'Linux'], '', $osList[0]->name)) ?: $osList[0]->name }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @if($operatingSystems->isEmpty())
                                        <p class="text-sm text-rose-500">Chưa có HĐH nào. Liên hệ admin.</p>
                                    @endif
                                </div>

                                {{-- Chọn Location --}}
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                                        Vị trí Datacenter <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        @foreach($locations as $location)
                                            <label class="relative cursor-pointer group">
                                                <input type="radio" name="location" value="{{ $location->hetzner_name }}"
                                                    x-model="selectedLocation" class="sr-only peer" {{ $loop->first ? 'checked' : '' }}>
                                                <div
                                                    class="p-3 rounded-xl border-2 border-slate-200 dark:border-border-dark peer-checked:border-primary peer-checked:bg-primary/5 transition-all hover:border-primary/50 text-center">
                                                    <p class="text-sm font-bold text-slate-900 dark:text-white">
                                                        {{ $location->city ?? $location->name }}</p>
                                                    <p class="text-xs text-slate-500 mt-0.5">({{ $location->country }})</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Thời hạn --}}
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                                        Thời hạn sử dụng <span class="text-rose-500">*</span>
                                    </label>
                                    @php
                                        $availableMonths = !empty($category->metadata['available_months']) 
                                            ? $category->metadata['available_months'] 
                                            : [1, 3, 6, 12];
                                        // Đảm bảo là mảng số và sắp xếp
                                        $availableMonths = array_map('intval', $availableMonths);
                                        sort($availableMonths);
                                    @endphp
                                    <div class="grid grid-cols-4 gap-3">
                                        @foreach($availableMonths as $index => $month)
                                            <label class="relative cursor-pointer">
                                                <input type="radio" name="duration_months" value="{{ $month }}" x-model="months"
                                                    class="sr-only peer" {{ $index === 0 ? 'checked' : '' }}>
                                                <div
                                                    class="p-3 rounded-xl border-2 border-slate-200 dark:border-border-dark peer-checked:border-primary peer-checked:bg-primary/5 transition-all hover:border-primary/50 text-center">
                                                    <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $month }}</p>
                                                    <p class="text-xs text-slate-500">tháng</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Phương thức kết nối --}}
                                @php
                                    $connectionMethods = !empty($category->metadata['connection_methods'])
                                        ? $category->metadata['connection_methods']
                                        : ['password'];
                                    $firstConnectionMethod = !empty($connectionMethods) ? $connectionMethods[0] : 'password';
                                @endphp
                                @if(count($connectionMethods) > 0)
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                                        Phương thức kết nối <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        @if(in_array('password', $connectionMethods))
                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="connection_method" value="password" x-model="connectionMethod" class="sr-only peer">
                                            <div class="p-3 rounded-xl border-2 border-slate-200 dark:border-border-dark peer-checked:border-primary peer-checked:bg-primary/5 transition-all hover:border-primary/50 flex flex-col items-center justify-center gap-1">
                                                <span class="material-symbols-outlined text-slate-400 group-hover:text-primary transition-colors" :class="connectionMethod === 'password' ? '!text-primary' : ''">password</span>
                                                <p class="text-sm font-bold text-slate-900 dark:text-white">Mật khẩu</p>
                                            </div>
                                        </label>
                                        @endif
                                        @if(in_array('ssh', $connectionMethods))
                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="connection_method" value="ssh" x-model="connectionMethod" class="sr-only peer">
                                            <div class="p-3 rounded-xl border-2 border-slate-200 dark:border-border-dark peer-checked:border-primary peer-checked:bg-primary/5 transition-all hover:border-primary/50 flex flex-col items-center justify-center gap-1">
                                                <span class="material-symbols-outlined text-slate-400 group-hover:text-primary transition-colors" :class="connectionMethod === 'ssh' ? '!text-primary' : ''">key</span>
                                                <p class="text-sm font-bold text-slate-900 dark:text-white">SSH Key</p>
                                            </div>
                                        </label>
                                        @endif
                                    </div>
                                    
                                    {{-- Form SSH --}}
                                    <div x-show="connectionMethod === 'ssh'" style="display: none;" x-transition class="mt-4 space-y-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-border-dark">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                                Tên SSH Key <span class="text-rose-500">*</span>
                                            </label>
                                            <input type="text" name="ssh_key_name" x-model="sshKeyName" :required="connectionMethod === 'ssh'"
                                                placeholder="Ví dụ: Laptop cá nhân"
                                                class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                                Public Key <span class="text-rose-500">*</span>
                                            </label>
                                            <textarea name="ssh_key_content" x-model="sshKeyContent" :required="connectionMethod === 'ssh'" rows="3"
                                                placeholder="ssh-rsa AAAAB3..."
                                                class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white font-mono"></textarea>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- Gia hạn tự động --}}
                                @if($category->is_renewable)
                                <div>
                                    <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 dark:border-border-dark cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <div class="relative flex items-center">
                                            <input type="checkbox" name="auto_renew" value="1" x-model="autoRenew" class="sr-only peer">
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-900 dark:text-white">Gia hạn tự động</p>
                                            <p class="text-xs text-slate-500">Tự động trừ tiền khi đến hạn để dịch vụ không bị gián đoạn</p>
                                        </div>
                                    </label>
                                </div>
                                @endif

                                {{-- Mã giảm giá --}}
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                        Mã giảm giá (không bắt buộc)
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <input type="text" name="coupon_code" placeholder="Nhập mã giảm giá..."
                                            x-model="couponCode" @keydown.enter.prevent="applyCoupon"
                                            class="flex-1 min-w-0 border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white uppercase transition-colors">
                                        <button type="button" @click="applyCoupon"
                                            class="px-5 py-3 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl transition-colors whitespace-nowrap">
                                            Áp dụng
                                        </button>
                                    </div>
                                    <p x-show="couponMessage" x-text="couponMessage" class="text-sm mt-2"
                                        :class="couponError ? 'text-rose-500' : 'text-emerald-500'" style="display: none;">
                                    </p>
                                </div>

                                {{-- Ghi chú --}}
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                        Ghi chú (không bắt buộc)
                                    </label>
                                    <textarea name="note" rows="2" placeholder="Ghi chú cho đơn hàng..."
                                        class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white resize-none"></textarea>
                                </div>

                                {{-- Tổng tiền + Nút mua --}}
                                <div class="bg-slate-50 dark:bg-background-dark rounded-xl p-5 space-y-3">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-500">Giá gói / tháng</span>
                                        <span
                                            class="text-slate-900 dark:text-white font-medium">{{ number_format($category->price, 0, ',', '.') }}đ</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-500">Thời hạn</span>
                                        <span class="text-slate-900 dark:text-white font-medium"
                                            x-text="months + ' tháng'"></span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm" x-show="couponDiscount > 0"
                                        style="display: none;" x-transition>
                                        <span class="text-emerald-500 font-medium flex items-center gap-1"><span
                                                class="material-symbols-outlined text-[16px]">local_offer</span> Giảm
                                            giá</span>
                                        <span class="text-emerald-500 font-bold"
                                            x-text="'-' + formatVND(couponDiscount)"></span>
                                    </div>
                                    <div
                                        class="border-t border-slate-200 dark:border-border-dark pt-3 flex items-center justify-between">
                                        <span class="text-base font-bold text-slate-900 dark:text-white">Tổng thanh
                                            toán</span>
                                        <span class="text-2xl font-extrabold text-primary"
                                            x-text="formatVND(totalAmount)"></span>
                                    </div>
                                </div>

                                @auth
                                    <button type="submit"
                                        class="w-full py-4 bg-gradient-to-br from-primary to-blue-600 text-white font-bold rounded-xl shadow-xl shadow-primary/30 hover:scale-[0.98] transition-transform text-lg flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined">rocket_launch</span>
                                        Đặt Mua VPS Ngay
                                    </button>
                                    <p class="text-center text-xs text-slate-500">
                                        Số dư hiện tại: <span
                                            class="font-bold text-primary">{{ number_format(auth()->user()->balance, 0, ',', '.') }}đ</span>
                                    </p>
                                @else
                                    <a href="{{ route('login') }}"
                                        class="w-full py-4 bg-gradient-to-br from-primary to-blue-600 text-white font-bold rounded-xl shadow-xl shadow-primary/30 hover:scale-[0.98] transition-transform text-lg flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined">login</span>
                                        Đăng nhập để mua
                                    </a>
                                @endauth
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function vpsPurchaseForm() {
            return {
                selectedFlavor: '{{ $firstFlavor }}',
                selectedOs: '{{ $operatingSystems->first()?->hetzner_name ?? '' }}',
                selectedLocation: '{{ $locations->first()?->hetzner_name ?? '' }}',
                months: {{ !empty($availableMonths) ? $availableMonths[0] : 1 }},
                connectionMethod: '{{ $firstConnectionMethod ?? "password" }}',
                sshKeyName: '',
                sshKeyContent: '',
                autoRenew: true,

                couponCode: '',
                couponDiscount: 0,
                couponMessage: '',
                couponError: false,

                formatVND(amount) {
                    return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
                },

                get totalAmount() {
                    const subtotal = {{ $category->price }} * this.months;
                    return Math.max(0, subtotal - this.couponDiscount);
                },

                async applyCoupon() {
                    if (!this.couponCode) {
                        this.couponMessage = 'Vui lòng nhập mã giảm giá';
                        this.couponError = true;
                        return;
                    }

                    const subtotal = {{ $category->price }} * this.months;
                    const toast = window.Toast || { error: console.error, success: console.log };

                    try {
                        const response = await fetch('{{ route('app.checkout.apply-coupon') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                code: this.couponCode,
                                subtotal: subtotal
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.couponDiscount = data.discount;
                            this.couponMessage = `Đã áp dụng mã: giảm ${this.formatVND(data.discount)}`;
                            this.couponError = false;
                            if (window.Toast) window.Toast.success(data.message);
                        } else {
                            this.couponDiscount = 0;
                            this.couponMessage = data.message || 'Mã giảm giá không hợp lệ.';
                            this.couponError = true;
                            if (window.Toast) window.Toast.error(this.couponMessage);
                        }
                    } catch (error) {
                        this.couponDiscount = 0;
                        this.couponMessage = 'Có lỗi xảy ra, vui lòng thử lại sau.';
                        this.couponError = true;
                    }
                }
            };
        }
    </script>
@endsection