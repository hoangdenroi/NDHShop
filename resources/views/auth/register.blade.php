<x-auth-layout>
    {{-- Card đăng ký --}}
    <div class="w-full max-w-md bg-white dark:bg-[#151c2b] rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-black/20 border border-slate-100 dark:border-slate-800 overflow-hidden">
        <div class="p-8 sm:p-10">
            {{-- Tiêu đề --}}
            <div class="text-center mb-8">
                <h1 class="text-slate-900 dark:text-white text-3xl font-bold leading-tight tracking-tight mb-2">
                    Tạo Tài Khoản
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-base font-normal">
                    Nhập thông tin để đăng ký tài khoản mới.
                </p>
            </div>

            {{-- Form đăng ký --}}
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                {{-- Họ tên --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="name">Họ tên</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">person</span>
                        <input
                            class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary pl-10 h-12 sm:text-sm"
                            id="name" name="name" type="text" value="{{ old('name') }}"
                            placeholder="Nguyễn Văn A" required autofocus autocomplete="name" />
                    </div>
                    <x-ui.input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                {{-- Email --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="email">Email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">mail</span>
                        <input
                            class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary pl-10 h-12 sm:text-sm"
                            id="email" name="email" type="email" value="{{ old('email') }}"
                            placeholder="email@example.com" required autocomplete="username" />
                    </div>
                    <x-ui.input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                {{-- Mật khẩu --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="password">Mật khẩu</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">lock</span>
                        <input
                            class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary pl-10 pr-10 h-12 sm:text-sm"
                            id="password" name="password" type="password"
                            placeholder="••••••••" required autocomplete="new-password" />
                        <button type="button"
                            class="absolute right-3 top-0 h-full flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                            onclick="const input = document.getElementById('password'); const icon = this.querySelector('span'); if(input.type === 'password') { input.type = 'text'; icon.textContent = 'visibility_off'; } else { input.type = 'password'; icon.textContent = 'visibility'; }">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                    <x-ui.input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                {{-- Xác nhận mật khẩu --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="password_confirmation">Xác nhận mật khẩu</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">lock</span>
                        <input
                            class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary pl-10 pr-10 h-12 sm:text-sm"
                            id="password_confirmation" name="password_confirmation" type="password"
                            placeholder="••••••••" required autocomplete="new-password" />
                        <button type="button"
                            class="absolute right-3 top-0 h-full flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                            onclick="const input = document.getElementById('password_confirmation'); const icon = this.querySelector('span'); if(input.type === 'password') { input.type = 'text'; icon.textContent = 'visibility_off'; } else { input.type = 'password'; icon.textContent = 'visibility'; }">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                    <x-ui.input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                {{-- Nút đăng ký --}}
                <button type="submit"
                    class="w-full flex items-center justify-center h-12 bg-primary hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-md shadow-blue-500/20 active:scale-[0.98]">
                    Đăng Ký
                </button>
            </form>

            {{-- Đường phân cách --}}
            <div class="relative flex items-center py-6">
                <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                <span class="flex-shrink-0 mx-4 text-slate-400 text-xs font-medium uppercase tracking-wider">Hoặc tiếp tục với</span>
                <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
            </div>

            {{-- Đăng nhập bằng mạng xã hội --}}
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('google.login') }}"
                    class="flex items-center justify-center gap-2 h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-200 font-medium text-sm">
                    <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Google
                </a>
                <button type="button"
                    class="flex items-center justify-center gap-2 h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-200 font-medium text-sm">
                    <svg class="w-5 h-5" fill="#1877F2" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    Facebook
                </button>
            </div>
        </div>

        {{-- Footer card --}}
        <div class="bg-slate-50 dark:bg-slate-800/50 px-8 py-5 text-center border-t border-slate-100 dark:border-slate-800">
            <p class="text-slate-600 dark:text-slate-400 text-sm">
                Đã có tài khoản?
                <a class="font-bold text-primary hover:text-blue-700 dark:hover:text-blue-400 transition-colors"
                    href="{{ route('login') }}">Đăng nhập ngay</a>
            </p>
        </div>
    </div>
</x-auth-layout>
