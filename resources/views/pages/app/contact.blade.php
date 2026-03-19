@extends('layouts.app.app-layout')

@section('content')
    {{-- Hero Title Section --}}
    <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl shadow-sm overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 py-16 md:py-24">
            <h1 class="text-slate-900 dark:text-slate-100 text-5xl font-black leading-tight tracking-tight mb-4">Get in touch</h1>
            <p class="text-slate-600 dark:text-slate-400 text-xl max-w-2xl">We're here to help you scale your engineering team. Send us a message and we'll respond within 24 hours.</p>
        </div>
    </div>

    {{-- Content Section --}}
    <div class="max-w-7xl mx-auto py-4">
        <div class="grid lg:grid-cols-2 gap-16">
            {{-- Left Side: Contact Form --}}
            <div class="bg-white dark:bg-slate-800 p-8 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <form action="#" class="space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <label class="flex flex-col gap-2">
                            <span class="text-slate-700 dark:text-slate-300 text-sm font-semibold">Full Name</span>
                            <input class="form-input rounded-lg border-slate-200 dark:border-slate-700 bg-background-light dark:bg-slate-800 focus:border-primary focus:ring-1 focus:ring-primary h-12 px-4 text-base" placeholder="John Doe" type="text"/>
                        </label>
                        <label class="flex flex-col gap-2">
                            <span class="text-slate-700 dark:text-slate-300 text-sm font-semibold">Email Address</span>
                            <input class="form-input rounded-lg border-slate-200 dark:border-slate-700 bg-background-light dark:bg-slate-800 focus:border-primary focus:ring-1 focus:ring-primary h-12 px-4 text-base" placeholder="john@codemarket.com" type="email"/>
                        </label>
                    </div>
                    <label class="flex flex-col gap-2">
                        <span class="text-slate-700 dark:text-slate-300 text-sm font-semibold">Subject</span>
                        <input class="form-input rounded-lg border-slate-200 dark:border-slate-700 bg-background-light dark:bg-slate-800 focus:border-primary focus:ring-1 focus:ring-primary h-12 px-4 text-base" placeholder="How can we help?" type="text"/>
                    </label>
                    <label class="flex flex-col gap-2">
                        <span class="text-slate-700 dark:text-slate-300 text-sm font-semibold">Message</span>
                        <textarea class="form-textarea rounded-lg border-slate-200 dark:border-slate-700 bg-background-light dark:bg-slate-800 focus:border-primary focus:ring-1 focus:ring-primary p-4 text-base resize-none" placeholder="Tell us more about your project..." rows="6"></textarea>
                    </label>
                    <button class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 px-8 rounded-lg transition-all flex items-center justify-center gap-2" type="submit">
                        Send Message
                        <span class="material-symbols-outlined">send</span>
                    </button>
                </form>
            </div>

            {{-- Right Side: Contact Info & Map --}}
            <div class="flex flex-col gap-10">
                <div class="space-y-8">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Contact Information</h3>
                    <div class="flex gap-4">
                        <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <span class="material-symbols-outlined">mail</span>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-slate-100">Email us</p>
                            <p class="text-slate-600 dark:text-slate-400">support@codemarket.dev</p>
                            <p class="text-slate-600 dark:text-slate-400">sales@codemarket.dev</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <span class="material-symbols-outlined">location_on</span>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-slate-100">Our Office</p>
                            <p class="text-slate-600 dark:text-slate-400">123 Tech Avenue, Suite 400<br/>San Francisco, CA 94105</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <span class="material-symbols-outlined">share</span>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-slate-100">Follow Us</p>
                            <div class="flex gap-4 mt-2">
                                <a class="text-slate-500 hover:text-primary transition-colors" href="#"><span class="material-symbols-outlined">language</span></a>
                                <a class="text-slate-500 hover:text-primary transition-colors" href="#"><span class="material-symbols-outlined">group</span></a>
                                <a class="text-slate-500 hover:text-primary transition-colors" href="#"><span class="material-symbols-outlined">public</span></a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Map Placeholder --}}
                <div class="relative w-full aspect-video rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 shadow-inner">
                    <div class="absolute inset-0 bg-slate-200 dark:bg-slate-700 flex flex-col items-center justify-center text-slate-400" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCcgBG18QtHtzcHtiPSNVAu-1BweNTjdwICeARZeZ5Ntrslv50MVXxFUot53vz4HLoK5HhQza8Bq9yRX6bgqfWX-mHKikMkPJfHTq67x_Hn4vz0Qmj0SObU_w619xbyHq609pujsxXafWofwfNG9ofFh3C2jhHvnZQx3TwSwWjudghTMBDeW8malIDoxrt1CD9AqhFw9_ijWPmsiYr7i5qv_XU49h3I2CvIYGNwahPToYYZFqtZRMXq1EmfQXfTZlI0lm7OXtprhwQ"); background-size: cover;'>
                        <div class="bg-white dark:bg-slate-900 p-3 rounded-full shadow-lg border border-primary animate-bounce">
                            <span class="material-symbols-outlined text-primary text-3xl">location_on</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
