<div x-show="activeTab === 'profile'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Thông tin tài khoản</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Cập nhật thông tin cá nhân của bạn.</p>
        </div>
        <form method="POST" action="{{ route('app.profile.update') }}" class="p-6 space-y-6">
            @csrf
            @method('patch')
            {{-- Họ tên --}}
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="name">Họ và tên</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">person</span>
                    <input class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary pl-10 h-12 sm:text-sm"
                        id="name" name="name" type="text" value="{{ old('name', Auth::user()->name) }}" required />
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            {{-- Số điện thoại --}}
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="phone">Số điện thoại</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">phone</span>
                    <input class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary pl-10 h-12 sm:text-sm"
                        id="phone" name="phone" type="number" value="{{ old('phone', Auth::user()->phone) }}" required />
                </div>
                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
            </div>

            {{-- Link ảnh --}}
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="avatar">Link ảnh</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">image</span>
                    <input class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary pl-10 h-12 sm:text-sm"
                        id="avatar" name="avatar" type="text" value="{{ old('avatar', Auth::user()->avatar) }}" required />
                </div>
                <x-input-error :messages="$errors->get('avatar')" class="mt-1" />
            </div>

            {{-- Thông tin chỉ xem (không sửa được) --}}
            <div class="border-t border-slate-100 dark:border-slate-700 pt-6 space-y-4">
                <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Thông tin tài khoản</p>

                {{-- Mã tài khoản --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Mã tài khoản</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">badge</span>
                        <input class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 pl-10 h-12 sm:text-sm cursor-not-allowed"
                            value="{{ Auth::user()->unitcode }}" readonly />
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 dark:text-slate-600 text-[18px] pointer-events-none">lock</span>
                    </div>
                </div>

                {{-- Số dư --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Số dư tài khoản</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">account_balance_wallet</span>
                        <input class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 pl-10 h-12 sm:text-sm cursor-not-allowed"
                            value="{{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }} VNĐ" readonly />
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 dark:text-slate-600 text-[18px] pointer-events-none">lock</span>
                    </div>
                </div>

                {{-- Đăng nhập lần cuối --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Đăng nhập lần cuối</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">schedule</span>
                        <input class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 pl-10 h-12 sm:text-sm cursor-not-allowed"
                            value="{{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('d/m/Y H:i') : 'Chưa có' }}" readonly />
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 dark:text-slate-600 text-[18px] pointer-events-none">lock</span>
                    </div>
                </div>
            </div>

            {{-- Nút lưu --}}
            <div class="flex items-center gap-4">
                <button type="submit"
                    class="flex items-center justify-center h-12 px-8 bg-primary hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-sm shadow-primary/20 active:scale-[0.98]">
                    Lưu thay đổi
                </button>
                @if (session('status') === 'profile-updated')
                    <p class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">Đã lưu thành công!</p>
                @endif
            </div>
        </form>
    </div>
</div>
