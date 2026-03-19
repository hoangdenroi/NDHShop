<x-auth-layout>
    {{-- Card xác nhận mật khẩu --}}
    <div class="w-full max-w-md bg-white dark:bg-[#151c2b] rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-black/20 border border-slate-100 dark:border-slate-800 overflow-hidden">
        <div class="p-8 sm:p-10">
            {{-- Tiêu đề --}}
            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-primary text-3xl">shield_lock</span>
                </div>
                <h1 class="text-slate-900 dark:text-white text-3xl font-bold leading-tight tracking-tight mb-2">
                    Xác Nhận Mật Khẩu
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-normal">
                    Đây là khu vực bảo mật. Vui lòng xác nhận mật khẩu trước khi tiếp tục.
                </p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
                @csrf

                {{-- Mật khẩu --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="password">Mật khẩu</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">lock</span>
                        <input
                            class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary pl-10 pr-10 h-12 sm:text-sm"
                            id="password" name="password" type="password"
                            placeholder="••••••••" required autocomplete="current-password" />
                        <button type="button"
                            class="absolute right-3 top-0 h-full flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                            onclick="const input = document.getElementById('password'); const icon = this.querySelector('span'); if(input.type === 'password') { input.type = 'text'; icon.textContent = 'visibility_off'; } else { input.type = 'password'; icon.textContent = 'visibility'; }">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                    <x-ui.input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                {{-- Nút xác nhận --}}
                <button type="submit"
                    class="w-full flex items-center justify-center h-12 bg-primary hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-md shadow-blue-500/20 active:scale-[0.98]">
                    Xác Nhận
                </button>
            </form>
        </div>
    </div>
</x-auth-layout>
