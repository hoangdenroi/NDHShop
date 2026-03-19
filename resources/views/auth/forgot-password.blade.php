<x-auth-layout>
    {{-- Card quên mật khẩu --}}
    <div class="w-full max-w-md bg-white dark:bg-[#151c2b] rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-black/20 border border-slate-100 dark:border-slate-800 overflow-hidden">
        <div class="p-8 sm:p-10">
            {{-- Tiêu đề --}}
            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-primary text-3xl">lock_reset</span>
                </div>
                <h1 class="text-slate-900 dark:text-white text-3xl font-bold leading-tight tracking-tight mb-2">
                    Quên Mật Khẩu?
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-normal">
                    Nhập email của bạn, chúng tôi sẽ gửi liên kết đặt lại mật khẩu.
                </p>
            </div>

            {{-- Trạng thái session --}}
            <x-ui.auth-session-status class="mb-4" :status="session('status')" />

            {{-- Form --}}
            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="email">Email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">mail</span>
                        <input
                            class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary pl-10 h-12 sm:text-sm"
                            id="email" name="email" type="email" value="{{ old('email') }}"
                            placeholder="email@example.com" required autofocus />
                    </div>
                    <x-ui.input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                {{-- Nút gửi --}}
                <button type="submit"
                    class="w-full flex items-center justify-center h-12 bg-primary hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-md shadow-blue-500/20 active:scale-[0.98]">
                    Gửi Liên Kết Đặt Lại
                </button>
            </form>
        </div>

        {{-- Footer card --}}
        <div class="bg-slate-50 dark:bg-slate-800/50 px-8 py-5 text-center border-t border-slate-100 dark:border-slate-800">
            <p class="text-slate-600 dark:text-slate-400 text-sm">
                Nhớ mật khẩu rồi?
                <a class="font-bold text-primary hover:text-blue-700 dark:hover:text-blue-400 transition-colors"
                    href="{{ route('login') }}">Quay lại đăng nhập</a>
            </p>
        </div>
    </div>
</x-auth-layout>
