<x-admin-layout title="NDHShop - Admin - Dashboard">
                <!-- 1. Stats Row (High Density) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Stat 1 -->
                    <div class="flex flex-col gap-2 rounded-lg p-5 border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark/50">
                        <div class="flex justify-between items-start">
                            <p class="text-slate-400 text-sm font-medium">Total Revenue</p>
                            <span class="material-symbols-outlined text-primary text-[20px]">payments</span>
                        </div>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-slate-900 dark:text-white text-2xl font-bold tracking-tight">$124.5k</h3>
                            <span
                                class="text-emerald-500 text-xs font-bold bg-emerald-500/10 px-1.5 py-0.5 rounded flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-[12px]">arrow_upward</span> 12%
                            </span>
                        </div>
                    </div>
                    <!-- Stat 2 -->
                    <div class="flex flex-col gap-2 rounded-lg p-5 border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark/50">
                        <div class="flex justify-between items-start">
                            <p class="text-slate-400 text-sm font-medium">New Subs</p>
                            <span class="material-symbols-outlined text-primary text-[20px]">person_add</span>
                        </div>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-slate-900 dark:text-white text-2xl font-bold tracking-tight">843</h3>
                            <span
                                class="text-emerald-500 text-xs font-bold bg-emerald-500/10 px-1.5 py-0.5 rounded flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-[12px]">arrow_upward</span> 5.2%
                            </span>
                        </div>
                    </div>
                    <!-- Stat 3 -->
                    <div class="flex flex-col gap-2 rounded-lg p-5 border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark/50">
                        <div class="flex justify-between items-start">
                            <p class="text-slate-400 text-sm font-medium">Active Users</p>
                            <span class="material-symbols-outlined text-primary text-[20px]">group</span>
                        </div>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-slate-900 dark:text-white text-2xl font-bold tracking-tight">12.4k</h3>
                            <span
                                class="text-emerald-500 text-xs font-bold bg-emerald-500/10 px-1.5 py-0.5 rounded flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-[12px]">arrow_upward</span> 2.1%
                            </span>
                        </div>
                    </div>
                    <!-- Stat 4 -->
                    <div class="flex flex-col gap-2 rounded-lg p-5 border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark/50">
                        <div class="flex justify-between items-start">
                            <p class="text-slate-400 text-sm font-medium">Avg Session</p>
                            <span class="material-symbols-outlined text-primary text-[20px]">timer</span>
                        </div>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-slate-900 dark:text-white text-2xl font-bold tracking-tight">4m 32s</h3>
                            <span
                                class="text-rose-500 text-xs font-bold bg-rose-500/10 px-1.5 py-0.5 rounded flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-[12px]">arrow_downward</span> 1.4%
                            </span>
                        </div>
                    </div>
                    <!-- Stat 5 -->
                    <div class="flex flex-col gap-2 rounded-lg p-5 border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark/50">
                        <div class="flex justify-between items-start">
                            <p class="text-slate-400 text-sm font-medium">Tickets</p>
                            <span class="material-symbols-outlined text-primary text-[20px]">confirmation_number</span>
                        </div>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-slate-900 dark:text-white text-2xl font-bold tracking-tight">15</h3>
                            <span
                                class="text-slate-400 text-xs font-bold bg-slate-500/10 px-1.5 py-0.5 rounded flex items-center gap-0.5">
                                0.0%
                            </span>
                        </div>
                    </div>
                </div>
                <!-- 2. Charts & Detailed Health Grid -->
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <!-- Main Chart: Monthly Performance -->
                    <div class="xl:col-span-2 rounded-lg border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-slate-900 dark:text-white text-lg font-bold">Monthly Performance</h3>
                                <p class="text-slate-400 text-sm">Revenue vs Expenses comparison</p>
                            </div>
                            <select
                                class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-white text-sm rounded-lg p-2 focus:ring-0 focus:border-primary">
                                <option>This Year</option>
                                <option>Last Year</option>
                            </select>
                        </div>
                        <!-- Chart Visualization (CSS Bars) -->
                        <div class="h-64 flex items-end gap-2 sm:gap-4 md:gap-6 justify-between px-2">
                            <!-- Y-Axis Labels (Visual only) -->
                            <div
                                class="hidden sm:flex flex-col justify-between h-full text-xs text-slate-500 pb-6 -mr-2">
                                <span>100k</span>
                                <span>75k</span>
                                <span>50k</span>
                                <span>25k</span>
                                <span>0</span>
                            </div>
                            <!-- Bar Group 1: Jan -->
                            <div class="flex flex-col justify-end items-center gap-2 h-full w-full group">
                                <div class="w-full flex justify-center items-end gap-1 h-[60%]">
                                    <div
                                        class="w-3 md:w-5 bg-primary rounded-t-sm h-full opacity-80 group-hover:opacity-100 transition-opacity">
                                    </div>
                                    <div
                                        class="w-3 md:w-5 bg-slate-600 rounded-t-sm h-[45%] opacity-80 group-hover:opacity-100 transition-opacity">
                                    </div>
                                </div>
                                <span class="text-xs text-slate-500 font-medium">Jan</span>
                            </div>
                            <!-- Bar Group 2: Feb -->
                            <div class="flex flex-col justify-end items-center gap-2 h-full w-full group">
                                <div class="w-full flex justify-center items-end gap-1 h-[75%]">
                                    <div
                                        class="w-3 md:w-5 bg-primary rounded-t-sm h-full opacity-80 group-hover:opacity-100 transition-opacity">
                                    </div>
                                    <div
                                        class="w-3 md:w-5 bg-slate-600 rounded-t-sm h-[50%] opacity-80 group-hover:opacity-100 transition-opacity">
                                    </div>
                                </div>
                                <span class="text-xs text-slate-500 font-medium">Feb</span>
                            </div>
                            <!-- Bar Group 3: Mar -->
                            <div class="flex flex-col justify-end items-center gap-2 h-full w-full group">
                                <div class="w-full flex justify-center items-end gap-1 h-[55%]">
                                    <div
                                        class="w-3 md:w-5 bg-primary rounded-t-sm h-full opacity-80 group-hover:opacity-100 transition-opacity">
                                    </div>
                                    <div
                                        class="w-3 md:w-5 bg-slate-600 rounded-t-sm h-[60%] opacity-80 group-hover:opacity-100 transition-opacity">
                                    </div>
                                </div>
                                <span class="text-xs text-slate-500 font-medium">Mar</span>
                            </div>
                            <!-- Bar Group 4: Apr -->
                            <div class="flex flex-col justify-end items-center gap-2 h-full w-full group">
                                <div class="w-full flex justify-center items-end gap-1 h-[85%]">
                                    <div
                                        class="w-3 md:w-5 bg-primary rounded-t-sm h-full opacity-80 group-hover:opacity-100 transition-opacity">
                                    </div>
                                    <div
                                        class="w-3 md:w-5 bg-slate-600 rounded-t-sm h-[40%] opacity-80 group-hover:opacity-100 transition-opacity">
                                    </div>
                                </div>
                                <span class="text-xs text-slate-500 font-medium">Apr</span>
                            </div>
                            <!-- Bar Group 5: May -->
                            <div class="flex flex-col justify-end items-center gap-2 h-full w-full group">
                                <div class="w-full flex justify-center items-end gap-1 h-[65%]">
                                    <div
                                        class="w-3 md:w-5 bg-primary rounded-t-sm h-full opacity-80 group-hover:opacity-100 transition-opacity">
                                    </div>
                                    <div
                                        class="w-3 md:w-5 bg-slate-600 rounded-t-sm h-[35%] opacity-80 group-hover:opacity-100 transition-opacity">
                                    </div>
                                </div>
                                <span class="text-xs text-slate-500 font-medium">May</span>
                            </div>
                            <!-- Bar Group 6: Jun -->
                            <div class="flex flex-col justify-end items-center gap-2 h-full w-full group">
                                <div class="w-full flex justify-center items-end gap-1 h-[90%]">
                                    <div
                                        class="w-3 md:w-5 bg-primary rounded-t-sm h-full opacity-80 group-hover:opacity-100 transition-opacity">
                                    </div>
                                    <div
                                        class="w-3 md:w-5 bg-slate-600 rounded-t-sm h-[55%] opacity-80 group-hover:opacity-100 transition-opacity">
                                    </div>
                                </div>
                                <span class="text-xs text-slate-500 font-medium">Jun</span>
                            </div>
                            <!-- Bar Group 7: Jul -->
                            <div class="flex flex-col justify-end items-center gap-2 h-full w-full group">
                                <div class="w-full flex justify-center items-end gap-1 h-[70%]">
                                    <div
                                        class="w-3 md:w-5 bg-primary rounded-t-sm h-full opacity-80 group-hover:opacity-100 transition-opacity">
                                    </div>
                                    <div
                                        class="w-3 md:w-5 bg-slate-600 rounded-t-sm h-[45%] opacity-80 group-hover:opacity-100 transition-opacity">
                                    </div>
                                </div>
                                <span class="text-xs text-slate-500 font-medium">Jul</span>
                            </div>
                            <!-- Bar Group 8: Aug (Current) -->
                            <div class="flex flex-col justify-end items-center gap-2 h-full w-full group relative">
                                <div
                                    class="absolute -top-8 bg-slate-100 dark:bg-surface-dark border border-slate-200 dark:border-border-dark text-white text-[10px] py-1 px-2 rounded shadow-lg z-10 whitespace-nowrap">
                                    $92.4k</div>
                                <div class="w-full flex justify-center items-end gap-1 h-[95%]">
                                    <div class="w-3 md:w-5 bg-primary rounded-t-sm h-full"></div>
                                    <div class="w-3 md:w-5 bg-slate-600 rounded-t-sm h-[40%]"></div>
                                </div>
                                <span class="text-xs text-slate-900 dark:text-white font-bold">Aug</span>
                            </div>
                        </div>
                        <!-- Legend -->
                        <div class="flex items-center justify-center gap-6 mt-6">
                            <div class="flex items-center gap-2">
                                <div class="size-3 bg-primary rounded-sm"></div>
                                <span class="text-sm text-slate-400">Revenue</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="size-3 bg-slate-600 rounded-sm"></div>
                                <span class="text-sm text-slate-400">Expenses</span>
                            </div>
                        </div>
                    </div>
                    <!-- System Health Widget -->
                    <div class="xl:col-span-1 flex flex-col gap-4">
                        <div
                            class="flex-1 rounded-lg border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark p-6 flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-slate-900 dark:text-white text-lg font-bold">System Health</h3>
                                <div class="flex items-center gap-2">
                                    <span class="size-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                    <span class="text-emerald-500 text-sm font-medium">Stable</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-6">
                                <!-- CPU -->
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-slate-400">CPU Load</span>
                                        <span class="text-slate-900 dark:text-white font-bold">45%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 dark:bg-background-dark h-2 rounded-full overflow-hidden">
                                        <div class="bg-primary h-full rounded-full" style="width: 45%"></div>
                                    </div>
                                </div>
                                <!-- Memory -->
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-slate-400">Memory Usage</span>
                                        <span class="text-slate-900 dark:text-white font-bold">62%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 dark:bg-background-dark h-2 rounded-full overflow-hidden">
                                        <div class="bg-purple-500 h-full rounded-full" style="width: 62%"></div>
                                    </div>
                                </div>
                                <!-- Storage -->
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-slate-400">SSD Storage</span>
                                        <span class="text-slate-900 dark:text-white font-bold">88%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 dark:bg-background-dark h-2 rounded-full overflow-hidden">
                                        <div class="bg-amber-500 h-full rounded-full" style="width: 88%"></div>
                                    </div>
                                </div>
                                <!-- Latency Box -->
                                <div class="grid grid-cols-2 gap-4 mt-2">
                                    <div
                                        class="bg-background-dark p-3 rounded-lg border border-border-dark text-center">
                                        <p class="text-slate-500 text-xs uppercase font-bold tracking-wider">Latency</p>
                                        <p class="text-slate-900 dark:text-white text-xl font-bold mt-1">24ms</p>
                                    </div>
                                    <div
                                        class="bg-background-dark p-3 rounded-lg border border-border-dark text-center">
                                        <p class="text-slate-500 text-xs uppercase font-bold tracking-wider">Uptime</p>
                                        <p class="text-slate-900 dark:text-white text-xl font-bold mt-1">99.9%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 3. Kanban-style Pending Tasks -->
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-slate-900 dark:text-white text-lg font-bold">Pending Tasks</h3>
                        <button class="text-sm text-primary font-medium hover:text-blue-400">View All Tasks</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 h-full">
                        <!-- Col 1: To Do -->
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between px-1">
                                <div class="flex items-center gap-2">
                                    <span class="size-2 bg-slate-500 rounded-full"></span>
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">To Do</span>
                                </div>
                                <span class="text-xs bg-slate-800 text-slate-400 px-2 py-0.5 rounded-full">3</span>
                            </div>
                            <!-- Task Card -->
                            <div
                                class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark p-4 rounded-lg hover:border-slate-300 dark:hover:border-slate-500 transition-colors cursor-pointer group">
                                <div class="flex justify-between items-start mb-2">
                                    <span
                                        class="bg-blue-500/10 text-blue-400 text-[10px] font-bold px-2 py-0.5 rounded uppercase">Design</span>
                                    <button class="text-slate-600 hover:text-white"><span
                                            class="material-symbols-outlined text-[16px]">more_horiz</span></button>
                                </div>
                                <p class="text-slate-900 dark:text-white text-sm font-medium mb-3">Update Q3 Marketing Assets</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex -space-x-2">
                                        <div class="size-6 rounded-full bg-slate-700 border border-surface-dark bg-center bg-cover"
                                            data-alt="User avatar 1"
                                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuC6U2qKEjyikTuM4pcKKBS8-cf3xNUH9iV-1Gsmd7m67AxgJAjCtvvcVYxrjgDpKLB5zDlFhBi7UaQS2Ri0X9vsbGZ-w18T01whsP1iOJflPp-fA2tL5qZFMEbHrAPtG-UiZZ0bpG5KO5OTKm0-d6KoAfl9oYp6UmORIdz3RCctlLbA7A9-sBvMBGZicnZoJGCsZZctKtOIOQD84A9UucUSNryWAArOuXZZJOPcMVoMMFVPKTTPr_izZs1p4BXSVYNGBOUu59g7GD8");'>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 text-slate-500 text-xs">
                                        <span class="material-symbols-outlined text-[14px]">calendar_today</span> Oct 24
                                    </div>
                                </div>
                            </div>
                            <!-- Task Card -->
                            <div
                                class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark p-4 rounded-lg hover:border-slate-300 dark:hover:border-slate-500 transition-colors cursor-pointer group">
                                <div class="flex justify-between items-start mb-2">
                                    <span
                                        class="bg-purple-500/10 text-purple-400 text-[10px] font-bold px-2 py-0.5 rounded uppercase">Dev</span>
                                    <button class="text-slate-600 hover:text-white"><span
                                            class="material-symbols-outlined text-[16px]">more_horiz</span></button>
                                </div>
                                <p class="text-slate-900 dark:text-white text-sm font-medium mb-3">Fix API rate limiting bug on production
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex -space-x-2">
                                        <div class="size-6 rounded-full bg-slate-700 border border-surface-dark bg-center bg-cover"
                                            data-alt="User avatar 2"
                                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuD5OZbDJlxE5wDWhrkOyP6Szi6Gch34ZCeY2ShkMwtx-2HJMMYZfbq5253oWKcP01qCY9NL0d4ysdAPo_ag3GOqcKLtIBL_6Ic2BtjO4q5Zm2MyA9SN9k_mQJYGF-RuB-lVAJFFiEBN9TSkyQ1LTLmb9AnWJhQ81_MXeK1kIwtfT47aOtMnejOeJlj-bsrz6sTjVvaSzOI3vUjvONBZxLkXEJMlMHWKj1ShkcFQmtViJMUB3NYa2YI3hFYgxxeFNIvTy7TWhVlS3BE");'>
                                        </div>
                                        <div class="size-6 rounded-full bg-slate-700 border border-surface-dark bg-center bg-cover"
                                            data-alt="User avatar 3"
                                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCdtX11SykJkI9rERe4tFYzxPHN0IT2HPITgX7qD3HXfr-38K50FzbZSVeQGjya2AGvOkslR-1TKWjkFfxIUg-GwJuCV_HGYulK2WiYBp29FSIFWhkv1Spo4l-rvxa5nRfxCoA2G9J9LjaDCrkQf8J8FOmTkKS5QB_sLKJo__gatMcVdlBkIp0LjyJWMXHL3zLRFZeWtQ0kqQZ-FLLLYfIqawwCm_HiJBLJ5ZdPiwYBwMuKxf2a8ylU-5sidhtlwM88BSPkeDe45mM");'>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 text-amber-500 text-xs font-bold">
                                        <span class="material-symbols-outlined text-[14px]">priority_high</span> High
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Col 2: In Progress -->
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between px-1">
                                <div class="flex items-center gap-2">
                                    <span class="size-2 bg-primary rounded-full"></span>
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">In Progress</span>
                                </div>
                                <span class="text-xs bg-slate-800 text-slate-400 px-2 py-0.5 rounded-full">1</span>
                            </div>
                            <!-- Task Card -->
                            <div
                                class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark p-4 rounded-lg hover:border-slate-300 dark:hover:border-slate-500 transition-colors cursor-pointer group">
                                <div class="flex justify-between items-start mb-2">
                                    <span
                                        class="bg-emerald-500/10 text-emerald-400 text-[10px] font-bold px-2 py-0.5 rounded uppercase">System</span>
                                    <button class="text-slate-600 hover:text-white"><span
                                            class="material-symbols-outlined text-[16px]">more_horiz</span></button>
                                </div>
                                <p class="text-slate-900 dark:text-white text-sm font-medium mb-3">Database migration for v2.5</p>
                                <div class="w-full bg-slate-200 dark:bg-background-dark h-1.5 rounded-full overflow-hidden mb-3">
                                    <div class="bg-primary h-full rounded-full" style="width: 70%"></div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex -space-x-2">
                                        <div class="size-6 rounded-full bg-slate-700 border border-surface-dark bg-center bg-cover"
                                            data-alt="User avatar 4"
                                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAEvAOLORImya3mpuH6XVlcQnw4shU2hUmG29-sW3QVC9IzDZJZhpENoOTTRmsZoTo0oIrsUxa_wr25BPgJtWy43t3vq_zuizK6Q8ufjNjxgJa0fcp9Lti-5nAlzwVKmIKmu6VAYzoigAjhZ5gRZ-TIn9VAa2otU5luY59mzo7wa4A1A_oEfXGJCzrydaNhM9wCz28fSAAcEDm98YZpZHDfY8FZrFipfaZ0Ti_9hRZqonyhvv9exR9KsnyLV5bw7_dnmPXU9dAME5E");'>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 text-slate-500 text-xs">
                                        <span class="material-symbols-outlined text-[14px]">schedule</span> 2d left
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Col 3: Review -->
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between px-1">
                                <div class="flex items-center gap-2">
                                    <span class="size-2 bg-amber-500 rounded-full"></span>
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Review</span>
                                </div>
                                <span class="text-xs bg-slate-800 text-slate-400 px-2 py-0.5 rounded-full">2</span>
                            </div>
                            <!-- Task Card -->
                            <div
                                class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark p-4 rounded-lg hover:border-slate-300 dark:hover:border-slate-500 transition-colors cursor-pointer group opacity-75 hover:opacity-100">
                                <div class="flex justify-between items-start mb-2">
                                    <span
                                        class="bg-blue-500/10 text-blue-400 text-[10px] font-bold px-2 py-0.5 rounded uppercase">Content</span>
                                    <button class="text-slate-600 hover:text-white"><span
                                            class="material-symbols-outlined text-[16px]">more_horiz</span></button>
                                </div>
                                <p class="text-slate-900 dark:text-white text-sm font-medium mb-3">Newsletter Draft Approval</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex -space-x-2">
                                        <div class="size-6 rounded-full bg-slate-700 border border-surface-dark bg-center bg-cover"
                                            data-alt="User avatar 5"
                                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuA0BU_sTVx6Cs1dycf8D7WuDGGX2MNzoCN5clXYYtcQSXcEa0ACB5liaPGUwB_DiwLnHbdqCtS6yKPNefZDWeNZ3ttX45RynWF782Zg8MenNM2QjcWYUmJXbcerNxgWQYW6axgkY4dZzOh9gcqbcMPVJlwWEAptqo7y6uJ1qeDu1a8Jy1ukI8d9VN1TN6DlVo7je4GMaZrj18vUAFiPZ-6HOBKEDfWP3uljknjJFfjX2KAdLJNgPXSJjDX09dHC9Dr1FR7ipp9jzTs");'>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 text-slate-500 text-xs">
                                        <span class="material-symbols-outlined text-[14px]">check_circle</span> Ready
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
</x-admin-layout>