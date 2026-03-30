@extends('layouts.app.app-layout')

@section('content')
<div x-data="{
    activeTab: 'buckets',
    showCreateBucketModal: false,
    showUploadModal: false,
    showObjectsModal: false,
    selectedBucket: null,
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
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg shadow-violet-500/20">
                    <span class="material-symbols-outlined text-white text-2xl">cloud_upload</span>
                </div>
                <div>
                    <h2 class="text-slate-900 dark:text-white text-base font-bold">Cloud Storage</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-xs">S3-like Object Storage</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-primary">4</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Buckets</p>
                </div>
                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-emerald-500">127</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Files</p>
                </div>
                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-amber-500">2</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Public</p>
                </div>
            </div>
        </div>

        {{-- Menu điều hướng --}}
        <nav class="bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-700 shadow-sm">
            <div class="flex flex-col p-2 gap-1">
                <button @click="activeTab = 'buckets'" :class="activeTab === 'buckets' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left w-full group">
                    <span class="material-symbols-outlined" :class="activeTab === 'buckets' ? 'text-primary' : 'text-slate-400 group-hover:text-primary'">folder_open</span>
                    <span class="font-medium text-sm">Buckets</span>
                    <span class="ml-auto bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full">4</span>
                </button>
                <button @click="activeTab = 'api-keys'" :class="activeTab === 'api-keys' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left w-full group">
                    <span class="material-symbols-outlined" :class="activeTab === 'api-keys' ? 'text-primary' : 'text-slate-400 group-hover:text-primary'">key</span>
                    <span class="font-medium text-sm">API Keys</span>
                    <span class="ml-auto bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full">2</span>
                </button>
                <button @click="activeTab = 'pricing'" :class="activeTab === 'pricing' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left w-full group">
                    <span class="material-symbols-outlined" :class="activeTab === 'pricing' ? 'text-primary' : 'text-slate-400 group-hover:text-primary'">payments</span>
                    <span class="font-medium text-sm">Bảng giá</span>
                </button>
            </div>
            <div class="border-t border-slate-100 dark:border-slate-700 p-2 mt-1">
                <button @click="showCreateBucketModal = true"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors font-medium text-sm shadow-sm">
                    <span class="material-symbols-outlined text-lg">create_new_folder</span>
                    Tạo Bucket mới
                </button>
            </div>
        </nav>

        {{-- Quota hiện tại --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-100 dark:border-slate-700 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Quota hiện tại</h3>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-slate-500 dark:text-slate-400">Buckets</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300">4 / 10</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                        <div class="bg-primary h-1.5 rounded-full transition-all" style="width: 40%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-slate-500 dark:text-slate-400">Dung lượng</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300">1.8 GB / 5 GB</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                        <div class="bg-violet-500 h-1.5 rounded-full transition-all" style="width: 36%"></div>
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
        {{-- TAB: BUCKETS --}}
        {{-- ============================================ --}}
        <div x-show="activeTab === 'buckets'" x-cloak>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Buckets của bạn</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Quản lý thùng chứa và file lưu trữ</p>
                </div>
                <div class="relative w-full sm:w-auto">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                    <input type="text" placeholder="Tìm kiếm bucket..."
                        class="w-full sm:w-64 pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors">
                </div>
            </div>

            {{-- Danh sách Bucket Cards --}}
            <div class="grid gap-4">

                {{-- Bucket 1: Public, nhiều file --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl bg-violet-50 dark:bg-violet-500/10 flex items-center justify-center shadow-sm shrink-0">
                                <span class="material-symbols-outlined text-violet-500 text-2xl">folder_open</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">ndh-product-images</h3>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                        <span class="material-symbols-outlined text-xs">public</span>
                                        Public
                                    </span>
                                </div>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">85 files · Tạo 10/03/2026</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-3">
                            <button @click="showObjectsModal = true; selectedBucket = 'ndh-product-images'" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-primary bg-primary/5 hover:bg-primary/10 dark:bg-primary/10 dark:hover:bg-primary/20 transition-colors">
                                <span class="material-symbols-outlined text-base">visibility</span>
                                Xem files
                            </button>
                            <button @click="showUploadModal = true" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <span class="material-symbols-outlined text-base">upload</span>
                                Upload
                            </button>
                            <button class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Dung lượng</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">1.2 GB / 5 GB</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Số files</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">85</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Truy cập</p>
                            <p class="text-sm font-semibold text-emerald-500">Public</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">CDN URL</p>
                            <button @click="copyToClipboard('https://cdn.ndhshop.com/ndh-product-images', 'cdn1')" class="text-sm font-semibold text-primary hover:underline flex items-center gap-1">
                                Sao chép
                                <span class="material-symbols-outlined text-xs" x-text="copiedField === 'cdn1' ? 'check' : 'content_copy'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Bucket 2: Private --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center shadow-sm shrink-0">
                                <span class="material-symbols-outlined text-amber-500 text-2xl">folder_special</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">ndh-user-uploads</h3>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 dark:bg-slate-600 dark:text-slate-300">
                                        <span class="material-symbols-outlined text-xs">lock</span>
                                        Private
                                    </span>
                                </div>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">32 files · Tạo 12/03/2026</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-3">
                            <button @click="showObjectsModal = true; selectedBucket = 'ndh-user-uploads'" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-primary bg-primary/5 hover:bg-primary/10 dark:bg-primary/10 dark:hover:bg-primary/20 transition-colors">
                                <span class="material-symbols-outlined text-base">visibility</span>
                                Xem files
                            </button>
                            <button @click="showUploadModal = true" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <span class="material-symbols-outlined text-base">upload</span>
                                Upload
                            </button>
                            <button class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Dung lượng</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">420 MB / 5 GB</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Số files</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">32</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Truy cập</p>
                            <p class="text-sm font-semibold text-slate-500">Private</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Signed URL</p>
                            <p class="text-sm font-semibold text-slate-500">Yêu cầu API Key</p>
                        </div>
                    </div>
                </div>

                {{-- Bucket 3: Public, backup --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl bg-sky-50 dark:bg-sky-500/10 flex items-center justify-center shadow-sm shrink-0">
                                <span class="material-symbols-outlined text-sky-500 text-2xl">cloud_done</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">ndh-static-assets</h3>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                        <span class="material-symbols-outlined text-xs">public</span>
                                        Public
                                    </span>
                                </div>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">8 files · Tạo 20/03/2026</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-3">
                            <button class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-primary bg-primary/5 hover:bg-primary/10 dark:bg-primary/10 dark:hover:bg-primary/20 transition-colors">
                                <span class="material-symbols-outlined text-base">visibility</span>
                                Xem files
                            </button>
                            <button class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <span class="material-symbols-outlined text-base">upload</span>
                                Upload
                            </button>
                            <button class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Dung lượng</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">156 MB / 5 GB</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Số files</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">8</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Truy cập</p>
                            <p class="text-sm font-semibold text-emerald-500">Public</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">CDN URL</p>
                            <button @click="copyToClipboard('https://cdn.ndhshop.com/ndh-static-assets', 'cdn3')" class="text-sm font-semibold text-primary hover:underline flex items-center gap-1">
                                Sao chép
                                <span class="material-symbols-outlined text-xs" x-text="copiedField === 'cdn3' ? 'check' : 'content_copy'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Bucket 4: Private, backup DB --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center shadow-sm shrink-0">
                                <span class="material-symbols-outlined text-rose-500 text-2xl">backup</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">ndh-db-backups</h3>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 dark:bg-slate-600 dark:text-slate-300">
                                        <span class="material-symbols-outlined text-xs">lock</span>
                                        Private
                                    </span>
                                </div>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">2 files · Tạo 25/03/2026</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-3">
                            <button class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-primary bg-primary/5 hover:bg-primary/10 dark:bg-primary/10 dark:hover:bg-primary/20 transition-colors">
                                <span class="material-symbols-outlined text-base">visibility</span>
                                Xem files
                            </button>
                            <button class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <span class="material-symbols-outlined text-base">upload</span>
                                Upload
                            </button>
                            <button class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Dung lượng</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">45 MB / 5 GB</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Số files</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">2</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Truy cập</p>
                            <p class="text-sm font-semibold text-slate-500">Private</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Loại file</p>
                            <p class="text-sm font-semibold text-slate-500">.sql.gz</p>
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
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Quản lý API keys để truy cập Storage qua SDK</p>
                </div>
                <button class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-lg">add</span>
                    Tạo Key mới
                </button>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-700">
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tên</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Key</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Quyền</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sử dụng lần cuối</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-5 py-4 text-sm font-medium text-slate-900 dark:text-white">Storage Full Access</td>
                            <td class="px-5 py-4">
                                <code class="text-xs bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded font-mono text-slate-600 dark:text-slate-300">stg_••••••••x9k2m4n7</code>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">Read/Write</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-500 dark:text-slate-400">30 phút trước</td>
                            <td class="px-5 py-4 text-right">
                                <button class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-5 py-4 text-sm font-medium text-slate-900 dark:text-white">CDN Read Only</td>
                            <td class="px-5 py-4">
                                <code class="text-xs bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded font-mono text-slate-600 dark:text-slate-300">stg_••••••••p3q5r8s1</code>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400">Read Only</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-500 dark:text-slate-400">2 ngày trước</td>
                            <td class="px-5 py-4 text-right">
                                <button class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Code snippet --}}
            <div class="mt-6 bg-slate-900 dark:bg-slate-950 rounded-xl p-5 border border-slate-700">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold text-slate-300">Ví dụ sử dụng SDK</p>
                    <button @click="copyToClipboard('...', 'sdk')" class="text-xs text-primary hover:text-primary/80 font-medium flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm" x-text="copiedField === 'sdk' ? 'check' : 'content_copy'"></span>
                        <span x-text="copiedField === 'sdk' ? 'Đã sao chép!' : 'Sao chép'"></span>
                    </button>
                </div>
<pre class="text-sm text-emerald-400 font-mono leading-relaxed overflow-x-auto"><code><span class="text-slate-500">// Upload file qua API</span>
<span class="text-sky-400">const</span> response = <span class="text-sky-400">await</span> fetch(<span class="text-amber-300">'https://api.ndhshop.com/v1/storage/buckets/ndh-product-images/objects'</span>, {
  method: <span class="text-amber-300">'POST'</span>,
  headers: { <span class="text-amber-300">'X-API-Key'</span>: <span class="text-amber-300">'stg_your_api_key'</span> },
  body: formData
});</code></pre>
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
    {{-- MODAL: TẠO BUCKET MỚI --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="showCreateBucketModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showCreateBucketModal = false"></div>
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-100 dark:border-slate-700 p-6"
            @click.away="showCreateBucketModal = false">
            <button @click="showCreateBucketModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-1">Tạo Bucket mới</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Tạo thùng chứa để lưu trữ file</p>

            {{-- Tên bucket --}}
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 block">Tên Bucket</label>
            <input type="text" placeholder="vd: my-project-assets"
                class="w-full border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 rounded-xl text-sm font-mono text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary/30 focus:border-primary mb-5">

            {{-- Quyền truy cập --}}
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 block">Quyền truy cập</label>
            <div class="grid grid-cols-2 gap-3 mb-5">
                <button class="flex items-center gap-3 p-4 rounded-xl border-2 border-primary bg-primary/5 dark:bg-primary/10 ring-2 ring-primary/30 transition-all">
                    <span class="material-symbols-outlined text-primary">lock</span>
                    <div class="text-left">
                        <p class="text-sm font-bold text-slate-900 dark:text-white">Private</p>
                        <p class="text-xs text-slate-500">Cần API Key</p>
                    </div>
                </button>
                <button class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-200 dark:border-slate-600 hover:border-slate-300 transition-all">
                    <span class="material-symbols-outlined text-emerald-500">public</span>
                    <div class="text-left">
                        <p class="text-sm font-bold text-slate-900 dark:text-white">Public</p>
                        <p class="text-xs text-slate-500">Truy cập qua CDN</p>
                    </div>
                </button>
            </div>

            {{-- Loại file cho phép --}}
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 block">Loại file cho phép</label>
            <div class="flex flex-wrap gap-2 mb-6">
                <span class="px-3 py-1.5 rounded-lg bg-primary/10 text-primary text-xs font-medium border border-primary/20">Ảnh (jpg, png, webp)</span>
                <span class="px-3 py-1.5 rounded-lg bg-primary/10 text-primary text-xs font-medium border border-primary/20">Video (mp4, webm)</span>
                <span class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-500 text-xs font-medium border border-slate-200 dark:border-slate-600 cursor-pointer hover:border-primary hover:text-primary transition-colors">PDF</span>
                <span class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-500 text-xs font-medium border border-slate-200 dark:border-slate-600 cursor-pointer hover:border-primary hover:text-primary transition-colors">Tất cả</span>
            </div>

            <button class="w-full py-3 rounded-xl bg-primary text-white font-semibold hover:bg-primary/90 transition-colors shadow-sm flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">create_new_folder</span>
                Tạo Bucket
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- MODAL: UPLOAD FILE --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="showUploadModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showUploadModal = false"></div>
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-100 dark:border-slate-700 p-6"
            @click.away="showUploadModal = false">
            <button @click="showUploadModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-1">Upload File</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Tải file lên bucket</p>

            {{-- Vùng kéo thả --}}
            <div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-10 text-center hover:border-primary hover:bg-primary/5 dark:hover:bg-primary/5 transition-colors cursor-pointer mb-5">
                <span class="material-symbols-outlined text-4xl text-slate-400 mb-3 block">cloud_upload</span>
                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Kéo thả file vào đây hoặc</p>
                <button class="mt-2 px-4 py-2 rounded-lg bg-primary/10 text-primary text-sm font-medium hover:bg-primary/20 transition-colors">Chọn file từ máy</button>
                <p class="text-xs text-slate-400 mt-3">Tối đa 50 MB / file · jpg, png, webp, pdf, mp4</p>
            </div>

            {{-- Danh sách file sắp upload (demo) --}}
            <div class="space-y-2 mb-5">
                <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-700/50 rounded-lg px-4 py-3">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-violet-500 text-lg">image</span>
                        <div>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">product-banner.webp</p>
                            <p class="text-xs text-slate-500">2.4 MB</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-emerald-500 font-medium">Sẵn sàng</span>
                        <button class="text-slate-400 hover:text-red-500 transition-colors">
                            <span class="material-symbols-outlined text-base">close</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Đường dẫn (key) --}}
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 block">Đường dẫn (tùy chọn)</label>
            <input type="text" placeholder="vd: images/banners/"
                class="w-full border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 rounded-xl text-sm font-mono text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary/30 focus:border-primary mb-5">

            <button class="w-full py-3 rounded-xl bg-primary text-white font-semibold hover:bg-primary/90 transition-colors shadow-sm flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">upload</span>
                Upload file
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- MODAL: XEM FILES TRONG BUCKET --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="showObjectsModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showObjectsModal = false"></div>
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl border border-slate-100 dark:border-slate-700 p-6 max-h-[85vh] overflow-y-auto"
            @click.away="showObjectsModal = false">
            <button @click="showObjectsModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>
            <div class="flex items-center gap-3 mb-5">
                <div class="h-10 w-10 rounded-xl bg-violet-50 dark:bg-violet-500/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-violet-500">folder_open</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white" x-text="selectedBucket || 'Bucket'"></h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Danh sách file trong bucket</p>
                </div>
            </div>

            {{-- Bảng danh sách file --}}
            <div class="rounded-xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-700/50">
                            <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500 uppercase">File</th>
                            <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500 uppercase">Loại</th>
                            <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500 uppercase">Kích thước</th>
                            <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500 uppercase">Ngày tạo</th>
                            <th class="px-4 py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach([
                            ['name' => 'images/hero-banner.webp', 'type' => 'image/webp', 'size' => '1.8 MB', 'date' => '28/03/2026', 'icon' => 'image', 'color' => 'text-violet-500'],
                            ['name' => 'images/product-01.jpg', 'type' => 'image/jpeg', 'size' => '420 KB', 'date' => '27/03/2026', 'icon' => 'image', 'color' => 'text-violet-500'],
                            ['name' => 'images/product-02.png', 'type' => 'image/png', 'size' => '680 KB', 'date' => '27/03/2026', 'icon' => 'image', 'color' => 'text-violet-500'],
                            ['name' => 'docs/terms.pdf', 'type' => 'application/pdf', 'size' => '240 KB', 'date' => '25/03/2026', 'icon' => 'picture_as_pdf', 'color' => 'text-red-500'],
                            ['name' => 'videos/intro.mp4', 'type' => 'video/mp4', 'size' => '12.5 MB', 'date' => '20/03/2026', 'icon' => 'movie', 'color' => 'text-sky-500'],
                        ] as $file)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined {{ $file['color'] }} text-lg">{{ $file['icon'] }}</span>
                                    <span class="text-sm font-mono text-slate-900 dark:text-white">{{ $file['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $file['type'] }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-300">{{ $file['size'] }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $file['date'] }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1 justify-end">
                                    <button class="p-1.5 rounded-lg text-primary hover:bg-primary/10 transition-colors" title="Sao chép URL">
                                        <span class="material-symbols-outlined text-base">content_copy</span>
                                    </button>
                                    <button class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Tải xuống">
                                        <span class="material-symbols-outlined text-base">download</span>
                                    </button>
                                    <button class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors" title="Xóa">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection