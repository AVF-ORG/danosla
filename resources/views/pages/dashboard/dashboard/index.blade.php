@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-12 gap-6">
        <!-- Row 1 -->
        <div class="col-span-12 lg:col-span-8">
            <div class="sneat-card p-5 md:p-6 overflow-hidden relative h-full">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="flex-1 text-center md:text-left">
                        <h4 class="text-xl font-semibold text-brand-600 mb-1">Congratulations John! 🎉</h4>
                        <p class="text-gray-500 mb-4 text-sm leading-relaxed">You have done 72% more sales today.<br>Check your new badge in your profile.</p>
                        <a href="#" class="btn-primary px-4 py-2 text-sm">View Badges</a>
                    </div>
                    <div class="mt-6 md:mt-0 md:absolute md:right-8 md:bottom-0">
                        <img src="/images/cards/sneat-congratulations-v3.png" class="h-40 md:h-48" alt="Congratulations">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-4">
            <div class="grid grid-cols-2 gap-6">
                <!-- Order Card -->
                <div class="sneat-card p-5">
                    <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider block mb-1">Order</span>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">276k</h3>
                    <div id="orderChart" class="h-10"></div>
                </div>
                <!-- Sales Card -->
                <div class="sneat-card p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded bg-info-50 flex items-center justify-center text-info-600 dark:bg-info-500/10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        </div>
                        <button class="text-gray-400 hover:text-gray-700">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                        </button>
                    </div>
                    <span class="text-sm text-gray-500 block mb-0.5">Sales</span>
                    <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-1">$4,679</h4>
                    <span class="text-success-600 text-xs font-semibold">↑ 28.42%</span>
                </div>
            </div>
        </div>

        <!-- Row 2 -->
        <div class="col-span-12 lg:col-span-8">
            <div class="sneat-card overflow-hidden">
                <div class="flex flex-col md:flex-row">
                    <!-- Main Revenue Chart -->
                    <div class="flex-1 p-5 md:p-6 border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-800 overflow-hidden">
                        <div class="flex items-center justify-between mb-4">
                            <h5 class="text-lg font-semibold text-gray-800 dark:text-white">Total Revenue</h5>
                            <button class="text-gray-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                            </button>
                        </div>
                        <div id="chartOne" class="h-[300px]"></div>
                    </div>
                    <!-- Growth Sidebar -->
                    <div class="w-full md:w-[280px] p-5 md:p-6 bg-gray-50/20 dark:bg-white/5 flex flex-col items-center justify-center">
                        <div class="text-center mb-6">
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded px-4 py-1.5 text-xs font-semibold text-gray-500 inline-flex items-center gap-2 mb-6 cursor-pointer hover:bg-gray-50">
                                2025 <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div id="growthChart" class="h-40 w-full mb-4"></div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">62% Company Growth</p>
                        </div>
                        <div class="flex items-center justify-between w-full border-t border-gray-100 dark:border-gray-800 pt-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded bg-brand-50 flex items-center justify-center text-brand-600 dark:bg-brand-500/10">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2" /></svg>
                                </div>
                                <div>
                                    <p class="text-[11px] text-gray-400 mb-0 uppercase tracking-tighter">2025</p>
                                    <p class="text-sm font-bold text-gray-800 dark:text-white">$32.5k</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded bg-info-50 flex items-center justify-center text-info-600 dark:bg-info-500/10">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18" /></svg>
                                </div>
                                <div>
                                    <p class="text-[11px] text-gray-400 mb-0 uppercase tracking-tighter">2024</p>
                                    <p class="text-sm font-bold text-gray-800 dark:text-white">$41.2k</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-4">
            <div class="grid grid-cols-2 gap-6 h-full">
                <!-- Payments Card -->
                <div class="sneat-card p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded bg-brand-50 flex items-center justify-center text-brand-600 dark:bg-brand-500/10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>
                        </div>
                        <button class="text-gray-400 hover:text-gray-700">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                        </button>
                    </div>
                    <span class="text-sm text-gray-500 block mb-0.5">Payments</span>
                    <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-1">$2,456</h4>
                    <span class="text-error-600 text-xs font-semibold">↓ 14.82%</span>
                </div>
                <!-- Revenue Card -->
                <div class="sneat-card p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded bg-info-50 flex items-center justify-center text-info-600 dark:bg-info-500/10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        </div>
                        <button class="text-gray-400 hover:text-gray-700">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                        </button>
                    </div>
                    <span class="text-sm text-gray-500 block mb-0.5">Revenue</span>
                    <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-1">425k</h4>
                    <div class="flex gap-0.5 mt-2 overflow-hidden items-end h-6">
                        <div class="w-1.5 h-2 bg-brand-100 rounded-sm"></div>
                        <div class="w-1.5 h-4 bg-brand-100 rounded-sm"></div>
                        <div class="w-1.5 h-3 bg-brand-100 rounded-sm"></div>
                        <div class="w-1.5 h-5 bg-brand-100 rounded-sm"></div>
                        <div class="w-1.5 h-6 bg-brand-600 rounded-sm"></div>
                        <div class="w-1.5 h-4 bg-brand-100 rounded-sm"></div>
                        <div class="w-1.5 h-3 bg-brand-100 rounded-sm"></div>
                    </div>
                </div>

                <!-- Profile Report -->
                <div class="col-span-2">
                    <div class="sneat-card p-5 md:p-6 h-full">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h5 class="text-lg font-semibold text-gray-800 dark:text-white mb-0">Profile Report</h5>
                                <span class="inline-block bg-warning-50 text-warning-600 text-[10px] px-2 py-0.5 rounded font-bold uppercase dark:bg-warning-500/10 mt-1">YEAR 2022</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <span class="text-success-600 text-sm font-semibold">↑ 68.2%</span>
                                <h4 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">$84,686</h4>
                            </div>
                            <div class="flex-1 max-w-[150px]">
                                <div id="chartThirteen" class="h-20"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3 -->
        <div class="col-span-12 lg:col-span-4">
            <div class="sneat-card p-5 md:p-6 h-full flex flex-col">
                <div class="flex items-center justify-between mb-2">
                    <h5 class="text-lg font-semibold text-gray-800 dark:text-white">Order Statistics</h5>
                    <button class="text-gray-400">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                    </button>
                </div>
                <p class="text-sm text-gray-500 mb-6">42.82k Total Sales</p>
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">8,258</h3>
                        <p class="text-sm text-gray-400">Total Orders</p>
                    </div>
                    <div id="chartTwo" class="w-24 h-24"></div>
                </div>
                <div class="mt-auto space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded bg-brand-50 flex items-center justify-center text-brand-600 dark:bg-brand-500/10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Electronic</p>
                                <p class="text-xs text-gray-400 leading-tighter">Mobile, Earbuds, TV</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-800 dark:text-white">82.5k</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded bg-success-50 flex items-center justify-center text-success-600 dark:bg-success-500/10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Fashion</p>
                                <p class="text-xs text-gray-400 leading-tighter">T-shirt, Jeans, Shoes</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-800 dark:text-white">23.8k</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded bg-info-50 flex items-center justify-center text-info-600 dark:bg-info-500/10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Decor</p>
                                <p class="text-xs text-gray-400 leading-tighter">Fine Art, Dining</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-800 dark:text-white">849k</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded bg-secondary-50 flex items-center justify-center text-secondary-600 dark:bg-secondary-500/10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Sports</p>
                                <p class="text-xs text-gray-400 leading-tighter">Football, Cricket Kit</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-800 dark:text-white">99k</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-4">
            <div class="sneat-card h-full overflow-hidden">
                <div x-data="{ activeTab: 'income' }" class="flex flex-col h-full">
                    <div class="p-5 md:p-6 border-b border-gray-100 dark:border-gray-800">
                        <ul class="flex items-center gap-6">
                            <li><button @click="activeTab = 'income'" :class="activeTab === 'income' ? 'text-brand-600 border-b-2 border-brand-600 pb-2' : 'text-gray-500 hover:text-gray-700 pb-2'" class="text-sm font-semibold">Income</button></li>
                            <li><button @click="activeTab = 'expenses'" :class="activeTab === 'expenses' ? 'text-brand-600 border-b-2 border-brand-600 pb-2' : 'text-gray-500 hover:text-gray-700 pb-2'" class="text-sm font-semibold">Expenses</button></li>
                            <li><button @click="activeTab = 'profit'" :class="activeTab === 'profit' ? 'text-brand-600 border-b-2 border-brand-600 pb-2' : 'text-gray-500 hover:text-gray-700 pb-2'" class="text-sm font-semibold">Profit</button></li>
                        </ul>
                    </div>
                    <div class="p-5 md:p-6 flex-1 flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 rounded bg-brand-50 flex items-center justify-center text-brand-600 dark:bg-brand-500/10">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M7 15h2c.55 0 1-.45 1-1v-4c0-.55-.45-1-1-1H7c-.55 0-1 .45-1 1v4c0 .55.45 1 1 1zm4-3c-.55 0-1 .45-1 1v1c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-1c0-.55-.45-1-1-1h-2zM3 10V8c0-1.1.9-2 2-2h14c1.1 0 2 .9 2 2v2H3zm18 2v6c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2v-6h18z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400 font-semibold mb-0">Total Balance</p>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xl font-bold text-gray-800 dark:text-white">$459.10</h4>
                                    <span class="text-success-600 text-xs font-semibold">↑ 42.9%</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 min-h-[160px]">
                            <div id="chartThree" class="h-full w-full"></div>
                        </div>
                        <div class="mt-6 flex items-center justify-center gap-3">
                            <div class="relative w-12 h-12 flex items-center justify-center">
                                <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-gray-100 dark:text-gray-800" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <path class="text-brand-600" stroke-width="3" stroke-dasharray="65, 100" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                </svg>
                                <span class="absolute text-[10px] font-bold text-gray-800 dark:text-white">$65</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white mb-0">Income this week</p>
                                <p class="text-xs text-gray-400">$39k less than last week</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-4">
            <div class="sneat-card p-5 md:p-6 h-full flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <h5 class="text-lg font-semibold text-gray-800 dark:text-white">Transactions</h5>
                    <button class="text-gray-400">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                    </button>
                </div>
                <div class="space-y-6 flex-1">
                    @foreach([
                        ['icon' => 'pay-paypal', 'title' => 'Paypal', 'subtitle' => 'Send money', 'amount' => '+82.6', 'color' => 'error'],
                        ['icon' => 'wallet', 'title' => 'Wallet', 'subtitle' => 'Mac\'D', 'amount' => '+270.69', 'color' => 'success'],
                        ['icon' => 'chart-pie', 'title' => 'Transfer', 'subtitle' => 'Refund', 'amount' => '+637.91', 'color' => 'info'],
                        ['icon' => 'credit-card', 'title' => 'Credit Card', 'subtitle' => 'Ordered Food', 'amount' => '-838.71', 'color' => 'brand'],
                        ['icon' => 'wallet', 'title' => 'Wallet', 'subtitle' => 'Starbucks', 'amount' => '+203.33', 'color' => 'success'],
                        ['icon' => 'briefcase', 'title' => 'Mastercard', 'subtitle' => 'Ordered Food', 'amount' => '-92.45', 'color' => 'warning']
                    ] as $transaction)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded bg-{{ $transaction['color'] }}-50 flex items-center justify-center text-{{ $transaction['color'] }}-600 dark:bg-{{ $transaction['color'] }}-500/10">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                </div>
                                <div class="flex flex-col">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white leading-tight">{{ $transaction['title'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $transaction['subtitle'] }}</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <p class="text-sm font-bold text-gray-800 dark:text-white leading-tight">{{ $transaction['amount'] }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">USD</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        </div>

        <!-- Row 4 -->
        <div class="grid grid-cols-12 gap-6 mt-6">
            <div class="col-span-12 lg:col-span-7">
                <div class="sneat-card p-5 md:p-6 h-full">
                    <div class="flex items-center justify-between mb-8">
                        <h5 class="text-lg font-semibold text-gray-800 dark:text-white">Activity Timeline</h5>
                        <button class="text-gray-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                        </button>
                    </div>
                    <div class="relative pl-8 space-y-10 after:content-[''] after:absolute after:left-3 after:top-1 after:bottom-1 after:w-[2px] after:bg-gray-100 dark:after:bg-gray-800">
                        <div class="relative">
                            <div class="absolute -left-[35px] top-1 w-3 h-3 rounded-full bg-brand-500 ring-4 ring-brand-50 dark:ring-brand-500/10 z-10 transition-all"></div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">12 Invoices have been paid</p>
                                <span class="text-xs text-gray-400">12 min ago</span>
                            </div>
                            <p class="text-sm text-gray-500 mb-4 font-medium">Invoices have been paid to the company</p>
                            <div class="flex items-center gap-3 p-2 bg-gray-50 dark:bg-gray-900 rounded border border-gray-100 dark:border-gray-800 w-fit">
                                <svg class="w-6 h-6 text-error-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm1 9h-4v2h4v-2zm0 4h-4v2h4v-2zm-5-8h5v5h-5V7z"/></svg>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-400">invoices.pdf</span>
                            </div>
                        </div>
                        <div class="relative">
                            <div class="absolute -left-[35px] top-1 w-3 h-3 rounded-full bg-info-500 ring-4 ring-info-50 dark:ring-info-500/10 z-10"></div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Client Meeting</p>
                                <span class="text-xs text-gray-400">45 min ago</span>
                            </div>
                            <p class="text-sm text-gray-500 mb-4 font-medium">Project meeting with john @10:15am</p>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold text-[10px]">JS</div>
                                <div>
                                    <p class="text-xs font-bold text-gray-800 dark:text-white uppercase">John Smith (Client)</p>
                                    <p class="text-[10px] text-gray-400">CEO of InfiniCorp</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-12 lg:col-span-5">
                <div class="sneat-card h-full overflow-hidden">
                    <div x-data="{ activeTab: 'browser' }" class="flex flex-col h-full">
                        <div class="p-5 md:p-6 border-b border-gray-100 dark:border-gray-800">
                            <ul class="flex items-center gap-6">
                                <li><button @click="activeTab = 'browser'" :class="activeTab === 'browser' ? 'text-brand-600 border-b-2 border-brand-600 pb-2' : 'text-gray-500 hover:text-gray-700 pb-2'" class="text-xs font-bold uppercase tracking-widest">Browser</button></li>
                                <li><button @click="activeTab = 'os'" :class="activeTab === 'os' ? 'text-brand-600 border-b-2 border-brand-600 pb-2' : 'text-gray-500 hover:text-gray-700 pb-2'" class="text-xs font-bold uppercase tracking-widest">Operating System</button></li>
                                <li><button @click="activeTab = 'country'" :class="activeTab === 'country' ? 'text-brand-600 border-b-2 border-brand-600 pb-2' : 'text-gray-500 hover:text-gray-700 pb-2'" class="text-xs font-bold uppercase tracking-widest">Country</button></li>
                            </ul>
                        </div>
                        <div class="p-5 md:p-6 flex-1">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-[10px] text-gray-400 uppercase font-bold border-b border-gray-50 dark:border-gray-800/50">
                                        <th class="pb-3">No</th>
                                        <th class="pb-3">Browser</th>
                                        <th class="pb-3">Visits</th>
                                        <th class="pb-3 text-right">Data</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                                    @foreach([
                                        ['no' => 1, 'name' => 'Chrome', 'visits' => '8.92k', 'percent' => '64.91%', 'color' => 'success'],
                                        ['no' => 2, 'name' => 'Safari', 'visits' => '1.29k', 'percent' => '19.03%', 'color' => 'brand'],
                                        ['no' => 3, 'name' => 'Firefox', 'visits' => '328', 'percent' => '3.26%', 'color' => 'info'],
                                        ['no' => 4, 'name' => 'Edge', 'visits' => '142', 'percent' => '1.56%', 'color' => 'warning']
                                    ] as $browser)
                                        <tr>
                                            <td class="py-4 text-sm font-bold text-gray-500">{{ $browser['no'] }}</td>
                                            <td class="py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center text-orange-600 dark:bg-orange-500/10">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/></svg>
                                                    </div>
                                                    <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $browser['name'] }}</span>
                                                </div>
                                            </td>
                                            <td class="py-4 text-sm font-bold text-gray-800 dark:text-white">{{ $browser['visits'] }}</td>
                                            <td class="py-4">
                                                <div class="flex items-center gap-3 justify-end">
                                                    <div class="w-24 h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                                        <div class="h-full bg-{{ $browser['color'] }}-500 rounded-full" style="width: {{ $browser['percent'] }}"></div>
                                                    </div>
                                                    <span class="text-xs font-bold text-gray-500">{{ $browser['percent'] }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
