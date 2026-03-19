{{-- Modal Xác nhận xóa bài viết --}}
<x-ui.modal name="delete-blog-post" maxWidth="md">
    <div x-data="{
            post: { id: null, title: '' }
        }"
        @open-delete-blog-post.window="post = $event.detail; $dispatch('open-modal', 'delete-blog-post')">
        <form method="POST" x-bind:action="`{{ url('admin/blogs-posts') }}/${post?.id}`" class="p-6">
            @csrf
            @method('DELETE')

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="size-10 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-rose-500">warning</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Xóa bài viết</h3>
            </div>

            {{-- Content --}}
            <div class="mb-6">
                <p class="text-slate-600 dark:text-slate-400 text-sm">
                    Bạn có chắc chắn muốn xóa bài viết <strong class="text-slate-900 dark:text-white"
                        x-text="`&quot;${post?.title}&quot;`"></strong> không? Hành động này sẽ chuyển trạng thái bài
                    viết thành đã xóa và sẽ không còn hiển thị trên trang chủ.
                </p>
                <div
                    class="mt-4 p-3 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-lg flex gap-3">
                    <span class="material-symbols-outlined text-amber-500 text-[20px] shrink-0">info</span>
                    <p class="text-xs text-amber-700 dark:text-amber-400">
                        Bài viết bị xóa vẫn được lưu tạm trong cơ sở dữ liệu.
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'delete-blog-post')"
                    class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    Hủy bớt
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    Chắc chắn xóa
                </button>
            </div>
        </form>
    </div>
</x-ui.modal>