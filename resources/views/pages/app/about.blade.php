@extends('layouts.app.app-layout')

@section('content')
    {{-- Hero Section --}}
    <section class="relative py-20 px-6 bg-white dark:bg-background-dark overflow-hidden rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="mx-auto max-w-7xl">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="flex flex-col gap-6">
                    <span class="inline-block w-fit px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider">Since 2020</span>
                    <h1 class="text-5xl font-extrabold tracking-tight lg:text-6xl text-slate-900 dark:text-slate-100">
                        Empowering developers <span class="text-primary">worldwide</span>
                    </h1>
                    <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed max-w-xl">
                        CodeMarket is the leading destination for production-ready source code, helping developers build faster and smarter by providing high-quality reusable components.
                    </p>
                </div>
                <div class="relative h-[400px] w-full rounded-2xl overflow-hidden shadow-2xl">
                    <div class="absolute inset-0 bg-gradient-to-tr from-primary/20 to-transparent z-10"></div>
                    <img class="h-full w-full object-cover" alt="Diverse development team collaborating in modern office space" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDyy83w8LhP3TCdlnTSmU-Ok8buvpHnCUjiNAfZf4UFgec4jsy4-jllnP9nSp8WZU84uWhQBiXV3K83AuyJkrM41pbknNJLdZx5vHImyBHNfpxpht8-Iugl0zEhCKEbZCG0bBh2MScsph6urxyLIzkHir8B8s0qYPx_FOFrq4DCSDlY5UgCTU_mp8L19w6_zsy1mxYKX4f7TAhaaxB5u4pd4R5Ad2ZtMT1elylTCOsnZ6gaepDuGaaKoFGp31_tJ0cvn8yb-vFh-kk"/>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="py-12 border border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 rounded-2xl">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <p class="text-4xl font-black text-primary">50k+</p>
                    <p class="text-sm text-slate-500 uppercase font-bold tracking-widest mt-2">Active Users</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-black text-primary">10k+</p>
                    <p class="text-sm text-slate-500 uppercase font-bold tracking-widest mt-2">Digital Assets</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-black text-primary">120+</p>
                    <p class="text-sm text-slate-500 uppercase font-bold tracking-widest mt-2">Frameworks</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-black text-primary">24/7</p>
                    <p class="text-sm text-slate-500 uppercase font-bold tracking-widest mt-2">Expert Support</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Our Mission & Story --}}
    <section class="py-24 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="mx-auto max-w-4xl px-6">
            <div class="flex flex-col gap-16">
                <div>
                    <h2 class="text-3xl font-bold mb-6 flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">rocket_launch</span>
                        Our Mission
                    </h2>
                    <p class="text-xl text-slate-600 dark:text-slate-300 leading-relaxed italic border-l-4 border-primary pl-6">
                        "To provide a seamless marketplace where developers can find high-quality source code to accelerate their projects and focus on what truly matters: innovation."
                    </p>
                </div>
                <div>
                    <h2 class="text-3xl font-bold mb-6 flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">history_edu</span>
                        Our Story
                    </h2>
                    <div class="space-y-4 text-slate-600 dark:text-slate-400 leading-relaxed">
                        <p>
                            Founded in 2020, CodeMarket started as a small project to help developers share reusable components. We noticed a recurring problem: developers were spending 80% of their time building the same foundational features for every new project.
                        </p>
                        <p>
                            What began as a library of shared React components quickly evolved into a comprehensive marketplace. Today, we are the leading destination for production-ready source code across multiple frameworks including Vue, Angular, Flutter, and more.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Core Values --}}
    <section class="py-24 bg-slate-50 dark:bg-slate-900/30 rounded-2xl">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold">Our Core Values</h2>
                <p class="text-slate-500 mt-2">The principles that guide everything we do</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary mb-6">
                        <span class="material-symbols-outlined">verified</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Quality First</h3>
                    <p class="text-slate-600 dark:text-slate-400">Every piece of code on our platform undergoes a rigorous manual review process to ensure security and performance.</p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary mb-6">
                        <span class="material-symbols-outlined">diversity_3</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Community Driven</h3>
                    <p class="text-slate-600 dark:text-slate-400">We believe in the power of collaboration. Our roadmap is shaped by the feedback of the thousands of developers who use CodeMarket.</p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary mb-6">
                        <span class="material-symbols-outlined">lightbulb</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Constant Innovation</h3>
                    <p class="text-slate-600 dark:text-slate-400">The tech landscape changes fast. We continuously update our catalog and features to keep up with the latest industry standards.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Team Members --}}
    <section class="py-24 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold">Meet the Team</h2>
                <p class="text-slate-500 mt-2">The people behind the code</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="group text-center">
                    <div class="relative mb-4 aspect-square rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img class="h-full w-full object-cover transition-transform group-hover:scale-105" alt="Portrait of Alexander Thorne, CEO" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsufma_56kHu2Ci88dSIrRxHScUNPFKStjImYU3533cdSWWzOJnRU2ewhY6aXq1FzNEHMsqo04RNIrbwUSuw_R9xDfIOZRNNBarN1KwBkHz7IXV0Q3alGN5DMMpQm6vqDZoRuvBcqnYrSYmdk95Bm_Qr8-JKXbSBv_7kilyT4kdb1fqDsNybFOgZP7LH9ifHKI8XWdOJ9fvtuyHa-btoPNouS7tffs-0hXV2qaJmrr_Tw6p92dMF-O-AXmthxFGeg_Af12PwaQVRE"/>
                    </div>
                    <h4 class="text-lg font-bold">Alexander Thorne</h4>
                    <p class="text-primary text-sm font-medium">Founder &amp; CEO</p>
                </div>
                <div class="group text-center">
                    <div class="relative mb-4 aspect-square rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img class="h-full w-full object-cover transition-transform group-hover:scale-105" alt="Portrait of Sarah Jenkins, CTO" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC91SynLdPwkfxfh8aE6IHxbr7C9HZE0PGT2wzhXDlZDaLQSqunyl_j6AZEaP9IrS7uaNvLmsTH3Z5Nvh8c31a7XpWhPNcX6AJRqxT1mE5lr29tEF5Cjno-DlLBps6waGOi-lBRXSqm7UWzJYIJ_Ts6tLC2Hg9QG6_FhPjVCfHMJ8h9BN6OlC21gHvGYyZ22MktGuo_icn2qegB0k8q5MgmAPzOOMif9yoRBX3UHqWgwy4IXGGtKigborWBgpBDG0IXUbsIFCrUDLI"/>
                    </div>
                    <h4 class="text-lg font-bold">Sarah Jenkins</h4>
                    <p class="text-primary text-sm font-medium">CTO</p>
                </div>
                <div class="group text-center">
                    <div class="relative mb-4 aspect-square rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img class="h-full w-full object-cover transition-transform group-hover:scale-105" alt="Portrait of David Chen, Head of Engineering" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDAS_SQxWhF4TL0sgxjZ0CYqe1k-S2OcONeTdnXgGb4pOfnwM2lHV7lMiOcT1wim3XX_4RCuEuqKkkmF3dyuBloi6i2h07DEyYYpCq2MU-Z5sYwzIAQygkztgBtQY9_nuZvRwZD6FHzhuOoe22Wlhpg7mjvZW4UzTqqWJ264OWJb-629q7nwmRyidoM3-5urf7FJhVGbaUDznwFAE0xOY3VC-OyuB1sO1x3uljsHUQ1SuNMxLtRRi-Kvjhx2chkwWu7ZNTcE-CiHo8"/>
                    </div>
                    <h4 class="text-lg font-bold">David Chen</h4>
                    <p class="text-primary text-sm font-medium">Head of Engineering</p>
                </div>
                <div class="group text-center">
                    <div class="relative mb-4 aspect-square rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img class="h-full w-full object-cover transition-transform group-hover:scale-105" alt="Portrait of Elena Rodriguez, Product Designer" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAXqZzdEngtq0GlM1H1_xCJVcNrvL2EYPAK57Y1yXL0B6iNfwCYQwQ75hbX9l1mu77zkzGean5YKGpTRWxfnRRtk_FeRRusIbqyS9-fNgIxtqQM7x27Ob3fNs-pmCJvD4_QbrDHLpxb01itAnLssCynaW0qanF7MuWfAN2OSgROvyoGE5GDsJI6xKggzhgoRqKIvMv8qgJSde_Bqfe_X0V0c9XuibiVTrvpy8qYNmU0tlKCsZhvwgWKDKYba6BOD7HI1MqtDrklL0U"/>
                    </div>
                    <h4 class="text-lg font-bold">Elena Rodriguez</h4>
                    <p class="text-primary text-sm font-medium">Product Designer</p>
                </div>
            </div>
        </div>
    </section>
@endsection
