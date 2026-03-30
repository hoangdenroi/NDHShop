{{-- Component Footer --}}
<footer
    class="bg-white dark:bg-gray-950 border-t border-gray-200 dark:border-gray-800 flex flex-col items-center w-full px-4 lg:px-20 py-12">
    <div class="max-w-[1400px] w-full grid grid-cols-1 md:grid-cols-3 gap-12">
        <!-- Column 1: Company Info -->
        <div class="flex flex-col gap-4">
            <div>
                <h3 class="text-sm font-black uppercase tracking-wider text-[#111318] dark:text-white mb-4">NDHShop
                </h3>
                <ul class="flex flex-col gap-3 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-lg opacity-70">account_circle</span>
                        <span>Người đại diện: <span class="font-bold text-[#111318] dark:text-gray-200">Nguyễn Đức
                                Hoàng</span></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-lg opacity-70">receipt_long</span>
                        <span>Mã số thuế: <span class="font-bold text-[#111318] dark:text-gray-200">Đang cập
                                nhật</span></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-lg opacity-70">location_on</span>
                        <span>Địa chỉ: <span class="font-bold text-[#111318] dark:text-gray-200">Hà Nội, Việt
                                Nam</span></span>
                    </li>
                </ul>
            </div>

            <div class="pt-6 border-t border-gray-100 dark:border-gray-800">
                <p class="text-xs text-gray-500 mb-2">Copyright © {{ date('Y') }} NDHShop. All rights reserved.</p>
                <p class="text-xs text-gray-500 mb-2">Version: 1.0.0</p>
                <!-- <div class="flex gap-4 text-xs font-bold text-gray-800 dark:text-gray-300">
                    <a routerLink="/apps/privacy" class="hover:text-primary transition-colors">Privacy Policy</a>
                    <span class="opacity-30">|</span>
                    <a routerLink="/apps/terms" class="hover:text-primary transition-colors">Terms of Service</a>
                </div> -->
            </div>
        </div>

        <!-- Column 2: Contact & Socials -->
        <div class="flex flex-col gap-4">
            <div>
                <h3 class="text-sm font-black uppercase tracking-wider text-[#111318] dark:text-white mb-4">Liên hệ</h3>
                <ul class="flex flex-col gap-3 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-lg opacity-70">call</span>
                        <a href="tel:+84388937608" class="hover:text-primary transition-colors">
                            Hotline: <span class="font-bold text-[#111318] dark:text-gray-200">+84 388937608</span>
                        </a>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-lg opacity-70">mail</span>
                        <a href="mailto:support@ndhshop.com" class="hover:text-primary transition-colors">
                            Email: <span
                                class="font-bold text-[#111318] dark:text-gray-200">support&#64;ndhshop.com</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- <div class="flex flex-col gap-4">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Theo dõi chúng tôi tại</p>
                <div class="flex flex-col gap-2">
                    <a href="https://beacons.ai" target="_blank"
                        class="w-full flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 hover:border-primary/50 transition-all group">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-lg opacity-70">link</span>
                            <span class="text-xs font-bold font-medium">beacons.ai</span>
                        </div>
                        <span
                            class="material-symbols-outlined text-xs opacity-0 group-hover:opacity-100 transition-opacity">open_in_new</span>
                    </a>
                    <a href="https://www.facebook.com/dichvuwebonline" target="_blank"
                        class="w-full flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 hover:border-primary/50 transition-all group">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-lg opacity-70">facebook</span>
                            <span class="text-xs font-bold font-medium">Fanpage Facebook Dịch vụ Web</span>
                        </div>
                        <span
                            class="material-symbols-outlined text-xs opacity-0 group-hover:opacity-100 transition-opacity">open_in_new</span>
                    </a>
                </div>
            </div> -->

            <!-- <div class="flex flex-col gap-4">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Dịch vụ khác</p>
                <div class="flex flex-col gap-2">
                    <a href="#"
                        class="flex items-center justify-between p-2 rounded-lg bg-primary/5 dark:bg-primary/10 border border-primary/10 hover:bg-primary/10 transition-all group">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-lg text-primary">local_fire_department</span>
                            <span class="text-xs font-bold text-primary">Deal Hot Hôm Nay</span>
                        </div>
                    </a>
                    <a href="#"
                        class="flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 hover:border-primary/50 transition-all group">
                        <div class="flex items-center gap-3 text-orange-600">
                            <span class="material-symbols-outlined text-lg">code</span>
                            <span class="text-xs font-bold">Làm Website/App theo yêu cầu</span>
                        </div>
                    </a>
                </div>
            </div> -->
        </div>

        <!-- Column 3: Optional Widget/Area -->
        <div class="flex flex-col gap-4">
            <div>
                <h3 class="text-sm font-black uppercase tracking-wider text-[#111318] dark:text-white mb-4">Đăng ký nhận
                    tin mới nhất</h3>
                <div class="flex flex-col gap-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        Nhận các bài viết mới về thiết kế, sản phẩm và mã giảm giá hàng tháng trực tiếp qua email của
                        bạn.
                    </p>
                    <form x-data="{
                            email: '',
                            loading: false,
                            successMsg: '',
                            errorMsg: '',
                            submit() {
                                this.loading = true;
                                this.successMsg = '';
                                this.errorMsg = '';
                                fetch('/api/v1/newsletter/subscribe', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ email: this.email })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    this.loading = false;
                                    if(data.success) {
                                        this.successMsg = data.message;
                                        this.email = '';
                                    } else {
                                        if(data.errors) {
                                            this.errorMsg = Object.values(data.errors)[0][0];
                                        } else {
                                            this.errorMsg = data.message || 'Lỗi hệ thống';
                                        }
                                    }
                                })
                                .catch(err => {
                                    this.loading = false;
                                    this.errorMsg = 'Có lỗi xảy ra.';
                                });
                            }
                        }" @submit.prevent="submit" class="flex flex-col gap-2">
                        
                        <template x-if="successMsg">
                            <p class="text-xs text-green-500 font-medium" x-text="successMsg"></p>
                        </template>
                        <template x-if="errorMsg">
                            <p class="text-xs text-red-500 font-medium" x-text="errorMsg"></p>
                        </template>

                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-lg text-gray-400 group-focus-within:text-primary transition-colors">email</span>
                            <input x-model="email" required type="email" placeholder="Email của bạn..." class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-900 border transition-all rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary disabled:opacity-50 disabled:cursor-not-allowed">
                        </div>
                        <button :disabled="loading" type="submit" class="w-full py-2.5 px-4 bg-[#111318] dark:bg-white text-white dark:text-[#111318] text-xs font-bold uppercase tracking-widest rounded-xl hover:opacity-90 transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <span x-show="!loading">Đăng ký</span>
                            <span x-show="loading">Đang chờ...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</footer>