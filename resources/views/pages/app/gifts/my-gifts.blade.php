@extends('layouts.app.app-layout')
@section('content')
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        {{-- Tiêu đề --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white">
                    Quà tặng <span class="text-primary">của tôi</span>
                </h1>
                <p class="text-slate-500 mt-1">Danh sách các trang quà tặng bạn đã tạo để gửi cho bạn bè, người thân.</p>
            </div>
            <a href="{{ route('app.gifts.templates') }}" class="flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-primary/20">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Tạo thiệp mới
            </a>
        </div>

        {{-- Session messages are handled via toast in app-layout.blade.php --}}

        {{-- Bảng danh sách --}}
        <div class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-background-dark/50 border-b border-slate-200 dark:border-border-dark">
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-12 text-center">STT</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider min-w-[200px]">Thông tin thiệp</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Gói</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">Lượt xem</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Trạng thái</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-border-dark">
                        @forelse($gifts as $gift)
                            @php
                                $template = $gift->template;
                                $isExpired = $gift->isExpired();
                                $isDraft = $gift->status === \App\Models\GiftPage::STATUS_DRAFT;
                                $isActive = $gift->status === \App\Models\GiftPage::STATUS_ACTIVE && !$isExpired;
                                $shareUrl = $gift->share_url;
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-background-dark/30 transition-colors group">
                                <td class="p-4 text-center text-sm font-medium text-slate-500">{{ $loop->iteration }}</td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-lg bg-slate-100 dark:bg-slate-800 overflow-hidden shrink-0 border border-slate-200 dark:border-border-dark">
                                            @if($gift->meta_image)
                                                <img src="{{ $gift->meta_image }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                    <span class="material-symbols-outlined text-[20px]">image</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-slate-900 dark:text-white text-sm font-bold line-clamp-1" title="{{ $gift->meta_title }}">{{ $gift->meta_title }}</p>
                                            @if($gift->share_code)
                                                <div class="flex items-center gap-1 mt-0.5" x-data="{ copied: false }">
                                                    <code class="text-xs text-primary bg-primary/10 px-1.5 py-0.5 rounded">{{ $gift->share_code }}</code>
                                                    <button @click="navigator.clipboard.writeText('{{ $shareUrl }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                                        class="text-slate-400 hover:text-primary transition-colors" title="Copy Link">
                                                        <span class="material-symbols-outlined text-[14px]" x-text="copied ? 'check' : 'content_copy'">content_copy</span>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400 italic">Chưa có link (draft)</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold
                                        {{ $gift->isPremium() ? 'bg-primary/10 text-primary border border-primary/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                                        @if($gift->isPremium()) ⭐ @endif
                                        {{ $gift->plan_label }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center gap-1 text-sm font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        {{ number_format($gift->view_count) }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    @if($isDraft)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                            <span class="size-1.5 rounded-full bg-amber-500"></span> Nháp
                                        </span>
                                    @elseif($isExpired || $gift->status === \App\Models\GiftPage::STATUS_EXPIRED)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-500/10 text-rose-500 border border-rose-500/20">
                                            <span class="size-1.5 rounded-full bg-rose-500"></span> Hết hạn
                                        </span>
                                    @elseif($isActive)
                                        <div class="flex flex-col items-start gap-1.5">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-500 border border-emerald-500/20" 
                                                title="{{ $gift->expires_at ? 'Đến: ' . $gift->expires_at->format('d/m/Y') : 'Vĩnh viễn' }}">
                                                <span class="size-1.5 rounded-full bg-emerald-500"></span> Đang chạy
                                            </span>
                                            @if($gift->isPremium() && $gift->canBeEdited())
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20 whitespace-nowrap" title="Thời gian còn lại để chỉnh sửa thiệp Premium">
                                                    <span class="material-symbols-outlined text-[12px]">timer</span>
                                                    Sửa: {{ $gift->edit_hours_left }}h
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-500/10 text-slate-500 border border-slate-500/20">
                                            <span class="size-1.5 rounded-full bg-slate-500"></span> {{ $gift->status_label }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Nút tiếp tục thanh toán (cho draft) --}}
                                        @if($isDraft)
                                            <a href="{{ route('app.gifts.choose-plan', $gift->unitcode) }}"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary hover:bg-primary/90 text-white text-xs font-bold rounded-lg transition-colors"
                                                title="Tiếp tục thanh toán">
                                                <span class="material-symbols-outlined text-[14px]">payments</span>
                                                Chọn gói
                                            </a>
                                        @endif

                                        {{-- Nút nâng cấp Premium (cho basic active) --}}
                                        @if($isActive && !$gift->isPremium())
                                            <form method="POST" action="{{ route('app.gifts.upgrade', $gift->share_code) }}" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Nâng cấp Premium với {{ number_format(\App\Models\GiftPage::PLAN_PRICES['premium'], 0, ',', '.') }}đ?')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-primary/10 to-purple-500/10 hover:from-primary/20 hover:to-purple-500/20 text-primary text-xs font-bold rounded-lg transition-colors border border-primary/20"
                                                    title="Nâng cấp Premium">
                                                    <span class="material-symbols-outlined text-[14px]">upgrade</span>
                                                    Nâng cấp
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Nút xem thử thiệp --}}
                                        @if($gift->share_code)
                                            <a href="{{ $shareUrl }}" target="_blank"
                                                class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                title="Xem thử thiệp">
                                                <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                            </a>
                                        @endif

                                        {{-- Nút sửa (draft hoặc premium còn hạn 72h) --}}
                                        @if($gift->canBeEdited())
                                            @php
                                                $editTitle = "Chỉnh sửa thiệp";
                                                if (!$isDraft && $gift->isPremium()) {
                                                    $editTitle = "Chỉnh sửa thiệp (Còn " . $gift->edit_hours_left . " giờ)";
                                                }
                                            @endphp
                                            <a href="{{ route('app.gifts.edit', $gift->unitcode) }}"
                                                class="p-1.5 text-slate-400 hover:text-amber-500 hover:bg-amber-500/10 rounded-lg transition-colors"
                                                title="{{ $editTitle }}">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </a>
                                        @endif  

                                        {{-- Nút xóa --}}
                                        <button x-data="{}" x-on:click.prevent="if(confirm('Bạn có chắc chắn muốn xóa trang quà tặng này không?')) { document.getElementById('delete-gift-{{ $gift->unitcode }}').submit(); }"
                                            class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 rounded-lg transition-colors"
                                            title="Xóa thiệp">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                        <form id="delete-gift-{{ $gift->unitcode }}" action="{{ route('app.gifts.destroy', $gift->unitcode) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-10 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="size-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-2">
                                            <span class="material-symbols-outlined text-[32px] text-slate-300 dark:text-slate-600">inventory_2</span>
                                        </div>
                                        <p class="text-slate-900 dark:text-white font-bold text-lg">Bạn chưa có món quà nào</p>
                                        <p class="text-slate-500 text-sm max-w-sm">Hãy vào kho giao diện, chọn một mẫu ưng ý và tạo món quà bất ngờ cho người thân ngay nhé.</p>
                                        <a href="{{ route('app.gifts.templates') }}" class="mt-4 inline-flex items-center gap-2 px-5 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-bold rounded-xl transition-all shadow-md">
                                            Khám phá mẫu thiệp
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($gifts->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-border-dark bg-slate-50 dark:bg-background-dark/30 flex justify-center">
                    {{ $gifts->links('pagination::tailwind') }}
                </div>
            @endif
        </div>
    </div>
@endsection
