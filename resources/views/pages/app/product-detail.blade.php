@extends('layouts.app.app-layout')

@section('title', $product->name . ' - NDHShop')
@section('description', \Illuminate\Support\Str::limit(strip_tags($product->description), 150))

@section('content')
    <main class="mx-auto max-w-[1280px] px-6 py-8" x-data="{
                                        mainImage: '{{ $product->assets->first() ? $product->assets->first()->url_or_path : asset('images/placeholder.png') }}',
                                        isFullscreen: false,
                                        isFavorited: {{ Auth::check() && Auth::user()->wishlists()->where('product_id', $product->id)->exists() ? 'true' : 'false' }},
                                        cartLoading: false,

                                        toggleWishlist() {
                                            @if(!Auth::check())
                                                window.dispatchEvent(new CustomEvent('toast', {
                                                    detail: { type: 'warning', title: 'Cảnh báo', message: 'Bạn cần đăng nhập để thêm vào danh sách yêu thích.' }
                                                }));
                                                setTimeout(() => window.location.href = '{{ route('login') }}', 1000);
                                                return;
                                            @endif

                                            fetch('{{ route('app.wishlist.toggle') }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                    'Accept': 'application/json'
                                                },
                                                body: JSON.stringify({ product_id: {{ $product->id }} })
                                            })
                                            .then(res => {
                                                if (res.status === 401) {
                                                    window.location.href = '{{ route('login') }}';
                                                    throw new Error('Unauthorized');
                                                }
                                                return res.json();
                                            })
                                            .then(data => {
                                                if(data.success) {
                                                    this.isFavorited = data.is_favorited;
                                                    window.dispatchEvent(new CustomEvent('toast', {
                                                        detail: { type: 'success', title: 'Thành công', message: data.message }
                                                    }));
                                                } else {
                                                    window.dispatchEvent(new CustomEvent('toast', {
                                                        detail: { type: 'error', title: 'Lỗi', message: data.message || 'Có lỗi xảy ra' }
                                                    }));
                                                }
                                            })
                                            .catch(err => console.error(err));
                                        },

                                        addToCart() {
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
                                                this.cartLoading = false;
                                                if(res.body.success) {
                                                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: res.body.count } }));
                                                    window.dispatchEvent(new CustomEvent('toast', {
                                                        detail: { type: 'success', title: 'Thành công', message: res.body.message }
                                                    }));
                                                } else {
                                                    window.dispatchEvent(new CustomEvent('toast', {
                                                        detail: { type: 'error', title: 'Lỗi', message: res.body.message || 'Thêm vào giỏ hàng thất bại' }
                                                    }));
                                                }
                                            })
                                            .catch(err => {
                                                this.cartLoading = false;
                                                window.dispatchEvent(new CustomEvent('toast', {
                                                    detail: { type: 'error', title: 'Lỗi', message: 'Đã xảy ra lỗi hệ thống' }
                                                }));
                                            });
                                        }
                                    }">
        <!-- Breadcrumb -->
        <nav class="mb-6 flex items-center text-sm font-medium text-slate-500 dark:text-slate-400">
            <a class="hover:text-primary transition-colors" href="{{ route('app.src-app-game') }}">Sản phẩm</a>
            <span class="mx-2 text-slate-300 dark:text-slate-600">/</span>
            <span
                class="hover:text-primary transition-colors cursor-pointer">{{ $product->category->name ?? 'Chưa phân loại' }}</span>
            <span class="mx-2 text-slate-300 dark:text-slate-600">/</span>
            <span class="text-slate-900 dark:text-white line-clamp-1">{{ $product->name }}</span>
        </nav>

        <!-- Product Hero Section -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-12">
            <!-- Left: Gallery -->
            <div class="lg:col-span-8 space-y-4">
                {{-- Main Image Viewer --}}
                <div class="group relative aspect-video w-full overflow-hidden rounded-xl bg-slate-100 shadow-sm dark:bg-slate-800 cursor-zoom-in"
                    @click="isFullscreen = true">
                    <img :src="mainImage"
                        class="h-full w-full object-contain transition-transform duration-500 group-hover:scale-[1.02]"
                        alt="{{ $product->name }}">
                    <div class="absolute bottom-4 right-4 flex gap-2">
                        <button
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-slate-900 backdrop-blur-sm shadow-sm hover:bg-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">fullscreen</span>
                        </button>
                    </div>
                </div>

                {{-- Thumbnails Gallery --}}
                @if($product->assets->count() > 0)
                    <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide py-2">
                        @foreach($product->assets as $asset)
                            <button
                                class="relative h-20 w-36 flex-shrink-0 overflow-hidden rounded-lg border-2 transition-all p-0.5"
                                :class="mainImage === '{{ $asset->url_or_path }}' ? 'border-primary' : 'border-transparent bg-slate-100 hover:border-slate-300 dark:bg-slate-800'"
                                @click="mainImage = '{{ $asset->url_or_path }}'">
                                <img src="{{ $asset->url_or_path }}" class="h-full w-full rounded-[4px] object-cover"
                                    alt="Thumbnail">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right: Product Info -->
            <div class="lg:col-span-4">
                <div
                    class="flex flex-col gap-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5 dark:bg-slate-900 dark:ring-slate-800">
                    <div>
                        <div class="mb-2 flex items-center gap-2">
                            @if($product->platform)
                                <span
                                    class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">{{ $product->platform }}</span>
                            @endif
                            <span
                                class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-300">Đã
                                xác minh</span>
                        </div>
                        <h1 class="text-3xl font-bold leading-tight text-slate-900 dark:text-white">{{ $product->name }}
                        </h1>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                            Cre: <span class="font-medium text-primary">{{ $product->developer ?? 'Người nặc danh' }}</span>
                            @if($product->version)
                                <span class="mx-1">•</span> v{{ $product->version }}
                            @endif
                        </p>
                    </div>

                    <div class="flex items-baseline gap-2 border-b border-slate-100 pb-6 dark:border-slate-800">
                        @if($product->sale_price && $product->sale_price < $product->price)
                            <span
                                class="text-4xl font-extrabold text-slate-900 dark:text-white">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                            <span
                                class="text-lg text-slate-400 line-through decoration-slate-400/50">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                            @php $discount = round((($product->price - $product->sale_price) / $product->price) * 100); @endphp
                            <span class="ml-auto text-sm font-medium text-green-600 dark:text-green-400">Giảm
                                {{ $discount }}%</span>
                        @else
                            <span
                                class="text-4xl font-extrabold text-slate-900 dark:text-white">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                        @endif
                    </div>

                    <div class="space-y-3">
                        <button @click.prevent="addToCart" :disabled="cartLoading"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary py-3.5 text-base font-bold text-white shadow-lg shadow-primary/25 hover:bg-primary/90 active:scale-[0.98] transition-all disabled:opacity-75">
                            <span class="material-symbols-outlined" :class="cartLoading ? 'animate-spin' : ''"
                                x-text="cartLoading ? 'progress_activity' : 'shopping_cart'"></span>
                            Thêm vào giỏ
                        </button>
                        <button @click.prevent="toggleWishlist"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-100 py-3.5 text-base font-bold transition-all"
                            :class="isFavorited ? 'text-primary bg-primary/10' : 'text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700'">
                            <span class="material-symbols-outlined"
                                :style="isFavorited ? 'font-variation-settings: \'FILL\' 1;' : ''">favorite</span>
                            <span x-text="isFavorited ? 'Đã thêm Yêu thích' : 'Thêm vào yêu thích'"></span>
                        </button>
                    </div>

                    <div class="space-y-4 pt-2">
                        <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-300">
                            <span class="material-symbols-outlined text-slate-400">update</span>
                            <span>Cập nhật lúc: {{ $product->updated_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-300">
                            <span class="material-symbols-outlined text-slate-400">download</span>
                            <span>Tải về nhanh chóng</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Section & Related -->
        <div class="mt-12 grid grid-cols-1 gap-12 lg:grid-cols-12 items-start">
            <div class="lg:col-span-8 space-y-12">
                <!-- Tabs -->
                <section class="space-y-6" x-data="{ activeTab: 'description' }">
                    <div class="flex gap-8 border-b border-slate-200 dark:border-slate-800">
                        <button class="pb-3 text-lg font-bold transition-colors relative"
                            :class="activeTab === 'description' ? 'text-primary' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                            @click="activeTab = 'description'">
                            Giới thiệu sản phẩm
                            <span
                                class="absolute bottom-0 left-0 w-full h-0.5 bg-primary rounded-t-lg transition-transform origin-left"
                                :class="activeTab === 'description' ? 'scale-x-100' : 'scale-x-0'"></span>
                        </button>
                        <button class="pb-3 text-lg font-bold transition-colors relative"
                            :class="activeTab === 'reviews' ? 'text-primary' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                            @click="activeTab = 'reviews'">
                            Đánh giá
                            <span
                                class="absolute bottom-0 left-0 w-full h-0.5 bg-primary rounded-t-lg transition-transform origin-left"
                                :class="activeTab === 'reviews' ? 'scale-x-100' : 'scale-x-0'"></span>
                        </button>
                    </div>

                    <!-- Tab Content: Description -->
                    <div x-show="activeTab === 'description'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0">
                        <div
                            class="prose prose-slate max-w-none text-slate-600 dark:prose-invert dark:text-slate-400 break-words overflow-hidden">
                            {!! $product->description !!}
                        </div>
                    </div>

                    <!-- Tab Content: Reviews -->
                    <div x-show="activeTab === 'reviews'" x-cloak x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0">
                        <div
                            class="flex flex-col items-center justify-center py-12 text-slate-500 text-center rounded-xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/20">
                            <span class="material-symbols-outlined text-4xl mb-3 opacity-30">rate_review</span>
                            <p class="font-medium text-slate-700 dark:text-slate-300">Chưa có đánh giá nào</p>
                            <p class="text-sm mt-1">Sản phẩm này chưa nhận được đánh giá nào. Hãy là người đầu tiên đánh giá
                                trải nghiệm của mình.</p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Sidebar: Tech Specs -->
            <div class="lg:col-span-4 space-y-8">
                <div
                    class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5 dark:bg-slate-900 dark:ring-slate-800">
                    <h3 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">Thông số kỹ thuật</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between border-b border-slate-100 pb-2 dark:border-slate-800">
                            <span class="text-sm text-slate-500 dark:text-slate-400">Nền tảng</span>
                            <span
                                class="text-sm font-medium text-slate-900 dark:text-white">{{ $product->platform ?? 'Đa nền tảng' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-100 pb-2 dark:border-slate-800">
                            <span class="text-sm text-slate-500 dark:text-slate-400">Phiên bản</span>
                            <span
                                class="text-sm font-medium text-slate-900 dark:text-white">{{ $product->version ?? 'Mặc định' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-100 pb-2 dark:border-slate-800">
                            <span class="text-sm text-slate-500 dark:text-slate-400">Danh mục</span>
                            <span
                                class="text-sm font-medium text-slate-900 dark:text-white">{{ $product->category->name ?? 'None' }}</span>
                        </div>
                        <div class="flex justify-between pt-1">
                            <span class="text-sm text-slate-500 dark:text-slate-400">Tình trạng</span>
                            <span class="text-sm font-medium text-green-500">Hoạt động tốt</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-16 pt-8 border-t border-slate-200 dark:border-slate-800">
                <h3 class="mb-6 text-2xl font-bold text-slate-900 dark:text-white">Có thể bạn quan tâm</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                    @foreach($relatedProducts as $related)
                        <x-app.product-card :product="$related" />
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Fullscreen Modal -->
        <template x-teleport="body">
            <div x-show="isFullscreen" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[999] bg-black/95 flex items-center justify-center p-4"
                @keydown.escape.window="isFullscreen = false" @click="isFullscreen = false">
                <button @click.stop="isFullscreen = false"
                    class="absolute top-4 right-4 text-white/50 hover:text-white transition-colors p-2 bg-black/50 rounded-full flex z-10">
                    <span class="material-symbols-outlined text-[30px]">close</span>
                </button>
                <div class="relative max-w-[95vw] max-h-[95vh]" @click.stop>
                    <img :src="mainImage" class="max-h-[92vh] max-w-full rounded shadow-2xl object-contain">
                </div>
            </div>
        </template>
    </main>
@endsection