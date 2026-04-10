<x-admin-layout title="NDHShop - Admin - Cài đặt Hetzner VPS">
    <div class="flex flex-col gap-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white/80 dark:bg-surface-dark border border-slate-200 dark:border-border-dark p-4 rounded-xl backdrop-blur-sm">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Cài đặt Hetzner Cloud</h2>
                <p class="text-sm text-slate-500 mt-1">Sync hệ điều hành & vị trí datacenter từ Hetzner API</p>
            </div>
            <form method="POST" action="{{ route('admin.vps-settings.sync') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-primary/25 whitespace-nowrap">
                    <span class="material-symbols-outlined text-[18px]">sync</span>
                    Sync từ Hetzner
                </button>
            </form>
        </div>

        {{-- Bảng Hệ Điều Hành --}}
        <div class="rounded-lg border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-border-dark">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">terminal</span>
                    Hệ điều hành ({{ $operatingSystems->count() }})
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-background-dark/50 border-b border-slate-200 dark:border-border-dark">
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tên</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Hetzner Name</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Loại</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kiến trúc</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-border-dark">
                        @forelse($operatingSystems as $os)
                            <tr class="hover:bg-slate-50 dark:hover:bg-background-dark/30 transition-colors">
                                <td class="p-4">
                                    <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $os->name }}</span>
                                </td>
                                <td class="p-4">
                                    <code class="text-xs bg-slate-100 dark:bg-background-dark px-2 py-1 rounded text-primary font-mono">{{ $os->hetzner_name }}</code>
                                </td>
                                <td class="p-4">
                                    <span class="text-sm text-slate-600 dark:text-slate-400 capitalize">{{ $os->os_flavor ?? '—' }}</span>
                                </td>
                                <td class="p-4">
                                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ $os->architecture }}</span>
                                </td>
                                <td class="p-4">
                                    @if($os->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                            <span class="size-1.5 rounded-full bg-emerald-500"></span> Bật
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                            <span class="size-1.5 rounded-full bg-slate-500"></span> Tắt
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <form method="POST" action="{{ route('admin.vps-settings.toggle-os', $os) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <label class="relative inline-flex items-center cursor-pointer" title="{{ $os->is_active ? 'Tắt' : 'Bật' }}">
                                            <input type="checkbox" onchange="this.form.submit()" class="sr-only peer" {{ $os->is_active ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                                        </label>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600">terminal</span>
                                        <p class="text-slate-500 text-sm">Chưa có dữ liệu. Nhấn "Sync từ Hetzner" để bắt đầu.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Bảng Locations --}}
        <div class="rounded-lg border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-border-dark">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">location_on</span>
                    Vị trí Datacenter ({{ $locations->count() }})
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-background-dark/50 border-b border-slate-200 dark:border-border-dark">
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tên</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Hetzner Name</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Thành phố</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Quốc gia</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Network Zone</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-border-dark">
                        @forelse($locations as $location)
                            <tr class="hover:bg-slate-50 dark:hover:bg-background-dark/30 transition-colors">
                                <td class="p-4">
                                    <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $location->name }}</span>
                                </td>
                                <td class="p-4">
                                    <code class="text-xs bg-slate-100 dark:bg-background-dark px-2 py-1 rounded text-primary font-mono">{{ $location->hetzner_name }}</code>
                                </td>
                                <td class="p-4">
                                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ $location->city ?? '—' }}</span>
                                </td>
                                <td class="p-4">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $location->country ?? '—' }}</span>
                                </td>
                                <td class="p-4">
                                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ $location->network_zone ?? '—' }}</span>
                                </td>
                                <td class="p-4">
                                    @if($location->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                            <span class="size-1.5 rounded-full bg-emerald-500"></span> Bật
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                            <span class="size-1.5 rounded-full bg-slate-500"></span> Tắt
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <form method="POST" action="{{ route('admin.vps-settings.toggle-location', $location) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <label class="relative inline-flex items-center cursor-pointer" title="{{ $location->is_active ? 'Tắt' : 'Bật' }}">
                                            <input type="checkbox" onchange="this.form.submit()" class="sr-only peer" {{ $location->is_active ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                                        </label>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600">location_on</span>
                                        <p class="text-slate-500 text-sm">Chưa có dữ liệu. Nhấn "Sync từ Hetzner" để bắt đầu.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-admin-layout>
