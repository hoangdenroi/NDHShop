<div x-show="activeTab === 'profile'" x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
    <div
        class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Thông tin tài khoản</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Cập nhật thông tin cá nhân của bạn.</p>
        </div>
        <form method="POST" action="{{ route('app.profile.update') }}" class="p-6 space-y-6"
            enctype="multipart/form-data">
            @csrf
            @method('patch')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Họ tên --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="name">Họ và
                        tên</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">person</span>
                        <input
                            class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary pl-10 h-12 sm:text-sm"
                            id="name" name="name" type="text" value="{{ old('name', Auth::user()->name) }}" required />
                    </div>
                    <x-ui.input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                {{-- Số điện thoại --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="phone">Số điện
                        thoại</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">phone</span>
                        <input
                            class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary pl-10 h-12 sm:text-sm"
                            id="phone" name="phone" type="number" value="{{ old('phone', Auth::user()->phone) }}" />
                    </div>
                    <x-ui.input-error :messages="$errors->get('phone')" class="mt-1" />
                </div>
            </div>

            {{-- Ảnh đại diện --}}
            <div class="space-y-1.5" x-data="{
                preview: '{{ Auth::user()->avatar_url }}',
                dragover: false,
                handleFileChange(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.preview = URL.createObjectURL(file);
                    }
                },
                handleDrop(event) {
                    this.dragover = false;
                    const file = event.dataTransfer.files[0];
                    if (file) {
                        this.$refs.fileInput.files = event.dataTransfer.files;
                        this.preview = URL.createObjectURL(file);
                    }
                }
            }">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Ảnh đại diện (Tùy
                    chọn)</label>
                <div class="relative flex justify-center w-full mt-2">
                    <label for="avatar_file" @dragover.prevent="dragover = true" @dragleave.prevent="dragover = false"
                        @drop.prevent="handleDrop($event)"
                        :class="{'border-primary bg-primary/5': dragover, 'border-slate-300 dark:border-slate-600 border-dashed': !dragover}"
                        class="flex flex-col items-center justify-center w-full h-48 border-2 rounded-xl cursor-pointer bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-all overflow-hidden relative group">

                        <input type="file" id="avatar_file" name="avatar_file" class="hidden" x-ref="fileInput"
                            accept="image/png, image/jpeg, image/jpg, image/webp" @change="handleFileChange" />

                        <div x-show="!preview"
                            class="flex flex-col items-center justify-center pt-5 pb-6 text-slate-500 dark:text-slate-400">
                            <span
                                class="material-symbols-outlined text-[40px] mb-2 opacity-50 group-hover:text-primary transition-colors">cloud_upload</span>
                            <p class="mb-1 text-sm font-semibold">Nhấn để tải lên hoặc kéo thả vào</p>
                            <p class="text-xs">JPG, PNG, WEBP (Tối đa 20MB)</p>
                        </div>

                        <template x-if="preview">
                            <div class="absolute inset-0 p-2">
                                <img :src="preview" class="w-full h-full object-contain rounded-lg">
                                <div
                                    class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span
                                        class="text-white text-sm font-bold truncate px-4 py-2 bg-slate-800/80 rounded-lg shadow-sm w-max mb-12 flex items-center gap-2"><span
                                            class="material-symbols-outlined text-sm">edit</span>Đổi ảnh mới</span>
                                </div>
                            </div>
                        </template>
                    </label>
                </div>
                <x-ui.input-error :messages="$errors->get('avatar_file')" class="mt-1" />
            </div>

            {{-- Thông tin chỉ xem (không sửa được) --}}
            <div class="border-t border-slate-100 dark:border-slate-700 pt-6 space-y-4">
                <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Thông tin
                    tài khoản</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Mã tài khoản --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Mã tài khoản</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">badge</span>
                            <input
                                class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 pl-10 h-12 sm:text-sm cursor-not-allowed"
                                value="{{ Auth::user()->unitcode }}" readonly />
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1">
                                <button type="button"
                                    @click="navigator.clipboard.writeText('{{ Auth::user()->unitcode }}'); window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', title: 'Thành công', message: 'Đã sao chép mã tài khoản!' } }));"
                                    class="flex items-center justify-center p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                                    title="Sao chép">
                                    <span class="material-symbols-outlined text-[18px]">content_copy</span>
                                </button>
                                <span
                                    class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-[18px] pointer-events-none">lock</span>
                            </div>
                        </div>
                    </div>

                    {{-- Số dư --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Số dư tài
                            khoản</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">account_balance_wallet</span>
                            <input
                                class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 pl-10 h-12 sm:text-sm cursor-not-allowed"
                                value="{{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }} VNĐ" readonly />
                            <span
                                class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 dark:text-slate-600 text-[18px] pointer-events-none">lock</span>
                        </div>
                    </div>
                </div>

                {{-- Đăng nhập lần cuối --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Đăng nhập lần
                        cuối</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">schedule</span>
                        <input
                            class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 pl-10 h-12 sm:text-sm cursor-not-allowed"
                            value="{{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('d/m/Y H:i') : 'Chưa có' }}"
                            readonly />
                        <span
                            class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 dark:text-slate-600 text-[18px] pointer-events-none">lock</span>
                    </div>
                </div>
            </div>

            {{-- Nút lưu --}}
            <div class="flex items-center gap-4">
                <button type="submit"
                    class="flex items-center justify-center h-12 px-8 bg-primary hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-sm shadow-primary/20 active:scale-[0.98]">
                    Lưu thay đổi
                </button>
                @if (session('status') === 'profile-updated')
                    <p class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">Đã lưu thành công!</p>
                @endif
            </div>
        </form>
    </div>
</div>