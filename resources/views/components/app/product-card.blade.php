@props(['product'])

<div
    class="group flex flex-col bg-white dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-border-dark overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all duration-300">
    {{-- Product Thumbnail --}}
    <a href="{{ route('app.product.detail', $product->slug) }}"
        class="relative aspect-[25/15] w-full overflow-hidden bg-slate-50 dark:bg-slate-800/50 p-6 flex items-center justify-center">
        @php
            $primaryAsset = $product->assets->first();
            $imageUrl = $primaryAsset ? $primaryAsset->url_or_path : asset('images/placeholder.png');
        @endphp
        <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
            class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-700">

        {{-- Hover Overlay --}}
        <div
            class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
            <span
                class="px-4 py-2 bg-white text-slate-900 rounded-lg text-xs font-bold transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 hover:bg-primary hover:text-white">
                Xem chi tiết
            </span>
        </div>

        @if($product->platform)
            <div
                class="absolute top-3 right-3 bg-white/90 dark:bg-surface-dark/90 backdrop-blur text-[10px] font-bold px-2 py-1 rounded text-slate-900 dark:text-white shadow-sm uppercase">
                {{ $product->platform }}
            </div>
        @endif
    </a>

    {{-- Product Info --}}
    <div class="p-4 flex flex-col flex-1">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-semibold text-primary bg-primary/10 px-2 py-0.5 rounded">
                {{ $product->version ?? 'v1.0' }}
            </span>
            <div class="flex items-center gap-1 text-amber-400 text-xs font-bold">
                <span class="material-symbols-outlined text-[16px] fill-current">star</span>
                <span class="text-slate-500 dark:text-slate-400">
                    {{ number_format($product->reviews_avg_rating ?: 5.0, 1) }}
                    @if($product->reviews_count > 0)
                        ({{ $product->reviews_count }})
                    @endif
                </span>
            </div>
        </div>

        <a href="{{ route('app.product.detail', $product->slug) }}">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1 line-clamp-1 group-hover:text-primary transition-colors"
                title="{{ $product->name }}">
                {{ $product->name }}
            </h3>
        </a>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">
            {{ $product->description }}
        </p>

        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-border-dark flex items-center justify-between">
            <div class="flex flex-col">
                @if($product->sale_price && $product->sale_price < $product->price)
                    <span
                        class="text-[10px] text-slate-400 line-through">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                    <span
                        class="text-sm font-bold text-slate-900 dark:text-white">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                @else
                    <span
                        class="text-sm font-bold text-slate-900 dark:text-white">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                @endif
            </div>
            <div class="flex items-center gap-2" x-data="{
                wishlistLoading: false,
                cartLoading: false,
                isFavorited: {{ auth()->check() && \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists() ? 'true' : 'false' }},
                
                toggleWishlist() {
                    if (this.wishlistLoading) return;
                    this.wishlistLoading = true;
                    
                    fetch('{{ route('app.wishlist.toggle') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ product_id: {{ $product->id }} })
                    })
                    .then(res => res.json().then(data => ({status: res.status, body: data})))
                    .then(res => {
                        if (res.status === 401) {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'warning', title: 'Cảnh báo', message: 'Bạn cần đăng nhập để thao tác.' } }));
                            setTimeout(() => window.location.href = '{{ route('login') }}', 1000);
                            return;
                        }
                        if (res.body.success) {
                            this.isFavorited = res.body.is_favorited;
                            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', title: 'Thành công', message: res.body.message } }));
                        }
                    })
                    .finally(() => this.wishlistLoading = false);
                },

                addToCart() {
                    if (this.cartLoading) return;
                    this.cartLoading = true;
                    
                    fetch('{{ route('app.cart.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ product_id: {{ $product->id }} })
                    })
                    .then(res => res.json().then(data => ({status: res.status, body: data})))
                    .then(res => {
                        if (res.body.success) {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', title: 'Thành công', message: res.body.message } }));
                            window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: res.body.count } }));
                        } else {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', title: 'Lỗi', message: res.body.message } }));
                        }
                    })
                    .catch(() => {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', title: 'Lỗi', message: 'Có lỗi xảy ra, vui lòng thử lại.' } }));
                    })
                    .finally(() => this.cartLoading = false);
                }
            }">
                <button title="Thêm vào yêu thích" @click.prevent="toggleWishlist"
                    class="flex items-center justify-center p-1 transition-colors group/fav"
                    :class="isFavorited ? 'text-primary' : 'text-slate-400 hover:text-primary dark:text-slate-500 dark:hover:text-primary'">
                    <span class="material-symbols-outlined text-[20px] transition-all"
                        :style="isFavorited ? 'font-variation-settings: \'FILL\' 1;' : ''">favorite</span>
                </button>
                <button title="Thêm vào giỏ hàng" @click.prevent="addToCart"
                    class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 hover:bg-primary hover:text-white transition-all text-slate-900 dark:text-white">
                    <span class="material-symbols-outlined text-[18px]" :class="cartLoading ? 'animate-spin' : ''"
                        x-text="cartLoading ? 'progress_activity' : 'add_shopping_cart'"></span>
                </button>
            </div>
        </div>
    </div>
</div>