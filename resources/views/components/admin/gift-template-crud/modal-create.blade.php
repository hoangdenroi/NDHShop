{{-- Modal tạo Gift Template mới --}}
@props(['categories'])

<x-ui.modal name="create-gift-template" maxWidth="2xl">
    <form method="POST" action="{{ route('admin.gift-templates.store') }}" enctype="multipart/form-data" @class(['p-6']) x-data="{
        activeTab: 'info',
        name: '',
        slug: '',
        generateSlug() {
            this.slug = this.name.toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd').replace(/Đ/g, 'D')
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }
    }">
        @csrf

        <h2 @class(['text-lg', 'font-bold', 'text-slate-900', 'dark:text-white', 'mb-4'])>Thêm mẫu Template mới</h2>

        {{-- Tabs --}}
        <div @class(['flex', 'gap-1', 'border-b', 'border-slate-200', 'dark:border-border-dark', 'mb-4'])>
            <button type="button" @click="activeTab = 'info'"
                :class="activeTab === 'info' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700'"
                @class(['px-4', 'py-2', 'text-sm', 'font-medium', 'border-b-2', 'transition-colors'])>Thông tin</button>
            <button type="button" @click="activeTab = 'code'"
                :class="activeTab === 'code' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700'"
                @class(['px-4', 'py-2', 'text-sm', 'font-medium', 'border-b-2', 'transition-colors'])>Code</button>
            <button type="button" @click="activeTab = 'schema'"
                :class="activeTab === 'schema' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700'"
                @class(['px-4', 'py-2', 'text-sm', 'font-medium', 'border-b-2', 'transition-colors'])>Schema</button>
        </div>

        {{-- Tab: Thông tin cơ bản --}}
        <div x-show="activeTab === 'info'" @class(['flex', 'flex-col', 'gap-4'])>
            <div @class(['grid', 'grid-cols-1', 'md:grid-cols-2', 'gap-4'])>
                <div>
                    <label @class(['block', 'text-sm', 'font-medium', 'text-slate-700', 'dark:text-slate-300', 'mb-1'])>Tên mẫu *</label>
                    <input type="text" name="name" x-model="name" @input="generateSlug()" required
                        @class(['w-full', 'bg-slate-100', 'dark:bg-background-dark', 'border', 'border-slate-200', 'dark:border-border-dark', 'text-slate-700', 'dark:text-slate-300', 'text-sm', 'rounded-lg', 'px-3', 'py-2', 'focus:outline-none', 'focus:border-primary', 'focus:ring-1', 'focus:ring-primary'])>
                </div>
                <div>
                    <label @class(['block', 'text-sm', 'font-medium', 'text-slate-700', 'dark:text-slate-300', 'mb-1'])>Slug *</label>
                    <input type="text" name="slug" x-model="slug" required
                        @class(['w-full', 'bg-slate-100', 'dark:bg-background-dark', 'border', 'border-slate-200', 'dark:border-border-dark', 'text-slate-700', 'dark:text-slate-300', 'text-sm', 'rounded-lg', 'px-3', 'py-2', 'focus:outline-none', 'focus:border-primary', 'focus:ring-1', 'focus:ring-primary'])>
                </div>
            </div>
            <div @class(['grid', 'grid-cols-1', 'md:grid-cols-2', 'gap-4'])>
                <div>
                    <label @class(['block', 'text-sm', 'font-medium', 'text-slate-700', 'dark:text-slate-300', 'mb-1'])>Chủ đề *</label>
                    <select name="category_id" required
                        @class(['w-full', 'bg-slate-100', 'dark:bg-background-dark', 'border', 'border-slate-200', 'dark:border-border-dark', 'text-slate-700', 'dark:text-slate-300', 'text-sm', 'rounded-lg', 'px-3', 'py-2', 'focus:outline-none', 'focus:border-primary', 'focus:ring-1', 'focus:ring-primary'])>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label @class(['block', 'text-sm', 'font-medium', 'text-slate-700', 'dark:text-slate-300', 'mb-1'])>Giá (VND)</label>
                    <input type="number" name="price" value="0" min="0"
                        @class(['w-full', 'bg-slate-100', 'dark:bg-background-dark', 'border', 'border-slate-200', 'dark:border-border-dark', 'text-slate-700', 'dark:text-slate-300', 'text-sm', 'rounded-lg', 'px-3', 'py-2', 'focus:outline-none', 'focus:border-primary', 'focus:ring-1', 'focus:ring-primary'])>
                </div>
            </div>
            <div>
                <label @class(['block', 'text-sm', 'font-medium', 'text-slate-700', 'dark:text-slate-300', 'mb-1'])>File ảnh thumbnail</label>
                <input type="file" name="thumbnail" accept="image/*"
                    @class(['w-full', 'bg-slate-100', 'dark:bg-background-dark', 'border', 'border-slate-200', 'dark:border-border-dark', 'text-slate-700', 'dark:text-slate-300', 'text-sm', 'rounded-lg', 'px-3', 'py-2', 'focus:outline-none', 'focus:border-primary', 'focus:ring-1', 'focus:ring-primary'])>
            </div>
            <div @class(['flex', 'items-center', 'gap-6'])>
                <label @class(['flex', 'items-center', 'gap-2', 'cursor-pointer'])>
                    <input type="checkbox" name="is_active" value="1" checked
                        @class(['rounded', 'border-slate-300', 'dark:border-border-dark', 'text-primary', 'focus:ring-0', 'w-4', 'h-4'])>
                    <span @class(['text-sm', 'text-slate-700', 'dark:text-slate-300'])>Hoạt động</span>
                </label>
                <label @class(['flex', 'items-center', 'gap-2', 'cursor-pointer'])>
                    <input type="checkbox" name="is_premium" value="1"
                        @class(['rounded', 'border-slate-300', 'dark:border-border-dark', 'text-primary', 'focus:ring-0', 'w-4', 'h-4'])>
                    <span @class(['text-sm', 'text-slate-700', 'dark:text-slate-300'])>Premium (trả phí)</span>
                </label>
            </div>
        </div>

        {{-- Tab: Code Editor --}}
        <div x-show="activeTab === 'code'" @class(['flex', 'flex-col', 'gap-4'])>
            <div>
                <label @class(['block', 'text-sm', 'font-medium', 'text-slate-700', 'dark:text-slate-300', 'mb-1'])>HTML Code *</label>
                <p @class(['text-xs', 'text-slate-400', 'mb-1'])>Dùng placeholder: <code @class(['bg-slate-200', 'dark:bg-slate-700', 'px-1', 'rounded'])>@{{IMAGE_1}}</code>, <code @class(['bg-slate-200', 'dark:bg-slate-700', 'px-1', 'rounded'])>@{{TITLE}}</code>...</p>
                <textarea name="html_code" rows="10" required
                    @class(['w-full', 'bg-slate-100', 'dark:bg-background-dark', 'border', 'border-slate-200', 'dark:border-border-dark', 'text-slate-700', 'dark:text-slate-300', 'text-sm', 'rounded-lg', 'px-3', 'py-2', 'focus:outline-none', 'focus:border-primary', 'focus:ring-1', 'focus:ring-primary', 'font-mono'])
                    placeholder="<div class='gift-card'>&#10;  <h1>&#123;&#123;TITLE&#125;&#125;</h1>&#10;  <p>&#123;&#123;MESSAGE&#125;&#125;</p>&#10;</div>"></textarea>
            </div>
            <div>
                <label @class(['block', 'text-sm', 'font-medium', 'text-slate-700', 'dark:text-slate-300', 'mb-1'])>CSS Code</label>
                <textarea name="css_code" rows="6"
                    @class(['w-full', 'bg-slate-100', 'dark:bg-background-dark', 'border', 'border-slate-200', 'dark:border-border-dark', 'text-slate-700', 'dark:text-slate-300', 'text-sm', 'rounded-lg', 'px-3', 'py-2', 'focus:outline-none', 'focus:border-primary', 'focus:ring-1', 'focus:ring-primary', 'font-mono'])
                    placeholder=".gift-card { ... }"></textarea>
            </div>
            <div>
                <label @class(['block', 'text-sm', 'font-medium', 'text-slate-700', 'dark:text-slate-300', 'mb-1'])>JavaScript Code</label>
                <textarea name="js_code" rows="6"
                    @class(['w-full', 'bg-slate-100', 'dark:bg-background-dark', 'border', 'border-slate-200', 'dark:border-border-dark', 'text-slate-700', 'dark:text-slate-300', 'text-sm', 'rounded-lg', 'px-3', 'py-2', 'focus:outline-none', 'focus:border-primary', 'focus:ring-1', 'focus:ring-primary', 'font-mono'])
                    placeholder="// Hiệu ứng pháo hoa..."></textarea>
            </div>
        </div>

        {{-- Tab: Schema --}}
        <div x-show="activeTab === 'schema'" @class(['flex', 'flex-col', 'gap-4'])>
            <div>
                <label @class(['block', 'text-sm', 'font-medium', 'text-slate-700', 'dark:text-slate-300', 'mb-1'])>Schema JSON</label>
                <p @class(['text-xs', 'text-slate-400', 'mb-1'])>Định nghĩa các trường nhập liệu cho user. Ví dụ mẫu bên dưới (type: asset_picker dùng để kéo dữ liệu từ kho tài nguyên).</p>
                <textarea name="schema" rows="16"
                    @class(['w-full', 'bg-slate-100', 'dark:bg-background-dark', 'border', 'border-slate-200', 'dark:border-border-dark', 'text-slate-700', 'dark:text-slate-300', 'text-sm', 'rounded-lg', 'px-3', 'py-2', 'focus:outline-none', 'focus:border-primary', 'focus:ring-1', 'focus:ring-primary', 'font-mono'])
                    placeholder="Nhập JSON Schema tại đây">{
                "fields": [
                    { "key": "TITLE", "type": "text", "label": "Tiêu đề", "required": true },
                    { "key": "IMAGE", "type": "image", "label": "Ảnh tự upload", "limit": 2 },
                    { "key": "MUSIC", "type": "asset_picker", "asset_type": "audio", "label": "Chọn Nhạc nền (từ kho)" },
                    { "key": "STICKER", "type": "asset_picker", "asset_type": "gif", "label": "Chọn Sticker động" }
                ]
                }</textarea>
            </div>
        </div>

        {{-- Nút submit --}}
        <div @class(['flex', 'items-center', 'justify-end', 'gap-3', 'mt-6', 'pt-4', 'border-t', 'border-slate-200', 'dark:border-border-dark'])>
            <button type="button" x-on:click="$dispatch('close')"
                @class(['px-4', 'py-2', 'text-sm', 'font-medium', 'text-slate-600', 'dark:text-slate-400', 'hover:text-slate-900', 'dark:hover:text-white', 'transition-colors'])>
                Hủy
            </button>
            <button type="submit"
                @class(['px-4', 'py-2', 'bg-primary', 'hover:bg-primary/90', 'text-white', 'text-sm', 'font-semibold', 'rounded-lg', 'transition-colors', 'shadow-sm'])>
                Thêm mẫu
            </button>
        </div>
    </form>
</x-ui.modal>
