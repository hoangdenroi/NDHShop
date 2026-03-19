<div x-show="activeTab === 'topup'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak
    @balance-updated.window="balance = $event.detail.new_balance; if (topupView === 'qr_display') { topupView = 'form'; amount = ''; }"
    x-data="{ 
        balance: {{ Auth::user()->balance ?? 0 }},
        amount: '', 
        paymentMethod: 'qr', 
        topupView: 'form',
        qrUrl: '',
        qrDescription: '',
        isLoading: false,
        transactions: [],
        currentPage: 1,
        lastPage: 1,
        isLoadingHistory: false,
        async fetchHistory(page = 1) {
            if (this.isLoadingHistory) return;
            this.isLoadingHistory = true;
            try {
                const response = await fetch(`/api/v1/topup/history?page=${page}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    if (page === 1) {
                        this.transactions = data.data.data;
                    } else {
                        this.transactions = [...this.transactions, ...data.data.data];
                    }
                    this.currentPage = data.data.current_page;
                    this.lastPage = data.data.last_page;
                }
            } catch (error) {
                console.error('Error fetching history:', error);
            } finally {
                this.isLoadingHistory = false;
            }
        },
        init() {
            // Không tự động fetch, chỉ fetch khi bấm vào Lịch sử nạp
        },
        async generateQR() {
            if (!this.amount || parseInt(this.amount) < 20000) {
                alert('Vui lòng nhập số tiền hợp lệ (tối thiểu 20,000đ)');
                return;
            }
            if (this.paymentMethod === 'qr') {
                this.isLoading = true;
                
                try {
                    const response = await fetch('/api/v1/topup/qrcode', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            amount: this.amount
                        })
                    });
                    
                    const data = await response.json();
                    
                    if(data.success) {
                        this.qrUrl = data.qr_url;
                        this.qrDescription = data.description;
                        this.topupView = 'qr_display';
                    } else {
                        alert(data.message || 'Có lỗi xảy ra khi tạo QR thanh toán!');
                    }
                } catch (error) {
                    alert('Lỗi kết nối đến máy chủ. Vui lòng thử lại!');
                } finally {
                    this.isLoading = false;
                }
            } else {
                alert('Phương thức này đang được cập nhật!');
            }
        }
    }">
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white" x-text="topupView === 'form' ? 'Nạp tiền' : (topupView === 'qr_display' ? 'Quét mã thanh toán' : 'Lịch sử nạp tiền')"></h2>
            <button @click="if(topupView === 'history') { topupView = 'form'; } else { topupView = 'history'; fetchHistory(1); }"
                x-show="topupView !== 'qr_display'"
                class="flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary/80 transition-colors">
                <span class="material-symbols-outlined text-[18px]" x-text="topupView === 'form' ? 'history' : 'arrow_back'"></span>
                <span x-text="topupView === 'form' ? 'Lịch sử nạp' : 'Quay lại'"></span>
            </button>
        </div>
        {{-- View: Form nạp tiền --}}
        <div x-show="topupView === 'form'" class="p-6 space-y-6">

            {{-- Số dư hiện tại --}}
            <div class="bg-gradient-to-r from-primary/10 to-primary/5 dark:from-primary/20 dark:to-slate-700/50 rounded-xl p-5 flex items-center gap-4 border border-primary/20 dark:border-primary/30">
                <div class="size-12 rounded-full bg-primary/20 dark:bg-primary/30 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-[28px]">account_balance_wallet</span>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Số dư hiện tại</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white"><span x-text="new Intl.NumberFormat('vi-VN').format(balance)"></span> <span class="text-sm font-medium text-slate-500">VNĐ</span></p>
                </div>
            </div>

            {{-- Số tiền muốn nạp --}}
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Số tiền muốn nạp (VNĐ)</label>
                <div class="relative">
                    <input x-model="amount"
                        @input="amount = $event.target.value.replace(/[^0-9]/g, ''); if(parseInt(amount) > 100000000) amount = '100000000'"
                        class="form-input block w-full rounded-lg border-2 border-primary/40 dark:border-primary/50 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary pr-16 h-14 text-base"
                        type="text" inputmode="numeric" placeholder="Từ: 20,000đ — Tối đa: 100,000,000đ" />
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
                        <button x-show="amount" @click="amount = ''" type="button"
                            class="size-6 rounded-full bg-slate-200 dark:bg-slate-600 hover:bg-slate-300 dark:hover:bg-slate-500 flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-slate-500 dark:text-slate-300 text-[14px]">close</span>
                        </button>
                        <span class="text-slate-400 font-semibold">đ</span>
                    </div>
                </div>
            </div>

            {{-- Chọn nhanh --}}
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Chọn nhanh</label>
                <div class="grid grid-cols-3 gap-3">
                    <!-- <button @click="amount = 2000"
                        :class="amount == 2000 ? 'border-primary bg-primary/10' : 'border-slate-200 dark:border-slate-700'"
                        class="py-3 rounded-lg border text-center font-semibold text-sm text-slate-900 dark:text-white hover:border-primary transition-all">
                        2,000đ
                    </button> -->
                    <button @click="amount = 50000"
                        :class="amount == 50000 ? 'border-primary bg-primary/10' : 'border-slate-200 dark:border-slate-700'"
                        class="py-3 rounded-lg border text-center font-semibold text-sm text-slate-900 dark:text-white hover:border-primary transition-all">
                        50,000đ
                    </button>
                    <button @click="amount = 100000"
                        :class="amount == 100000 ? 'border-primary bg-primary/10' : 'border-slate-200 dark:border-slate-700'"
                        class="py-3 rounded-lg border text-center font-semibold text-sm text-slate-900 dark:text-white hover:border-primary transition-all">
                        100,000đ
                    </button>
                    <button @click="amount = 200000"
                        :class="amount == 200000 ? 'border-primary bg-primary/10' : 'border-slate-200 dark:border-slate-700'"
                        class="py-3 rounded-lg border text-center font-semibold text-sm text-slate-900 dark:text-white hover:border-primary transition-all">
                        200,000đ
                    </button>
                    <button @click="amount = 500000"
                        :class="amount == 500000 ? 'border-primary bg-primary/10' : 'border-slate-200 dark:border-slate-700'"
                        class="py-3 rounded-lg border text-center font-semibold text-sm text-slate-900 dark:text-white hover:border-primary transition-all">
                        500,000đ
                    </button>
                    <button @click="amount = 1000000"
                        :class="amount == 1000000 ? 'border-primary bg-primary/10' : 'border-slate-200 dark:border-slate-700'"
                        class="py-3 rounded-lg border text-center font-semibold text-sm text-slate-900 dark:text-white hover:border-primary transition-all">
                        1,000,000đ
                    </button>
                    <button @click="amount = 2000000"
                        :class="amount == 2000000 ? 'border-primary bg-primary/10' : 'border-slate-200 dark:border-slate-700'"
                        class="py-3 rounded-lg border text-center font-semibold text-sm text-slate-900 dark:text-white hover:border-primary transition-all">
                        2,000,000đ
                    </button>
                </div>
            </div>

            {{-- Phương thức thanh toán --}}
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Phương thức thanh toán</label>
                <div class="grid grid-cols-3 gap-3">
                    <button @click="paymentMethod = 'qr'"
                        :class="paymentMethod === 'qr' ? 'border-primary ring-2 ring-primary' : 'border-slate-200 dark:border-slate-700'"
                        class="relative flex flex-col items-center gap-3 p-5 rounded-xl border bg-slate-50 dark:bg-slate-900 hover:border-primary transition-all">
                        <div x-show="paymentMethod === 'qr'" class="absolute top-2 right-2 size-5 rounded-full bg-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-[14px]">check</span>
                        </div>
                        <img src="{{ asset('assets/images/qr-code.jpg') }}" alt="QR Code" class="w-10 h-10 object-contain rounded">
                        <div class="text-center">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">QR Code</p>
                            <p class="text-xs text-slate-400 mt-0.5">Quét mã QR</p>
                        </div>
                    </button>
                    <button @click="paymentMethod = 'vnpay'"
                        :class="paymentMethod === 'vnpay' ? 'border-primary ring-2 ring-primary' : 'border-slate-200 dark:border-slate-700'"
                        class="relative flex flex-col items-center gap-3 p-5 rounded-xl border bg-slate-50 dark:bg-slate-900 hover:border-primary transition-all">
                        <div x-show="paymentMethod === 'vnpay'" class="absolute top-2 right-2 size-5 rounded-full bg-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-[14px]">check</span>
                        </div>
                        <img src="{{ asset('assets/images/VNPay-Logo.jpg') }}" alt="VNPay" class="w-10 h-10 object-contain rounded">
                        <div class="text-center">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">VNPay</p>
                            <p class="text-xs text-slate-400 mt-0.5">QR, ATM, Visa</p>
                        </div>
                    </button>
                    <button @click="paymentMethod = 'momo'"
                        :class="paymentMethod === 'momo' ? 'border-primary ring-2 ring-primary' : 'border-slate-200 dark:border-slate-700'"
                        class="relative flex flex-col items-center gap-3 p-5 rounded-xl border bg-slate-50 dark:bg-slate-900 hover:border-primary transition-all">
                        <div x-show="paymentMethod === 'momo'" class="absolute top-2 right-2 size-5 rounded-full bg-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-[14px]">check</span>
                        </div>
                        <img src="{{ asset('assets/images/MoMo_Logo.png') }}" alt="MoMo" class="w-10 h-10 object-contain">
                        <div class="text-center">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">Momo</p>
                            <p class="text-xs text-slate-400 mt-0.5">Ví điện tử</p>
                        </div>
                    </button>
                </div>
            </div>

            {{-- Nút nạp tiền --}}
            <button @click="generateQR()" :disabled="isLoading" 
                class="w-full flex items-center justify-center h-12 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all shadow-sm shadow-primary/20 active:scale-[0.98] gap-2 disabled:opacity-75 disabled:cursor-wait">
                <span class="material-symbols-outlined text-[20px]" x-show="!isLoading">add_card</span>
                <span class="material-symbols-outlined text-[20px] animate-spin" x-show="isLoading" x-cloak>autorenew</span>
                <span x-text="isLoading ? 'Đang tạo QR...' : 'Nạp tiền'"></span>
            </button>
        </div>

        {{-- View: Hiển thị QR --}}
        <div x-show="topupView === 'qr_display'" class="p-6 space-y-6 flex flex-col items-center justify-center" x-cloak>
            <div class="w-full flex flex-col items-center text-center">
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 mb-4 inline-block">
                    <img :src="qrUrl" alt="VietQR" class="w-64 h-64 object-contain" />
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-xs mx-auto">Mở ứng dụng ngân hàng và quét mã bên trên. Mọi thông tin đã được điền tự động.</p>
            </div>

            <div class="w-full max-w-sm bg-slate-50 dark:bg-slate-900 rounded-xl p-5 space-y-4 border border-slate-100 dark:border-slate-700 mt-4">
                <div class="flex justify-between items-center pb-3 border-b border-slate-200 dark:border-slate-700">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Số tiền nạp:</span>
                    <span class="font-bold text-primary text-xl"><span x-text="new Intl.NumberFormat('vi-VN').format(amount)"></span>đ</span>
                </div>
                <div class="flex flex-col gap-1 pb-3 border-b border-slate-200 dark:border-slate-700">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Nội dung chuyển khoản:</span>
                    <span class="font-bold text-slate-900 dark:text-white text-lg text-center tracking-wider py-1" x-text="qrDescription"></span>
                </div>
                <div class="pt-1 flex items-start justify-center gap-1.5 px-2">
                    <span class="material-symbols-outlined text-[18px] text-amber-500 mt-0.5 shrink-0">info</span>
                    <span class="text-[13px] leading-snug text-amber-600 dark:text-amber-500 font-medium text-center">
                        Vui lòng nhập <span class="underline underline-offset-2">đúng nội dung chuyển khoản</span> để hệ thống tự động cộng tiền cho bạn trong giây lát.
                    </span>
                </div>
            </div>

            <div class="w-full max-w-sm flex flex-col gap-3">
                <button @click="window.location.href = '?tab=topup'" class="w-full flex items-center justify-center h-12 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all shadow-sm shadow-primary/20 active:scale-[0.98] gap-2">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    Đã nạp tiền
                </button>
                
                <button @click="topupView = 'form'" class="w-full flex items-center justify-center h-12 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-colors gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    Trở lại
                </button>
            </div>
        </div>

        {{-- View: Lịch sử nạp tiền --}}
        <div x-show="topupView === 'history'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div x-show="transactions.length === 0" class="p-12 flex flex-col items-center text-center">
                <span class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600 mb-3">receipt_long</span>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Chưa có giao dịch nạp tiền nào.</p>
                <button @click="topupView = 'form'" class="mt-4 text-primary text-sm font-semibold hover:underline">Nạp tiền ngay →</button>
            </div>
            
            <div x-show="transactions.length > 0" class="p-6 flex flex-col items-center">
                <div class="space-y-4 w-full">
                    <template x-for="item in transactions" :key="item.id">
                        <div class="flex items-center justify-between p-4 gap-3 rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 hover:border-primary/30 transition-colors overflow-hidden">
                            <div class="flex items-center gap-3 shrink-0">
                                <div class="size-10 rounded-full flex items-center justify-center shrink-0"
                                    :class="item.status === 'SUCCESS' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400'">
                                    <span class="material-symbols-outlined text-[20px]" x-text="item.status === 'SUCCESS' ? 'check_circle' : 'close'"></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 dark:text-white truncate" x-text="(item.status === 'SUCCESS' ? '+' : '') + new Intl.NumberFormat('vi-VN').format(item.amount) + 'đ'"></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate" x-text="new Date(item.created_at).toLocaleString('vi-VN')"></p>
                                </div>
                            </div>
                            <div class="text-right min-w-0 flex-1">
                                <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-bold uppercase tracking-wider shrink-0"
                                    :class="item.status === 'SUCCESS' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400'"
                                    x-text="item.status === 'SUCCESS' ? 'Thành công' : 'Thất bại'">
                                </span>
                                <div class="w-full mt-1 flex justify-end">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-full inline-block" :title="item.order_info" x-text="item.order_info || item.payment_method"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <button x-show="currentPage < lastPage" @click="fetchHistory(currentPage + 1)" :disabled="isLoadingHistory"
                    class="mt-6 flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-primary bg-primary/10 hover:bg-primary/20 rounded-full transition-colors disabled:opacity-50">
                    <span x-show="isLoadingHistory" class="material-symbols-outlined text-[18px] animate-spin" x-cloak>autorenew</span>
                    <span x-text="isLoadingHistory ? 'Đang tải...' : 'Tải thêm lịch sử'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
