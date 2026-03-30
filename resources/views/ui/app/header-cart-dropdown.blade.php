{{-- Icon giỏ hàng + Dropdown (Alpine.js fetch API) --}}
<div class="relative" x-data="{ 
    cartOpen: false, 
    cartCount: 0, 
    cartItems: [], 
    cartTotal: 0,
    removeItem(productId) {
        fetch('{{ route('app.cart.remove') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.count } }));
            }
        });
    }
}" x-init="
        const fetchCart = async () => {
            try {
                const res = await fetch('{{ route('app.cart.count') }}');
                const data = await res.json();
                cartCount = data.count || 0;
                cartItems = data.items || [];
                cartTotal = data.total || 0;
            } catch (e) {}
        };
        fetchCart();
        window.addEventListener('cart-updated', e => { 
            cartCount = e.detail.count;
            fetchCart();
        });
    ">
    <button @click="cartOpen = !cartOpen"
        class="relative flex size-10 cursor-pointer items-center justify-center rounded-full text-slate-900 dark:text-white transition-colors"
        :class="cartOpen ? 'bg-primary text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800'">
        <span class="material-symbols-outlined">shopping_cart</span>
        <span x-show="cartCount > 0" x-text="cartCount" x-cloak
            class="absolute -top-1 -right-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white px-1"></span>
    </button>

    {{-- Dropdown giỏ hàng --}}
    <div x-show="cartOpen" @click.outside="cartOpen = false"
        @keydown.escape.window="cartOpen = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
        class="absolute top-[100%] right-[-44px] sm:right-0 mt-3 w-[calc(100vw-24px)] sm:w-[400px] min-w-[280px] max-w-[400px] bg-white dark:bg-slate-800 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.3)] border border-slate-200 dark:border-slate-700 py-3 z-[200] origin-top-right transform"
        x-cloak>

        {{-- Mũi tên --}}
        <div
            class="absolute -top-[9px] right-[55px] sm:right-[11px] w-0 h-0 border-l-[9px] border-l-transparent border-r-[9px] border-r-transparent border-b-[9px] border-b-slate-200 dark:border-b-slate-700">
        </div>
        <div
            class="absolute -top-[7px] right-[57px] sm:right-[13px] w-0 h-0 border-l-[7px] border-l-transparent border-r-[7px] border-r-transparent border-b-[7px] border-b-white dark:border-b-slate-800">
        </div>

        {{-- Header --}}
        <div
            class="px-4 pb-3 border-b border-slate-100 dark:border-slate-700/50 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 dark:text-white text-[15px]">Giỏ hàng của bạn</h3>
            <span
                class="bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full"><span
                    x-text="cartCount"></span> mục</span>
        </div>

        {{-- Nội dung giỏ hàng (Có sản phẩm) --}}
        <div class="max-h-[60vh] lg:max-h-[400px] overflow-y-auto w-full scrollbar-hide py-2"
            x-show="cartItems.length > 0">
            <template x-for="item in cartItems" :key="item.id">
                <div
                    class="flex gap-3 px-4 py-3 border-b border-slate-100 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                    <img :src="item.image"
                        class="w-12 h-12 object-cover rounded-lg border border-slate-100 dark:border-slate-700 shrink-0"
                        alt="">
                    <div class="flex-1 min-w-0 flex flex-col justify-between pt-0.5">
                        <div class="flex items-start justify-between gap-2">
                            <h4 x-text="item.name"
                                class="text-[13px] font-bold text-slate-900 dark:text-white truncate"
                                :title="item.name"></h4>
                            <button @click.prevent="removeItem(item.product_id)"
                                title="Xoá khỏi giỏ"
                                class="text-slate-400 hover:text-red-500 transition-colors shrink-0">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                            </button>
                        </div>
                        <div class="text-[13px] font-bold text-primary mt-1"
                            x-text="new Intl.NumberFormat('vi-VN').format(item.price) + 'đ'"></div>
                    </div>
                </div>
            </template>

            <div class="px-4 pt-4 pb-2">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Tổng
                        cộng:</span>
                    <span class="text-base font-bold text-primary"
                        x-text="new Intl.NumberFormat('vi-VN').format(cartTotal) + 'đ'"></span>
                </div>
                <div class="flex gap-2">
                    <a href="#"
                        class="flex-1 flex items-center justify-center py-2.5 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-bold transition-all">
                        Xem giỏ hàng
                    </a>
                    <a href="{{ route('app.checkout') }}"
                        class="flex-1 flex items-center justify-center py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-sm font-bold transition-all shadow-sm shadow-primary/20">
                        Thanh toán
                    </a>
                </div>
            </div>
        </div>

        {{-- Nội dung (Rỗng) --}}
        <div x-show="cartItems.length === 0" x-cloak
            class="flex flex-col items-center justify-center py-10 px-4 text-slate-500 dark:text-slate-400">
            <span class="material-symbols-outlined text-[50px] mb-3 opacity-30">shopping_cart</span>
            <p class="text-[15px] font-medium text-slate-600 dark:text-slate-300 mb-1">Giỏ hàng
                trống</p>
            <p class="text-xs text-center mb-4">Bạn chưa chọn sản phẩm nào để thêm vào giỏ.</p>
            <button @click="cartOpen = false"
                class="bg-primary hover:bg-primary/90 text-white text-sm font-semibold py-2 px-6 rounded-lg transition-colors shadow-sm shadow-primary/20">
                Tiếp tục mua sắm
            </button>
        </div>
    </div>
</div>
