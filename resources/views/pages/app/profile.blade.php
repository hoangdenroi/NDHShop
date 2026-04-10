@extends('layouts.app.app-layout')

@section('content')
<div x-data="{ 
    activeTab: new URLSearchParams(window.location.search).get('tab') || 
               {{ session('status') === 'password-updated' || session('status') === 'profile-updated' || $errors->updatePassword->isNotEmpty() || $errors->has('email') ? "'settings'" : "'profile'" }},
    isFullscreen: false
}" class="flex flex-col lg:flex-row gap-8 w-full">
    {{-- Sidebar --}}
    <aside class="w-full lg:w-72 shrink-0 flex flex-col gap-6">
        {{-- Thẻ thông tin người dùng --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-100 dark:border-slate-700 shadow-sm flex flex-col items-center text-center">
            <div @click="isFullscreen = true" class="h-24 w-24 rounded-full bg-primary text-white flex items-center justify-center mb-4 ring-4 ring-slate-50 dark:ring-slate-700 text-3xl font-bold overflow-hidden cursor-pointer hover:ring-primary/50 transition-all">
                @if(Auth::user()->avatar_url)
                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="h-full w-full object-cover">
                @else
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                @endif
            </div>
            <h1 class="text-slate-900 dark:text-white text-lg font-bold">{{ Auth::user()->name }}</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">{{ Auth::user()->email }}</p>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Số dư: {{ number_format(Auth::user()->balance, 0, ',', '.') }} VND</p>
            @if(Auth::user()->role == 'admin')
                <a target="_blank" href="{{ route('admin.dashboard') }}" class="text-red-500 dark:text-red-400 text-sm font-medium"><span class="material-symbols-outlined">admin_panel_settings</span> Vào trang quản trị</a>
            @endif
        </div>

        {{-- Menu điều hướng (Alpine.js tabs) --}}
        <nav class="bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-700 shadow-sm">
            <div class="flex flex-col p-2 gap-1">
                <button @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left w-full group">
                    <span class="material-symbols-outlined" :class="activeTab === 'profile' ? 'text-primary' : 'text-slate-400 group-hover:text-primary'">person</span>
                    <span class="font-medium text-sm">Thông tin cá nhân</span>
                </button>
                <button @click="activeTab = 'topup'" :class="activeTab === 'topup' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left w-full group">
                    <span class="material-symbols-outlined" :class="activeTab === 'topup' ? 'text-primary' : 'text-slate-400 group-hover:text-primary'">account_balance_wallet</span>
                    <span class="font-medium text-sm">Nạp tiền</span>
                </button>
                <button @click="activeTab = 'purchases'" :class="activeTab === 'purchases' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left w-full group">
                    <span class="material-symbols-outlined" :class="activeTab === 'purchases' ? 'text-primary' : 'text-slate-400 group-hover:text-primary'">download</span>
                    <span class="font-medium text-sm">Sản phẩm đã mua</span>
                </button>
                <button @click="activeTab = 'history'" :class="activeTab === 'history' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left w-full group">
                    <span class="material-symbols-outlined" :class="activeTab === 'history' ? 'text-primary' : 'text-slate-400 group-hover:text-primary'">history</span>
                    <span class="font-medium text-sm">Lịch sử giao dịch</span>
                </button>
                <button @click="activeTab = 'favorites'" :class="activeTab === 'favorites' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left w-full group">
                    <span class="material-symbols-outlined" :class="activeTab === 'favorites' ? 'text-primary' : 'text-slate-400 group-hover:text-primary'">favorite</span>
                    <span class="font-medium text-sm">Yêu thích</span>
                </button>
                <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left w-full group">
                    <span class="material-symbols-outlined" :class="activeTab === 'settings' ? 'text-primary' : 'text-slate-400 group-hover:text-primary'">settings</span>
                    <span class="font-medium text-sm">Cài đặt</span>
                </button>
            </div>
            <div class="border-t border-slate-100 dark:border-slate-700 p-2 mt-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/10 transition-colors text-left">
                        <span class="material-symbols-outlined">logout</span>
                        <span class="font-medium text-sm">Đăng xuất</span>
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    {{-- Nội dung chính --}}
    <main class="flex-1 flex flex-col gap-6">

        {{-- Tab: Thông tin cá nhân --}}
        <x-app.profile.profile-tab />

        {{-- Tab: Nạp tiền --}}
        <x-app.profile.topup-tab />

        {{-- Tab: Sản phẩm đã mua --}}
        <x-app.profile.purchases-tab />

        {{-- Tab: Lịch sử giao dịch --}}
        <x-app.profile.history-tab />

        {{-- Tab: Yêu thích --}}
        <x-app.profile.favorites-tab />

        {{-- Tab: Cài đặt --}}
        <x-app.profile.settings-tab />

    </main>

    {{-- Modal phóng to ảnh --}}
    <div x-show="isFullscreen" style="display: none;"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/90 backdrop-blur-sm p-4"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="isFullscreen = false"
        @keydown.escape.window="isFullscreen = false">
        {{-- Nút đóng tuyệt đối ở góc màn hình --}}
        <button type="button" @click="isFullscreen = false" 
            class="absolute top-6 right-6 text-white/70 hover:text-white bg-slate-800/80 hover:bg-slate-700 rounded-full p-3 backdrop-blur-md transition-all shadow-xl z-[110]">
            <span class="material-symbols-outlined text-[28px]">close</span>
        </button>

        {{-- Container chứa ảnh luôn nằm giữa --}}
        <div class="relative max-w-[95vw] max-h-[90vh] flex items-center justify-center" @click.stop>
            @if(Auth::user()->avatar_url)
                <img src="{{ Auth::user()->avatar_url }}" alt="Avatar fullscreen" class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl">
            @else
                <div class="h-48 w-48 rounded-full bg-primary text-white flex items-center justify-center text-6xl font-bold shadow-2xl">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
