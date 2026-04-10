@extends('layouts.app.app-layout')

@section('content')
    {{-- Hero Title Section --}}
    <div @class(['bg-white', 'dark:bg-slate-800', 'border', 'border-slate-100', 'dark:border-slate-700', 'rounded-2xl', 'shadow-sm', 'overflow-hidden'])>
        <div @class(['max-w-7xl', 'mx-auto', 'px-6', 'py-16', 'md:py-24'])>
            <h1 @class(['text-slate-900', 'dark:text-slate-100', 'text-5xl', 'font-black', 'leading-tight', 'tracking-tight', 'mb-4'])>Liên hệ với chúng tôi</h1>
            <p @class(['text-slate-600', 'dark:text-slate-400', 'text-xl', 'max-w-2xl'])>Chúng tôi luôn sẵn sàng hỗ trợ bạn.
                Hãy gửi tin nhắn và chúng tôi sẽ phản hồi trong vòng 24 giờ.</p>
        </div>
    </div>

    {{-- Content Section --}}
    <div @class(['max-w-7xl', 'mx-auto', 'py-4', 'lg:mt-10'])>
        <div @class(['grid', 'lg:grid-cols-2', 'gap-10', 'lg:gap-16'])>
            {{-- Left Side: Contact Form --}}
            <div @class(['bg-white', 'dark:bg-slate-800', 'p-8', 'mx-6', 'lg:mx-0', 'rounded-xl', 'border', 'border-slate-200', 'dark:border-slate-700', 'shadow-sm'])>
                <form x-data="{
                            form: { name: '', email: '', subject: '', message: '' },
                            loading: false,
                            successMsg: '',
                            errorMsg: '',
                            categories: [],
                            init() {
                                fetch('{{ route('api.v1.categories') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                                    },
                                    body: JSON.stringify({ parent_id: null })
                                })
                                .then(res => res.json())
                            .then(data => {
                                    let flatList = [];
                                    data.forEach(cat => {
                                        let hasChildren = cat.children && cat.children.length > 0;
                                        // Danh mục cha (nếu có con thì không cho chọn, biến thành tiêu đề)
                                        flatList.push({ 
                                            value: hasChildren ? '' : cat.name, 
                                            label: cat.name, 
                                            disabled: hasChildren 
                                        });
                                        // Danh mục con
                                        if (hasChildren) {
                                            cat.children.forEach(child => {
                                                flatList.push({ 
                                                    value: child.name, 
                                                    label: '— ' + child.name, 
                                                    disabled: false 
                                                });
                                            });
                                        }
                                    });
                                    // Thêm tùy chọn Khác ở cuối cùng
                                    flatList.push({
                                        value: 'Khác',
                                        label: 'Khác (Other)',
                                        disabled: false
                                    });
                                    this.categories = flatList;
                                });
                            },
                            submit() {
                                this.loading = true;
                                this.successMsg = '';
                                this.errorMsg = '';
                                fetch('/api/v1/contact/submit', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify(this.form)
                                })
                                .then(res => res.json())
                                .then(data => {
                                    this.loading = false;
                                    if(data.success) {
                                        this.successMsg = data.message;
                                        this.form = { name: '', email: '', subject: '', message: '' };
                                    } else {
                                        if(data.errors) {
                                            this.errorMsg = Object.values(data.errors)[0][0];
                                        } else {
                                            this.errorMsg = data.message || 'Lỗi hệ thống';
                                        }
                                    }
                                })
                                .catch(err => {
                                    this.loading = false;
                                    this.errorMsg = 'Có lỗi xảy ra, vui lòng thử lại sau.';
                                });
                            }
                        }" @submit.prevent="submit" @class(['space-y-6'])>

                    <div @class(['grid', 'md:grid-cols-2', 'gap-6'])>
                        <label @class(['flex', 'flex-col', 'gap-2'])>
                            <span @class(['text-slate-700', 'dark:text-slate-300', 'text-sm', 'font-semibold'])>Họ và tên
                                <span @class(['text-red-500'])>*</span></span>
                            <input x-model="form.name" required @class(['form-input', 'rounded-lg', 'border-slate-200', 'dark:border-slate-700', 'bg-background-light', 'dark:bg-slate-800', 'focus:border-primary', 'focus:ring-1', 'focus:ring-primary', 'h-12', 'px-4', 'text-base'])
                                placeholder="Nguyễn Văn A" type="text" />
                        </label>
                        <label @class(['flex', 'flex-col', 'gap-2'])>
                            <span @class(['text-slate-700', 'dark:text-slate-300', 'text-sm', 'font-semibold'])>Địa chỉ
                                Email <span @class(['text-red-500'])>*</span></span>
                            <input x-model="form.email" required @class(['form-input', 'rounded-lg', 'border-slate-200', 'dark:border-slate-700', 'bg-background-light', 'dark:bg-slate-800', 'focus:border-primary', 'focus:ring-1', 'focus:ring-primary', 'h-12', 'px-4', 'text-base'])
                                placeholder="email@example.com" type="email" />
                        </label>
                    </div>
                    <label @class(['flex', 'flex-col', 'gap-2'])>
                        <span @class(['text-slate-700', 'dark:text-slate-300', 'text-sm', 'font-semibold'])>Chủ đề <span
                                @class(['text-red-500'])>*</span></span>
                        <select x-model="form.subject" required @class(['form-select', 'rounded-lg', 'border-slate-200', 'dark:border-slate-700', 'bg-background-light', 'dark:bg-slate-800', 'focus:border-primary', 'focus:ring-1', 'focus:ring-primary', 'h-12', 'px-4', 'text-base'])>
                            <option value="">-- Chọn chủ đề cần hỗ trợ --</option>
                            <template x-for="(cat, index) in categories" :key="index">
                                <option :value="cat.value" x-text="cat.label" :disabled="cat.disabled" :class="cat.disabled ? 'font-bold text-slate-900 bg-slate-100 dark:bg-slate-700 dark:text-white' : ''"></option>
                            </template>
                        </select>
                    </label>
                    <label @class(['flex', 'flex-col', 'gap-2'])>
                        <span @class(['text-slate-700', 'dark:text-slate-300', 'text-sm', 'font-semibold'])>Nội dung tin
                            nhắn <span @class(['text-red-500'])>*</span></span>
                        <textarea x-model="form.message" required @class(['form-textarea', 'rounded-lg', 'border-slate-200', 'dark:border-slate-700', 'bg-background-light', 'dark:bg-slate-800', 'focus:border-primary', 'focus:ring-1', 'focus:ring-primary', 'p-4', 'text-base', 'resize-none'])
                            placeholder="Hãy cho chúng tôi biết thêm về yêu cầu của bạn..." rows="6"></textarea>
                    </label>
                    <button :disabled="loading" type="submit" @class(['w-full', 'bg-primary', 'hover:bg-primary/90', 'text-white', 'font-bold', 'py-4', 'px-8', 'rounded-lg', 'transition-all', 'flex', 'items-center', 'justify-center', 'gap-2', 'disabled:opacity-50', 'disabled:cursor-not-allowed'])>
                        <span x-show="!loading">Gửi tin nhắn</span>
                        <span x-show="loading">Đang gửi...</span>
                        <span x-show="!loading" @class(['material-symbols-outlined'])>send</span>
                        <svg x-show="loading" @class(['animate-spin', '-ml-1', 'mr-3', 'h-5', 'w-5', 'text-white'])
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle @class(['opacity-25']) cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path @class(['opacity-75']) fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </button>
                    <!-- Alert Message -->
                    <template x-if="successMsg">
                        <div @class(['p-4', 'mb-4', 'text-sm', 'text-green-800', 'rounded-lg', 'bg-green-50', 'dark:bg-gray-800', 'dark:text-green-400']) role="alert">
                            <span @class(['font-medium'])>Thành công!</span> <span x-text="successMsg"></span>
                        </div>
                    </template>
                    <template x-if="errorMsg">
                        <div @class(['p-4', 'mb-4', 'text-sm', 'text-red-800', 'rounded-lg', 'bg-red-50', 'dark:bg-gray-800', 'dark:text-red-400']) role="alert">
                            <span @class(['font-medium'])>Lỗi!</span> <span x-text="errorMsg"></span>
                        </div>
                    </template>
                </form>
            </div>

            {{-- Right Side: Contact Info & Map --}}
            <div @class(['flex', 'flex-col', 'gap-10', 'mx-6', 'lg:mx-0'])>
                <div @class(['space-y-8'])>
                    <h3 @class(['text-2xl', 'font-bold', 'text-slate-900', 'dark:text-slate-100'])>Thông tin liên hệ</h3>
                    <div @class(['flex', 'gap-4'])>
                        <div @class(['size-12', 'rounded-lg', 'bg-primary/10', 'flex', 'items-center', 'justify-center', 'text-primary', 'shrink-0'])>
                            <span @class(['material-symbols-outlined'])>mail</span>
                        </div>
                        <div>
                            <p @class(['font-semibold', 'text-slate-900', 'dark:text-slate-100'])>Email của chúng tôi</p>
                            <p @class(['text-slate-600', 'dark:text-slate-400'])>support@ndhshop.com</p>
                        </div>
                    </div>
                    <div @class(['flex', 'gap-4'])>
                        <div @class(['size-12', 'rounded-lg', 'bg-primary/10', 'flex', 'items-center', 'justify-center', 'text-primary', 'shrink-0'])>
                            <span @class(['material-symbols-outlined'])>location_on</span>
                        </div>
                        <div>
                            <p @class(['font-semibold', 'text-slate-900', 'dark:text-slate-100'])>Văn phòng chính</p>
                            <p @class(['text-slate-600', 'dark:text-slate-400'])>Hà Nội. Việt Nam</p>
                        </div>
                    </div>
                    <div @class(['flex', 'gap-4'])>
                        <div @class(['size-12', 'rounded-lg', 'bg-primary/10', 'flex', 'items-center', 'justify-center', 'text-primary', 'shrink-0'])>
                            <span @class(['material-symbols-outlined'])>share</span>
                        </div>
                        <div>
                            <p @class(['font-semibold', 'text-slate-900', 'dark:text-slate-100'])>Theo dõi chúng tôi</p>
                            <div @class(['flex', 'gap-4', 'mt-2'])>
                                <a @class(['text-slate-500', 'hover:text-primary', 'transition-colors', 'flex', 'items-center', 'justify-center', 'p-2', 'rounded-full', 'hover:bg-slate-100', 'dark:hover:bg-slate-800']) href="#">
                                    <svg @class(['w-5', 'h-5', 'fill-current']) viewBox="0 0 24 24">
                                        <path
                                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                    </svg>
                                </a>
                                <a @class(['text-slate-500', 'hover:text-primary', 'transition-colors', 'flex', 'items-center', 'justify-center', 'p-2', 'rounded-full', 'hover:bg-slate-100', 'dark:hover:bg-slate-800']) href="#">
                                    <svg @class(['w-5', 'h-5', 'fill-current']) viewBox="0 0 448 512">
                                        <path
                                            d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z" />
                                    </svg>
                                </a>
                                <a @class(['text-slate-500', 'hover:text-primary', 'transition-colors', 'flex', 'items-center', 'justify-center', 'p-2', 'rounded-full', 'hover:bg-slate-100', 'dark:hover:bg-slate-800']) href="#">
                                    <svg @class(['w-5', 'h-5', 'fill-current']) viewBox="0 0 24 24">
                                        <path
                                            d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Google Maps --}}
                <div @class(['relative', 'w-full', 'aspect-video', 'rounded-xl', 'overflow-hidden', 'border', 'border-slate-200', 'dark:border-slate-800', 'bg-slate-100', 'dark:bg-slate-800', 'shadow-inner'])>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d1144.834204535145!2d105.77934926962553!3d21.127720249739053!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMjHCsDA3JzM5LjgiTiAxMDXCsDQ2JzQ4LjAiRQ!5e1!3m2!1svi!2s!4v1774599189339!5m2!1svi!2s"
                        @class(['absolute', 'inset-0', 'w-full', 'h-full']) style="border:0;" allowfullscreen=""
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
@endsection