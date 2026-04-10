@extends('layouts.app.app-layout')
@section('content')
    {{-- QRCode Styling Library --}}
    <script type="text/javascript" src="https://unpkg.com/qr-code-styling@1.5.0/lib/qr-code-styling.js"></script>

    <div class="container mx-auto px-4 py-8 max-w-2xl" x-data="giftSuccess()" x-init="initQR()">
        {{-- Confetti animation --}}
        <div class="text-center mb-8">
            <div
                class="size-20 bg-emerald-50 dark:bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                <span class="material-symbols-outlined text-[40px] text-emerald-500">celebration</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-2">
                🎉 Chúc mừng! Gift đã sẵn sàng
            </h1>
            <p class="text-slate-500">Chia sẻ link bên dưới để gửi món quà tới người thương nhé!</p>
        </div>

        {{-- Gift Info Card --}}
        <div
            class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-2xl overflow-hidden shadow-sm mb-6">
            <div class="p-5 flex items-center gap-4 border-b border-slate-100 dark:border-border-dark">
                <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-[24px]">redeem</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-900 dark:text-white truncate">{{ $gift->meta_title }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold
                                            {{ $gift->isPremium() ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-600' }}">
                            {{ $gift->isPremium() ? '⭐ Premium' : 'Basic' }}
                        </span>
                        @if ($gift->expires_at)
                            <span class="text-xs text-slate-400">• Hết hạn: {{ $gift->expires_at->format('d/m/Y') }}</span>
                        @else
                            <span class="text-xs text-emerald-500 font-medium">• Vĩnh viễn</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tab: Link / QR --}}
            <div class="p-5">
                <div class="flex border-b border-slate-100 dark:border-border-dark mb-5">
                    <button @click="activeTab = 'link'"
                        :class="activeTab === 'link' ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600'"
                        class="flex items-center gap-2 pb-3 px-4 text-sm font-bold border-b-2 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">link</span>
                        Link chia sẻ
                    </button>
                    <button @click="activeTab = 'qr'"
                        :class="activeTab === 'qr' ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600'"
                        class="flex items-center gap-2 pb-3 px-4 text-sm font-bold border-b-2 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">qr_code_2</span>
                        Mã QR Nghệ thuật
                    </button>
                </div>

                {{-- Tab: Link --}}
                <div x-show="activeTab === 'link'" x-transition>
                    <div
                        class="flex items-center gap-2 bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark rounded-xl p-3">
                        <input type="text" value="{{ $gift->share_url }}" readonly id="share-link"
                            class="flex-1 bg-transparent text-sm font-mono text-slate-700 dark:text-slate-300 focus:outline-none select-all truncate">
                        <button @click="copyLink()"
                            class="shrink-0 flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-bold rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-[16px]"
                                x-text="copied ? 'check' : 'content_copy'"></span>
                            <span x-text="copied ? 'Đã copy!' : 'Copy'"></span>
                        </button>
                    </div>

                    {{-- Nút mở link --}}
                    <div class="mt-4 text-center">
                        <a href="{{ $gift->share_url }}" target="_blank"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary/80 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                            Mở thử trong tab mới
                        </a>
                    </div>
                </div>

                {{-- Tab: QR --}}
                <div x-show="activeTab === 'qr'" x-transition class="space-y-5">
                    {{-- QR Preview với khung hình --}}
                    <div class="flex justify-center">
                        <div x-ref="qrExportArea" class="relative inline-block">
                            {{-- Khung hình chứa QR --}}
                            <div x-ref="qrFrame" class="relative overflow-hidden bg-white transition-all duration-300"
                                :style="getFrameStyle()" :class="getFrameClass()">
                                {{-- Ảnh nền người dùng --}}
                                <img x-show="bgImage" x-cloak :src="bgImage"
                                    class="absolute inset-0 w-full h-full object-cover opacity-50">
                                {{-- QR Code chính (nằm chính giữa) --}}
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div x-ref="qrContainer" class="relative z-10"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Controls --}}
                    <div class="space-y-4">
                        {{-- Khung hình (Outer Shape) --}}
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Khung
                                hình</label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="f in frames">
                                    <button @click="qrShape = f.id"
                                        class="px-3 py-2 text-xs font-bold rounded-xl border transition-all flex items-center gap-1.5"
                                        :class="qrShape === f.id ? 'bg-primary border-primary text-white shadow-lg' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300'">
                                        <span x-text="f.icon" class="text-sm"></span>
                                        <span x-text="f.name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Hình dạng mã (Dots Style) --}}
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Kiểu
                                chấm</label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="s in shapes">
                                    <button
                                        @click="qrOptions.dotsOptions.type = s.id; qrOptions.cornersSquareOptions.type = s.cornerId; updateQR()"
                                        class="px-3 py-2 text-xs font-bold rounded-xl border transition-all"
                                        :class="qrOptions.dotsOptions.type === s.id ? 'bg-primary border-primary text-white shadow-lg' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300'"
                                        x-text="s.name">
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Màu sắc --}}
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Màu
                                sắc</label>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <template x-for="c in colorPresets">
                                    <button @click="setColor(c)"
                                        class="size-8 rounded-full border-2 border-white shadow-sm transition-transform hover:scale-110 active:scale-95"
                                        :style="'background-color:' + c"
                                        :class="qrOptions.dotsOptions.color === c ? 'ring-2 ring-primary ring-offset-2 dark:ring-offset-slate-900' : ''">
                                    </button>
                                </template>
                                <div class="h-6 w-px bg-slate-200 dark:bg-slate-700"></div>
                                <input type="color" x-model="qrOptions.dotsOptions.color"
                                    @input="setColor($event.target.value)"
                                    class="size-8 rounded-md cursor-pointer border-0 p-0 overflow-hidden outline-none">
                            </div>
                        </div>

                        {{-- Ảnh nền --}}
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Ảnh
                                nền</label>
                            <div class="flex items-center gap-3">
                                <input type="file" x-ref="bgInput" accept="image/*" @change="handleBgUpload($event)"
                                    class="hidden">
                                <button @click="$refs.bgInput.click()"
                                    class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">add_photo_alternate</span>
                                    <span x-text="bgImage ? 'Đổi ảnh' : 'Chọn ảnh nền'"></span>
                                </button>
                                <button x-show="bgImage" @click="bgImage = ''; drawFiller()"
                                    class="flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-xl border border-red-200 text-red-500 hover:bg-red-50 transition-colors">
                                    <span class="material-symbols-outlined text-[14px]">close</span>
                                    Xóa ảnh
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button @click="downloadComposite('png')" :disabled="downloading"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-primary hover:bg-primary/90 text-white text-sm font-bold rounded-xl transition-all shadow-lg hover:shadow-primary/20 disabled:opacity-50">
                            <span class="material-symbols-outlined text-[18px]" :class="downloading ? 'animate-spin' : ''"
                                x-text="downloading ? 'sync' : 'image'"></span>
                            <span x-text="downloading ? 'Đang xử lý...' : 'Tải ảnh PNG'"></span>
                        </button>
                        <button @click="downloadComposite('svg')" :disabled="downloading"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-xl transition-all disabled:opacity-50">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                            Tải QR gốc (SVG)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Countdown cho Basic --}}
        @if (!$gift->isPremium() && $gift->expires_at)
            <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-2xl p-5 mb-6">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-amber-500 text-[24px] mt-0.5 shrink-0">timer</span>
                    <div>
                        <p class="font-bold text-amber-700 dark:text-amber-400 mb-1">
                            @php
                                $daysLeft = (int) now()->diffInDays($gift->expires_at, false);
                            @endphp
                            @if ($daysLeft > 0)
                                Link sẽ hết hạn sau {{ $daysLeft }} ngày
                            @else
                                Link sẽ hết hạn trong hôm nay
                            @endif
                        </p>
                        <p class="text-sm text-amber-600 dark:text-amber-400/80 mb-3">
                            Nâng cấp lên Premium để giữ link vĩnh viễn, bỏ watermark và có analytics chi tiết.
                        </p>
                        <form method="POST" action="{{ route('app.gifts.upgrade', $gift->share_code) }}">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-purple-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-primary/20 transition-all transform hover:-translate-y-0.5">
                                <span class="material-symbols-outlined text-[16px]">upgrade</span>
                                Nâng cấp Premium —
                                {{ number_format(\App\Models\GiftPage::PLAN_PRICES['premium'], 0, ',', '.') }}đ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Nút quay về --}}
        <div class="text-center">
            <a href="{{ route('app.gifts.my-gifts') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-colors">
                <span class="material-symbols-outlined text-[18px]">dashboard</span>
                Về trang Quà tặng của tôi
            </a>
        </div>
    </div>

    {{-- html2canvas để chụp khung hình composite --}}
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
        function giftSuccess() {
            return {
                activeTab: 'link',
                copied: false,
                downloading: false,
                qrInstance: null,
                qrShape: 'square',
                bgImage: '',
                dotColor: '#0d59f2',
                qrOptions: {
                    width: 180,
                    height: 180,
                    type: 'canvas',
                    data: '{{ $gift->share_url }}',
                    dotsOptions: { color: '#0d59f2', type: 'rounded' },
                    backgroundOptions: { color: 'transparent' },
                    cornersSquareOptions: { color: '#0d59f2', type: 'extra-rounded' },
                    cornersDotOptions: { type: 'dot' },
                    qrOptions: { errorCorrectionLevel: 'H' },
                    margin: 0
                },
                // Kích thước khung hình theo loại (px)
                frameSizes: {
                    square: 200,
                    rounded: 210,
                    circle: 280,
                    heart: 300
                },
                shapes: [
                    { id: 'square', cornerId: 'square', name: 'Vuông' },
                    { id: 'rounded', cornerId: 'extra-rounded', name: 'Bo góc' },
                    { id: 'extra-rounded', cornerId: 'extra-rounded', name: 'Tròn đều' },
                    { id: 'dots', cornerId: 'dot', name: 'Chấm bi' },
                    { id: 'classy-rounded', cornerId: 'extra-rounded', name: 'Nghệ thuật' }
                ],
                frames: [
                    { id: 'square', name: 'Vuông', icon: '⬜' },
                    { id: 'rounded', name: 'Bo góc', icon: '🔲' },
                    { id: 'circle', name: 'Tròn', icon: '⭕' },
                    { id: 'heart', name: 'Trái tim', icon: '💖' }
                ],
                colorPresets: ['#0d59f2', '#f2295b', '#fbbf24', '#0f172a', '#10b981', '#8b5cf6'],

                initQR() {
                    this.qrInstance = new QRCodeStyling(this.qrOptions);
                    this.$nextTick(() => {
                        this.qrInstance.append(this.$refs.qrContainer);
                    });
                },

                updateQR() {
                    if (this.qrInstance) {
                        this.qrInstance.update(this.qrOptions);
                    }
                },

                setColor(c) {
                    this.dotColor = c;
                    this.qrOptions.dotsOptions.color = c;
                    this.qrOptions.cornersSquareOptions.color = c;
                    this.updateQR();
                },

                // Lấy style CSS cho khung hình (clip-path + kích thước)
                getFrameStyle() {
                    var size = this.frameSizes[this.qrShape] || 200;
                    var base = 'width:' + size + 'px; height:' + size + 'px;';
                    var clips = {
                        square: '',
                        rounded: '',
                        circle: 'clip-path: circle(50% at 50% 50%);',
                        heart: 'clip-path: url(#heartClip);'
                    };
                    return base + (clips[this.qrShape] || '');
                },

                getFrameClass() {
                    var cls = {
                        square: 'rounded-2xl shadow-xl',
                        rounded: 'rounded-[2rem] shadow-xl',
                        circle: 'shadow-xl',
                        heart: ''
                    };
                    return cls[this.qrShape] || '';
                },

                // Xử lý tải ảnh nền
                handleBgUpload(event) {
                    var file = event.target.files[0];
                    if (!file) return;
                    if (file.size > 15 * 1024 * 1024) {
                        alert('Ảnh không được vượt quá 15MB');
                        return;
                    }
                    var reader = new FileReader();
                    var self = this;
                    reader.onload = function (e) {
                        self.bgImage = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                copyLink() {
                    navigator.clipboard.writeText('{{ $gift->share_url }}');
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                },

                // Tải xuống dạng composite (chụp toàn bộ khung hình)
                async downloadComposite(ext) {
                    this.downloading = true;
                    try {
                        if (ext === 'svg') {
                            // Tải QR gốc dạng SVG (không kèm khung)
                            // Tạm đổi type sang svg để export
                            this.qrOptions.type = 'svg';
                            this.qrInstance.update(this.qrOptions);
                            await new Promise(r => setTimeout(r, 200));
                            await this.qrInstance.download({ name: 'gift-qr-{{ $gift->share_code }}', extension: 'svg' });
                            // Đổi lại canvas
                            this.qrOptions.type = 'canvas';
                            this.qrInstance.update(this.qrOptions);
                            return;
                        }

                        // Chụp toàn bộ khung hình bằng html2canvas
                        var el = this.$refs.qrFrame;
                        var canvas = await html2canvas(el, {
                            backgroundColor: null,
                            scale: 3,       // Chất lượng cao (3x)
                            useCORS: true,
                            logging: false
                        });
                        canvas.toBlob(function (blob) {
                            var url = URL.createObjectURL(blob);
                            var a = document.createElement('a');
                            a.href = url;
                            a.download = 'gift-qr-{{ $gift->share_code }}.png';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                        }, 'image/png');
                    } catch (error) {
                        console.error('Download error:', error);
                        alert('Không thể tải ảnh QR. Vui lòng thử lại.');
                    } finally {
                        this.downloading = false;
                    }
                }
            }
        }
    </script>

    {{-- SVG Clip-path cho hình Trái tim (dùng cho CSS clip-path: url(#heartClip)) --}}
    <svg width="0" height="0" style="position:absolute;">
        <defs>
            <clipPath id="heartClip" clipPathUnits="objectBoundingBox">
                <path
                    d="M0.5,0.15 C0.5,0.05 0.6,0 0.7,0 C0.85,0 1,0.1 1,0.3 C1,0.6 0.5,1 0.5,1 C0.5,1 0,0.6 0,0.3 C0,0.1 0.15,0 0.3,0 C0.4,0 0.5,0.05 0.5,0.15 Z" />
            </clipPath>
        </defs>
    </svg>
@endsection