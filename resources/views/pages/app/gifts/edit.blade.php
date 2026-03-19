@extends('layouts.app.app-layout')
@section('content')
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        {{-- Breadcrumb & Tiêu đề --}}
        <div class="mb-8">
            <a href="{{ route('app.gifts.my-gifts') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-primary transition-colors mb-4">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Quay lại Quà tặng của tôi
            </a>
            <div class="flex items-center gap-4">
                <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white">
                    Chỉnh sửa thiệp: <span class="text-primary">{{ $template->name }}</span>
                </h1>
                <span class="px-3 py-1 text-xs font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                    {{ $template->category_label }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Cột Form Nhập Liệu --}}
            <div class="lg:col-span-5 xl:col-span-4 bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-2xl shadow-sm overflow-hidden sticky top-24">
                <div class="bg-primary/5 border-b border-primary/10 p-5">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">edit_document</span>
                        Nội dung thiệp
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Sửa đổi thông tin bên dưới để cập nhật món quà của bạn.</p>
                </div>

                <form method="POST" action="{{ route('app.gifts.update', $gift->share_code) }}" class="p-5 flex flex-col gap-5">
                    @csrf
                    @method('PUT')
                    
                    {{-- OG Meta: Tiêu đề link --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center gap-1">
                            Tiêu đề Link (SEO) <span class="text-xs font-normal text-slate-400 ml-auto">(Tùy chọn)</span>
                        </label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $gift->meta_title) }}" placeholder="VD: Gửi tặng Vợ yêu nhân ngày 8/3..."
                            class="w-full bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors placeholder:text-slate-400">
                        @error('meta_title') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <hr class="border-slate-100 dark:border-border-dark">

                    {{-- Dynamic Fields từ Schema --}}
                    @foreach($template->getSchemaFields() as $field)
                        @php
                            $inputName = "data[{$field['key']}]";
                            $oldValue = old("data.{$field['key']}", $gift->page_data[$field['key']] ?? '');
                            $isRequired = $field['required'] ?? false;
                            $maxLength = $field['maxLength'] ?? null;
                            $type = $field['type'] ?? 'text';
                        @endphp
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center gap-1">
                                {{ $field['label'] ?? $field['key'] }}
                                @if($isRequired) <span class="text-rose-500">*</span> @endif
                            </label>
                            
                            @if(in_array($type, ['textarea', 'image', 'url']))
                                <textarea name="{{ $inputName }}" rows="{{ $type === 'textarea' ? '3' : '2' }}" {{ $isRequired ? 'required' : '' }} {{ $maxLength ? "maxlength={$maxLength}" : '' }}
                                    class="w-full bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors resize-none placeholder:text-slate-400"
                                    placeholder="{{ $type === 'image' || $type === 'url' ? 'Nhập danh sách link ' . ($type == 'image' ? 'ảnh' : 'URL') . ' (Mỗi link 1 dòng)' : 'Nhập ' . mb_strtolower($field['label'] ?? '') . ' (Mỗi nội dung 1 dòng)' }}">{{ $oldValue }}</textarea>
                                <p class="text-[11px] text-slate-500 mt-1">
                                    @if($type === 'image') Gợi ý: Upload ảnh lên Facebook/Drive rồi copy link ảnh dán vào đây. <br>@endif
                                    <strong>Mỗi dòng</strong> sẽ được tự động lưu thành <code>{{ $field['key'] }}1</code>, <code>{{ $field['key'] }}2</code>...
                                    @if(isset($field['limit'])) <span class="text-rose-500 font-bold ml-1">(Tối đa: {{ $field['limit'] }} dòng)</span> @endif
                                </p>

                            @else
                                <input type="text" name="{{ $inputName }}" value="{{ $oldValue }}" {{ $isRequired ? 'required' : '' }} {{ $maxLength ? "maxlength={$maxLength}" : '' }}
                                    class="w-full bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors placeholder:text-slate-400"
                                    placeholder="Nhập {{ strtolower($field['label']) }}...">
                            @endif
                            
                            @error("data.{$field['key']}") <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endforeach

                    {{-- Submit Button --}}
                    <div class="mt-4 pt-5 border-t border-slate-100 dark:border-border-dark">
                        <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-primary to-purple-600 hover:from-primary/90 hover:to-purple-600/90 text-white font-bold rounded-xl shadow-lg shadow-primary/30 transition-all transform hover:-translate-y-0.5">
                            <span class="material-symbols-outlined">save</span>
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>

            {{-- Cột Preview Demo (Ảnh Thumbnail lớn) --}}
            <div class="lg:col-span-7 xl:col-span-8">
                <div class="bg-slate-100 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-border-dark overflow-hidden flex flex-col items-center justify-center min-h-[500px] p-8">
                    @if($template->thumbnail)
                        <img src="{{ $template->thumbnail }}" alt="{{ $template->name }}" class="max-w-full h-auto rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700">
                    @else
                        <div class="flex flex-col items-center gap-4 text-slate-400">
                            <span class="material-symbols-outlined text-[64px]">laptop_mac</span>
                            <p class="font-medium">Mẫu này chưa có ảnh xem trước</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
