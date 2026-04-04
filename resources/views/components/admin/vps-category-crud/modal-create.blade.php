@props(['operatingSystems', 'locations'])

<div x-data="{
    form: {
        name: '', server_group: 'regular', provision_type: 'auto', hetzner_server_type: '', price: '', annual_price: '',
        cpu: '', ram: '', storage: '', bandwidth: '20 TB',
        status: 'active', sort_order: 0,
        is_best_seller: false, is_renewable: true,
        description: '',
        operating_system_ids: [], location_ids: [],
        metadata: { 
            available_months: ['1', '3', '6', '12'],
            connection_methods: ['password', 'ssh'],
            ip: '1 IPv4 Public, 1 IPv6 Public',
            firewall: 'DDoS Protection',
            backup: 'Có (Tùy chọn)'
        }
    },
    resetForm() {
        this.form = {
            name: '', server_group: 'regular', provision_type: 'auto', hetzner_server_type: '', price: '', annual_price: '',
            cpu: '', ram: '', storage: '', bandwidth: '20 TB',
            status: 'active', sort_order: 0,
            is_best_seller: false, is_renewable: true,
            description: '',
            operating_system_ids: [], location_ids: [],
            metadata: { 
                available_months: ['1', '3', '6', '12'],
                connection_methods: ['password', 'ssh'],
                ip: '1 IPv4 Public, 1 IPv6 Public',
                firewall: 'DDoS Protection',
                backup: 'Có (Tùy chọn)'
            }
        };
    }
}" x-on:open-create-vps-category.window="$dispatch('open-modal', 'create-vps-category'); resetForm();">

    <x-ui.modal name="create-vps-category" maxWidth="2xl">
        <form action="{{ route('admin.vps-categories.store') }}" method="POST" class="p-6 max-h-[90vh] overflow-y-auto">
            @csrf

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-6 border-b border-slate-200 dark:border-border-dark pb-4">
                <span class="material-symbols-outlined text-primary text-[24px]">add_circle</span>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Thêm Gói VPS</h3>
            </div>

            <div class="space-y-4">
                @include('pages.admin.vps-categories-form-fields')
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 border-t border-slate-200 dark:border-border-dark mt-6 pt-4">
                <button type="button" x-on:click="$dispatch('close-modal', 'create-vps-category')"
                    class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    Hủy
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    Thêm Gói
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>
