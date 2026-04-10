{{-- Modal chỉnh sửa Gift Template --}}
@props(['categories'])

<div x-data="{
    templateData: {},
    activeTab: 'info',
    init() {
        window.addEventListener('open-edit-gift-template', (e) => {
            this.templateData = e.detail;
            this.activeTab = 'info';
            $dispatch('open-modal', 'edit-gift-template');
        });
    }
}" @open-edit-gift-template.window="templateData = $event.detail; activeTab = 'info'; $dispatch('open-modal', 'edit-gift-template')">

    <x-ui.modal name="edit-gift-template" maxWidth="2xl">
        <form method="POST" :action="'{{ route('admin.gift-templates.update', '__ID__') }}'.replace('__ID__', templateData.id)" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Chỉnh sửa Template</h2>

            {{-- Tabs --}}
            <div class="flex gap-1 border-b border-slate-200 dark:border-border-dark mb-4">
                <button type="button" @click="activeTab = 'info'"
                    :class="activeTab === 'info' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700'"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">Thông tin</button>
                <button type="button" @click="activeTab = 'code'"
                    :class="activeTab === 'code' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700'"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">Code</button>
                <button type="button" @click="activeTab = 'schema'"
                    :class="activeTab === 'schema' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700'"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">Schema</button>
            </div>

            {{-- Tab: Thông tin --}}
            <div x-show="activeTab === 'info'" class="flex flex-col gap-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tên mẫu *</label>
                        <input type="text" name="name" :value="templateData.name" required
                            class="w-full bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Slug *</label>
                        <input type="text" name="slug" :value="templateData.slug" required
                            class="w-full bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Chủ đề *</label>
                        <select name="category_id" required
                            class="w-full bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" :selected="templateData.category_id == {{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Giá (VND)</label>
                        <input type="number" name="price" :value="templateData.price || 0" min="0"
                            class="w-full bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">File ảnh thumbnail (Để trống nếu không thay đổi)</label>
                    <div class="flex items-center gap-4">
                        <img x-show="templateData.thumbnail" :src="templateData.thumbnail" class="w-16 h-16 object-cover rounded-lg border border-slate-200 dark:border-border-dark" alt="Thumbnail">
                        <input type="file" name="thumbnail" accept="image/*"
                            class="w-full bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg px-3 py-2 flex-1 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" :checked="templateData.is_active"
                            class="rounded border-slate-300 dark:border-border-dark text-primary focus:ring-0 w-4 h-4">
                        <span class="text-sm text-slate-700 dark:text-slate-300">Hoạt động</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_premium" value="1" :checked="templateData.is_premium"
                            class="rounded border-slate-300 dark:border-border-dark text-primary focus:ring-0 w-4 h-4">
                        <span class="text-sm text-slate-700 dark:text-slate-300">Premium</span>
                    </label>
                </div>
            </div>

            {{-- Tab: Code --}}
            <div x-show="activeTab === 'code'" class="flex flex-col gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">HTML Code *</label>
                    <textarea name="html_code" rows="10" required x-text="templateData.html_code"
                        class="w-full bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-mono"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">CSS Code</label>
                    <textarea name="css_code" rows="6" x-text="templateData.css_code"
                        class="w-full bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-mono"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">JavaScript Code</label>
                    <textarea name="js_code" rows="6" x-text="templateData.js_code"
                        class="w-full bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-mono"></textarea>
                </div>
            </div>

            {{-- Tab: Schema --}}
            <div x-show="activeTab === 'schema'" class="flex flex-col gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Schema JSON</label>
                    <textarea name="schema" rows="14" x-text="templateData.schema"
                        class="w-full bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-mono"></textarea>
                </div>
            </div>

            {{-- Nút submit --}}
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-slate-200 dark:border-border-dark">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    Hủy
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    Cập nhật
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>
