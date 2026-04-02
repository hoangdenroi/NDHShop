@extends('layouts.app.app-layout')

@section('content')
    {{-- Alpine.js state chính cho toàn trang --}}
    <div x-data="{
        activeTab: 'databases',
        showCreateModal: false,
        showConnectionModal: false,
        selectedDb: null,
        selectedEngine: 'mysql',
        dbName: '',
        copiedField: null,
        needsReload: false,

        {{-- Toast thông báo --}}
        showToast(message, type = 'success') {
            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    type: type,
                    title: type === 'warning' ? 'Cảnh báo' : (type === 'error' ? 'Lỗi' : (type === 'success' ? 'Thành công' : 'Thông báo')),
                    message: message
                }
            }));
        },

        copyToClipboard(text, field) {
            navigator.clipboard.writeText(text);
            this.copiedField = field;
            setTimeout(() => this.copiedField = null, 2000);
        },

        {{-- Chọn DB để xem connection --}}
        openConnection(db) {
            this.selectedDb = db;
            this.showConnectionModal = true;
        },

        closeConnectionModal() {
            this.showConnectionModal = false;
            // Xóa password clear text
            if (this.selectedDb) this.selectedDb.raw_password = null;
            if (this.needsReload) location.reload();
        },

        {{-- Tạo database mới --}}
        isCreatingDb: false,
        async createDatabase() {
            if (!this.dbName) return;
            this.isCreatingDb = true;

            try {
                const res = await fetch('{{ route('app.cloud-plan.create-database') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        engine: this.selectedEngine,
                        name: this.dbName
                    })
                });
                const data = await res.json();

                if (data.success) {
                    this.showCreateModal = false;
                    this.showToast(data.message, 'success');
                    
                    // Mở luôn modal connection với pass clear text một lần duy nhất
                    this.selectedDb = {
                        ...data.data.database,
                        connection_string: `${data.data.database.engine}://${data.data.database.db_user}:${data.data.password}@${data.data.database.host}:${data.data.database.port}/${data.data.database.db_name}`,
                        raw_password: data.data.password // Lưu tạm pass để hiện modal
                    };
                    this.needsReload = true;
                    this.showConnectionModal = true;
                    this.dbName = ''; // Reset form
                } else {
                    this.showToast(data.message || 'Thất bại.', 'error');
                }
            } catch (e) {
                console.error(e);
                this.showToast('Không thể kết nối máy chủ.', 'error');
            } finally {
                this.isCreatingDb = false;
            }
        },

        {{-- Xóa database --}}
        isDeletingDb: false,
        async deleteDb(id, name) {
            if (!confirm(`Bạn có chắc muốn xóa database ${name} vĩnh viễn?\nHành động này không thể hoàn tác!`)) return;

            this.isDeletingDb = true;
            try {
                const res = await fetch(`{{ url('apps/cloud-plan/database') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    this.showToast(data.message || 'Thất bại.', 'error');
                }
            } catch (e) {
                console.error(e);
                this.showToast('Không thể kết nối máy chủ.', 'error');
            } finally {
                this.isDeletingDb = false;
            }
        }
    }" class="flex flex-col lg:flex-row gap-8 w-full relative">


        {{-- Sidebar trái: tổng quan, điều hướng, quota --}}
        @include('pages.app.databases._sidebar')

        {{-- Nội dung chính --}}
        <main class="flex-1 flex flex-col gap-6">

            {{-- Tab: Databases --}}
            @include('pages.app.databases._tab-databases')

            {{-- Tab: API Keys --}}
            @include('pages.app.databases._tab-api-keys')

            {{-- Tab: Bảng giá --}}
            <div x-show="activeTab === 'pricing'" x-cloak>
                @include('pages.app.databases._tab-pricing')
            </div>
        </main>

        {{-- Modal: Tạo Database mới --}}
        @include('pages.app.databases._modal-create')

        {{-- Modal: Chi tiết kết nối --}}
        @include('pages.app.databases._modal-connection')
    </div>
@endsection