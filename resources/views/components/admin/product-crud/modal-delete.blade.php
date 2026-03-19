<div x-data="{
    product: {},
    open(productData) {
        this.product = productData;
        $dispatch('open-modal', 'delete-product');
    }
}" x-on:open-delete-product.window="open($event.detail)">

    <x-ui.modal name="delete-product" maxWidth="sm">
        <form method="POST" x-bind:action="'/admin/products/' + product.id" class="p-6">
            @csrf
            @method('DELETE')

            <div class="flex flex-col items-center text-center">
                {{-- Icon cảnh báo --}}
                <div
                    class="w-16 h-16 rounded-full bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[32px] text-rose-500">warning</span>
                </div>

                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Xác nhận vô hiệu hóa</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                    Bạn có chắc chắn muốn vô hiệu hóa sản phẩm <span class="font-bold text-slate-900 dark:text-white"
                        x-text="product.name"></span>? Hành động này sẽ ẩn sản phẩm khỏi hệ thống.
                </p>

                <div class="flex items-center gap-3 w-full">
                    <button type="button" x-on:click="$dispatch('close-modal', 'delete-product')"
                        class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        Hủy bỏ
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-rose-500/25">
                        Xác nhận xóa
                    </button>
                </div>
            </div>
        </form>
    </x-ui.modal>
</div>