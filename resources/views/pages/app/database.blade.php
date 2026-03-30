@extends('layouts.app.app-layout')

@section('content')
    <div x-data="{
        activeTab: 'databases',
        showCreateModal: false,
        showConnectionModal: false,
        selectedEngine: 'mysql',
        selectedPlan: 'free',
        copiedField: null,
        copyToClipboard(text, field) {
            navigator.clipboard.writeText(text);
            this.copiedField = field;
            setTimeout(() => this.copiedField = null, 2000);
        }
    }" class="flex flex-col lg:flex-row gap-8 w-full">

        {{-- ═══════════════════════════════════════════ --}}
        {{-- SIDEBAR TRÁI --}}
        {{-- ═══════════════════════════════════════════ --}}
        <aside class="w-full lg:w-72 shrink-0 flex flex-col gap-6">

            {{-- Thẻ tổng quan --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-100 dark:border-slate-700 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <span class="material-symbols-outlined text-white text-2xl">database</span>
                    </div>
                    <div>
                        <h2 class="text-slate-900 dark:text-white text-base font-bold">Cloud Database</h2>
                        <p class="text-slate-500 dark:text-slate-400 text-xs">Mini DBaaS</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-primary">3</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Databases</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-emerald-500">2</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Hoạt động</p>
                    </div>
                </div>
            </div>

            {{-- Menu điều hướng --}}
            <nav
                class="bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-700 shadow-sm">
                <div class="flex flex-col p-2 gap-1">
                    <button @click="activeTab = 'databases'"
                        :class="activeTab === 'databases' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left w-full group">
                        <span class="material-symbols-outlined"
                            :class="activeTab === 'databases' ? 'text-primary' : 'text-slate-400 group-hover:text-primary'">storage</span>
                        <span class="font-medium text-sm">Databases</span>
                        <span class="ml-auto bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full">3</span>
                    </button>
                    <button @click="activeTab = 'api-keys'"
                        :class="activeTab === 'api-keys' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left w-full group">
                        <span class="material-symbols-outlined"
                            :class="activeTab === 'api-keys' ? 'text-primary' : 'text-slate-400 group-hover:text-primary'">key</span>
                        <span class="font-medium text-sm">API Keys</span>
                        <span class="ml-auto bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full">2</span>
                    </button>
                    <button @click="activeTab = 'pricing'"
                        :class="activeTab === 'pricing' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left w-full group">
                        <span class="material-symbols-outlined"
                            :class="activeTab === 'pricing' ? 'text-primary' : 'text-slate-400 group-hover:text-primary'">payments</span>
                        <span class="font-medium text-sm">Bảng giá</span>
                    </button>
                </div>
                <div class="border-t border-slate-100 dark:border-slate-700 p-2 mt-1">
                    <button @click="showCreateModal = true"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors font-medium text-sm shadow-sm">
                        <span class="material-symbols-outlined text-lg">add_circle</span>
                        Tạo Database mới
                    </button>
                </div>
            </nav>

            {{-- Quota hiện tại --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-100 dark:border-slate-700 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Quota hiện tại</h3>
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-500 dark:text-slate-400">Databases</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">3 / 10</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                            <div class="bg-primary h-1.5 rounded-full transition-all" style="width: 30%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-500 dark:text-slate-400">Dung lượng</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">128 MB / 500 MB</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                            <div class="bg-emerald-500 h-1.5 rounded-full transition-all" style="width: 25%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-500 dark:text-slate-400">Gói dịch vụ</span>
                            <span class="font-medium text-primary">Pro</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ═══════════════════════════════════════════ --}}
        {{-- NỘI DUNG CHÍNH --}}
        {{-- ═══════════════════════════════════════════ --}}
        <main class="flex-1 flex flex-col gap-6">

            {{-- ============================================ --}}
            {{-- TAB: DATABASES --}}
            {{-- ============================================ --}}
            <div x-show="activeTab === 'databases'" x-cloak>

                {{-- Tiêu đề + Tìm kiếm --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Databases của bạn</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Quản lý tất cả database instances</p>
                    </div>
                    <div class="relative w-full sm:w-auto">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                        <input type="text" placeholder="Tìm kiếm database..."
                            class="w-full sm:w-64 pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors">
                    </div>
                </div>

                {{-- Danh sách Database Cards --}}
                <div class="grid gap-4">

                    {{-- DB Card 1: PostgreSQL - Active --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow p-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center shadow-sm shrink-0">
                                    <x-icons.postgresql class="w-9 h-9" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white">ndh_ecommerce_prod
                                        </h3>
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Hoạt động
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">PostgreSQL · Gói Pro · Tạo
                                        15/03/2026</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 sm:gap-3">
                                <button @click="showConnectionModal = true"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-primary bg-primary/5 hover:bg-primary/10 dark:bg-primary/10 dark:hover:bg-primary/20 transition-colors">
                                    <span class="material-symbols-outlined text-base">link</span>
                                    Kết nối
                                </button>
                                <button
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                    <span class="material-symbols-outlined text-base">lock_reset</span>
                                    Reset Pass
                                </button>
                                <button
                                    class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </div>
                        </div>
                        {{-- Thanh dung lượng --}}
                        <div
                            class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Dung lượng</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">85 MB / 500 MB</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Connections</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">8 / 20</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Port</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">5432</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Hết hạn</p>
                                <p class="text-sm font-semibold text-amber-500">15/04/2026</p>
                            </div>
                        </div>
                    </div>

                    {{-- DB Card 2: MySQL - Active --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow p-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center shadow-sm shrink-0">
                                    <x-icons.mysql class="w-9 h-9" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white">ndh_blog_dev</h3>
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Hoạt động
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">MySQL · Gói Free · Tạo
                                        20/03/2026</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 sm:gap-3">
                                <button
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-primary bg-primary/5 hover:bg-primary/10 dark:bg-primary/10 dark:hover:bg-primary/20 transition-colors">
                                    <span class="material-symbols-outlined text-base">link</span>
                                    Kết nối
                                </button>
                                <button
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                    <span class="material-symbols-outlined text-base">lock_reset</span>
                                    Reset Pass
                                </button>
                                <button
                                    class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </div>
                        </div>
                        <div
                            class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Dung lượng</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">12 MB / 50 MB</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Connections</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">2 / 5</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Port</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">3306</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Hết hạn</p>
                                <p class="text-sm font-semibold text-slate-500">Không giới hạn</p>
                            </div>
                        </div>
                    </div>

                    {{-- DB Card 3: PostgreSQL - Provisioning --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm p-5 opacity-80">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center shadow-sm shrink-0 animate-pulse">
                                    <x-icons.postgresql class="w-9 h-9" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white">ndh_analytics</h3>
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                                            <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4" fill="none"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            Đang khởi tạo...
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">PostgreSQL · Gói Pro · Tạo
                                        vừa xong</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- TAB: API KEYS --}}
            {{-- ============================================ --}}
            <div x-show="activeTab === 'api-keys'" x-cloak>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">API Keys</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Quản lý API keys để truy cập qua SDK</p>
                    </div>
                    <button
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-lg">add</span>
                        Tạo Key mới
                    </button>
                </div>

                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-700">
                                <th
                                    class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Tên</th>
                                <th
                                    class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Key</th>
                                <th
                                    class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Sử dụng lần cuối</th>
                                <th
                                    class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Trạng thái</th>
                                <th class="px-5 py-3.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="px-5 py-4 text-sm font-medium text-slate-900 dark:text-white">Production Key</td>
                                <td class="px-5 py-4">
                                    <code
                                        class="text-xs bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded font-mono text-slate-600 dark:text-slate-300">ndh_••••••••a1b2c3d4</code>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-500 dark:text-slate-400">2 giờ trước</td>
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">Kích
                                        hoạt</span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button
                                        class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="px-5 py-4 text-sm font-medium text-slate-900 dark:text-white">Development Key
                                </td>
                                <td class="px-5 py-4">
                                    <code
                                        class="text-xs bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded font-mono text-slate-600 dark:text-slate-300">ndh_••••••••e5f6g7h8</code>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-500 dark:text-slate-400">5 ngày trước</td>
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">Kích
                                        hoạt</span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button
                                        class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- TAB: BẢNG GIÁ (Component chung) --}}
            {{-- ============================================ --}}
            <div x-show="activeTab === 'pricing'" x-cloak>
                <x-app.cloud-plan-pricing />
            </div>
        </main>

        {{-- ═══════════════════════════════════════════ --}}
        {{-- MODAL: TẠO DATABASE MỚI --}}
        {{-- ═══════════════════════════════════════════ --}}
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showCreateModal = false"></div>
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-100 dark:border-slate-700 p-6"
                @click.away="showCreateModal = false">
                <button @click="showCreateModal = false"
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-1">Tạo Database mới</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Chọn engine và gói dịch vụ phù hợp</p>

                {{-- Chọn Engine --}}
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 block">Database Engine</label>
                <div class="grid grid-cols-2 gap-3 mb-5">
                    <button @click="selectedEngine = 'postgresql'"
                        :class="selectedEngine === 'postgresql' ? 'border-primary bg-primary/5 dark:bg-primary/10 ring-2 ring-primary/30' : 'border-slate-200 dark:border-slate-600 hover:border-slate-300'"
                        class="flex items-center gap-3 p-4 rounded-xl border-2 transition-all">
                        <div
                            class="h-10 w-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                            <x-icons.postgresql class="w-8 h-8" />
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">PostgreSQL</p>
                            <p class="text-xs text-slate-500">Port 5432</p>
                        </div>
                    </button>
                    <button @click="selectedEngine = 'mysql'"
                        :class="selectedEngine === 'mysql' ? 'border-primary bg-primary/5 dark:bg-primary/10 ring-2 ring-primary/30' : 'border-slate-200 dark:border-slate-600 hover:border-slate-300'"
                        class="flex items-center gap-3 p-4 rounded-xl border-2 transition-all">
                        <div
                            class="h-10 w-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                            <x-icons.mysql class="w-8 h-8" />
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">MySQL</p>
                            <p class="text-xs text-slate-500">Port 3306</p>
                        </div>
                    </button>
                </div>

                {{-- Tên database --}}
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 block">Tên Database</label>
                <div class="flex items-center gap-0 mb-5">
                    <span
                        class="bg-slate-100 dark:bg-slate-700 border border-r-0 border-slate-200 dark:border-slate-600 px-3 py-2.5 rounded-l-xl text-sm text-slate-500 font-mono">ndh_</span>
                    <input type="text" placeholder="ten_database"
                        class="flex-1 border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2.5 rounded-r-xl text-sm font-mono text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary/30 focus:border-primary">
                </div>

                {{-- Chọn Plan --}}
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 block">Gói dịch vụ</label>
                <div class="space-y-2 mb-6">
                    <template
                        x-for="plan in [{id:'free',name:'Free',price:'0đ',desc:'50MB · 5 conn'},{id:'pro',name:'Pro',price:'50.000đ',desc:'500MB · 20 conn'},{id:'team',name:'Team',price:'150.000đ',desc:'5GB · 50 conn'}]"
                        :key="plan.id">
                        <button @click="selectedPlan = plan.id"
                            :class="selectedPlan === plan.id ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-slate-200 dark:border-slate-600'"
                            class="w-full flex items-center justify-between p-3 rounded-xl border-2 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center"
                                    :class="selectedPlan === plan.id ? 'border-primary' : 'border-slate-300 dark:border-slate-500'">
                                    <div class="w-2 h-2 rounded-full bg-primary" x-show="selectedPlan === plan.id"></div>
                                </div>
                                <div class="text-left">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white"
                                        x-text="plan.name"></span>
                                    <span class="text-xs text-slate-500 ml-2" x-text="plan.desc"></span>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-primary" x-text="plan.price + '/tháng'"></span>
                        </button>
                    </template>
                </div>

                <button
                    class="w-full py-3 rounded-xl bg-primary text-white font-semibold hover:bg-primary/90 transition-colors shadow-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-lg">rocket_launch</span>
                    Tạo Database
                </button>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════ --}}
        {{-- MODAL: CHI TIẾT KẾT NỐI --}}
        {{-- ═══════════════════════════════════════════ --}}
        <div x-show="showConnectionModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showConnectionModal = false"></div>
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-100 dark:border-slate-700 p-6"
                @click.away="showConnectionModal = false">
                <button @click="showConnectionModal = false"
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
                <div class="flex items-center gap-3 mb-5">
                    <div class="h-10 w-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                        <x-icons.postgresql class="w-8 h-8" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">ndh_ecommerce_prod</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Thông tin kết nối PostgreSQL</p>
                    </div>
                </div>

                <div class="space-y-3">
                    {{-- Các trường kết nối --}}
                    @foreach([
                            ['label' => 'Host', 'value' => 'db.ndhshop.com', 'field' => 'host'],
                            ['label' => 'Port', 'value' => '5432', 'field' => 'port'],
                            ['label' => 'Database', 'value' => 'ndh_ecommerce_prod', 'field' => 'dbname'],
                            ['label' => 'Username', 'value' => 'ndh_user_abc123', 'field' => 'user'],
                            ['label' => 'Password', 'value' => '••••••••••••', 'field' => 'pass'],
                        ] as $item)
                        <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-700/50 rounded-xl px-4 py-3">
                            <div>

                                                           <p class="text-xs text-slate-500 dark:text-slate-400">{{ $item['label'] }}</p>
                                <p class="text-sm font-mono font-medium text-slate-900 dark:text-white">{{ $item['value'] }}</p>
                            </div>
                            <button @click="copyToClipboard('{{ $item['value'] }}', '{{ $item['field'] }}')"
                                class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                                <span class="material-symbols-outlined text-base" :class="copiedField === '{{ $item['field'] }}' ? 'text-emerald-500' : 'text-slate-400'"
                                    x-text="copiedField === '{{ $item['field'] }}' ? 'check' : 'content_copy'"></span>
                            </button>
                        </div>
                    @endforeach
                </div>


                {{-- Connection String --}}

                                           <div class="mt-4 bg-slate-900 dark:bg-slate-950 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs text-slate-400 font-medium">Connection String</p>
                        <
                       button @click="copyToClipboard('postgresql://ndh_user_abc123:****@db.ndhshop.com:5432/ndh_ecommerce_prod', 'connstr')"
                            class="text-xs text-primary hover:text-primary/80 font-medium flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm" x-text="copiedField === 'connstr' ? 'check' : 'content_copy'"></span>
                            <span x-text="copiedField === 'connstr' ? 'Đã sao chép!' : 'Sao chép'"></span>
                        </button>
                    </div>
                    <code class="text-sm text-emerald-400 font-mono break-all leading-relaxed">postgresql://ndh_user_abc123:****@db.ndhshop.com:5432/ndh_ecommerce_prod</code>
                </div>
            </div>
        </div>
    </div>
@endsection