<x-auth-layout>
    {{-- Card xác minh email --}}
    <div class="w-full max-w-md bg-white dark:bg-[#151c2b] rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-black/20 border border-slate-100 dark:border-slate-800 overflow-hidden">
        <div class="p-8 sm:p-10">
            {{-- Tiêu đề --}}
            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-primary text-3xl">mark_email_unread</span>
                </div>
                <h1 class="text-slate-900 dark:text-white text-3xl font-bold leading-tight tracking-tight mb-2">
                    Xác Minh Email
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-normal">
                    Cảm ơn bạn đã đăng ký! Vui lòng xác minh địa chỉ email bằng cách bấm vào liên kết chúng tôi vừa gửi.
                </p>
            </div>

            {{-- Thông báo đã gửi lại liên kết --}}
            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                    <p class="text-sm font-medium text-green-700 dark:text-green-400 text-center">
                        Liên kết xác minh mới đã được gửi đến email của bạn.
                    </p>
                </div>
            @endif

            {{-- Các nút hành động --}}
            <div class="space-y-3">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center h-12 bg-primary hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-md shadow-blue-500/20 active:scale-[0.98]">
                        Gửi Lại Email Xác Minh
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center h-12 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-[0.98]">
                        Đăng Xuất
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-auth-layout>
