@props(['operatingSystems', 'locations'])

<div x-data="{
    form: {
        id: null, slug: '', name: '', server_group: 'regular', provision_type: 'auto', hetzner_server_type: '', price: '', annual_price: '',
        cpu: '', ram: '', storage: '', bandwidth: '20 TB',
        status: 'active', sort_order: 0,
        is_best_seller: false, is_renewable: true,
        description: '',
        operating_system_ids: [], location_ids: [],
        metadata: { 
            available_months: ['1', '3', '6', '12'],
            connection_methods: ['password', 'ssh'],
            ip: '',
            firewall: '',
            backup: ''
        }
    },
    open(category) {
        this.form = {
            id: category.id,
            slug: category.slug,
            name: category.name || '',
            server_group: category.server_group || 'regular',
            provision_type: category.provision_type || 'auto',
            hetzner_server_type: category.hetzner_server_type || '',
            price: category.price || '',
            annual_price: category.annual_price || '',
            cpu: category.cpu || '',
            ram: category.ram || '',
            storage: category.storage || '',
            bandwidth: category.bandwidth || '20 TB',
            status: category.status || 'active',
            sort_order: category.sort_order || 0,
            is_best_seller: category.is_best_seller == 1,
            is_renewable: category.is_renewable == 1,
            description: category.description || '',
            operating_system_ids: (category.operating_systems || []).map(os => os.id),
            location_ids: (category.locations || []).map(loc => loc.id),
            metadata: category.metadata ? { 
                available_months: (category.metadata.available_months || []).map(String),
                connection_methods: category.metadata.connection_methods || ['password', 'ssh'],
                ip: category.metadata.ip || '',
                firewall: category.metadata.firewall || '',
                backup: category.metadata.backup || ''
            } : { 
                available_months: ['1', '3', '6', '12'],
                connection_methods: ['password', 'ssh'],
                ip: '',
                firewall: '',
                backup: ''
            }
        };
        $dispatch('open-modal', 'edit-vps-category');
    }
}" x-on:open-edit-vps-category.window="open($event.detail)">

    <x-ui.modal name="edit-vps-category" maxWidth="2xl">
        <form method="POST" x-bind:action="`/admin/vps/categories/${form.slug}`" class="p-6 max-h-[90vh] overflow-y-auto">
            @csrf
            @method('PUT')

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-6 border-b border-slate-200 dark:border-border-dark pb-4">
                <span class="material-symbols-outlined text-primary text-[24px]">edit_note</span>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Cập nhật Gói VPS</h3>
            </div>

            <div class="space-y-4">
                @include('pages.admin.vps-categories-form-fields')
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 border-t border-slate-200 dark:border-border-dark mt-6 pt-4">
                <button type="button" x-on:click="$dispatch('close-modal', 'edit-vps-category')"
                    class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    Hủy
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    Lưu Thay Đổi
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>
