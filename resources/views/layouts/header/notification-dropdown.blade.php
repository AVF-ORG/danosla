{{-- Notification Dropdown Component --}}
<div class="relative" x-data="{
    dropdownOpen: false,
    notifications: @js(auth()->user()->notifications()->latest()->take(10)->get()),
    get unreadCount() {
        return this.notifications.filter(n => !n.read_at).length;
    },
    init() {
        if (window.Echo) {
            window.Echo.private(`App.Models.User.{{ auth()->id() }}`)
                .notification((notification) => {
                    console.log('New notification received:', notification);
                    
                    // Laravel/Reverb usually wraps notification data in a 'data' key.
                    // We handle both wrapped and unwrapped scenarios.
                    let payload = notification.data || notification;
                    if (payload.data) payload = payload.data;

                    this.notifications.unshift({
                        id: notification.id || ('temp-' + Date.now()),
                        data: payload,
                        created_at: notification.created_at || new Date().toISOString(),
                        read_at: null
                    });
                    if (this.notifications.length > 10) this.notifications.pop();
                });
        }
    },
    toggleDropdown() {
        this.dropdownOpen = !this.dropdownOpen;
    },
    closeDropdown() {
        this.dropdownOpen = false;
    },
    async markAllAsRead() {
        try {
            await axios.post('{{ route('notifications.mark-all-read') }}');
            this.notifications.forEach(n => n.read_at = new Date().toISOString());
        } catch (e) {
            console.error('Error marking all as read:', e);
        }
    }
}" @click.away="closeDropdown()">
    <!-- Notification Button -->
    <button
        class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-11 w-11 hover:bg-gray-100 hover:text-gray-700 dark:border-white/[0.1] dark:bg-white/[0.03] dark:text-white/60 dark:hover:bg-white/[0.08] dark:hover:text-white"
        @click="toggleDropdown()"
        type="button"
    >
        <!-- Notification Badge -->
        <template x-if="unreadCount > 0">
            <span
                class="absolute right-0 top-0.5 z-1 h-2 w-2 rounded-full bg-orange-400 ring-2 ring-white dark:ring-gray-dark"
            >
                <span
                    class="absolute inline-flex w-full h-full bg-orange-400 rounded-full opacity-75 -z-1 animate-ping"
                ></span>
            </span>
        </template>

        <!-- Bell Icon -->
        <svg
            class="fill-current"
            width="20"
            height="20"
            viewBox="0 0 20 20"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z"
                fill=""
            />
        </svg>
    </button>

    <!-- Dropdown Start -->
    <div
        x-show="dropdownOpen"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute -right-[240px] mt-[17px] flex h-[480px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark sm:w-[361px] lg:right-0"
        style="display: none;"
    >
        <!-- Dropdown Header -->
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 dark:border-gray-800">
            <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">Notification</h5>
            
            <template x-if="unreadCount > 0">
                <button @click="markAllAsRead()" class="text-xs text-brand-500 hover:underline">
                    Tout marquer comme lu
                </button>
            </template>
        </div>

        <!-- Notification List -->
        <ul class="flex flex-col h-auto overflow-y-auto custom-scrollbar">
            <template x-if="notifications.length === 0">
                <li class="p-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    Pas de notifications
                </li>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <li>
                    <a
                        class="flex gap-3 rounded-lg border-b border-gray-100 p-3 px-4.5 py-3 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-white/5 transition-colors"
                        :class="notification.read_at ? 'bg-white dark:bg-gray-900' : 'bg-brand-50/50 dark:bg-brand-900/10'"
                        :href="'{{ route('notifications.read', ['id' => '__ID__']) }}'.replace('__ID__', notification.id)"
                    >
                        <span class="relative block w-full h-10 rounded-full z-1 max-w-10 flex items-center justify-center rounded-2xl"
                            :class="notification.read_at ? 'bg-gray-100 text-gray-400' : (
                                notification.data.status_type === 'accepted' ? 'bg-success-500 text-white shadow-lg shadow-success-500/20' : (
                                    notification.data.status_type === 'rejected' ? 'bg-gray-400 text-white' : (
                                        notification.data.status_type === 'completed' ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/20' : 'bg-brand-500 text-white shadow-lg shadow-brand-500/20'
                                    )
                                )
                            )">
                            <template x-if="notification.data.status_type === 'accepted'">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </template>
                            <template x-if="notification.data.status_type === 'rejected'">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </template>
                            <template x-if="notification.data.status_type === 'bid_received'">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                            <template x-if="!notification.data.status_type || (notification.data.status_type !== 'accepted' && notification.data.status_type !== 'rejected' && notification.data.status_type !== 'bid_received')">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                        </span>

                        <span class="block min-w-0 flex-1">
                            <span class="mb-1 block text-theme-sm">
                                <span class="font-bold text-gray-800 dark:text-white/90 block truncate" x-text="notification.data.title"></span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 truncate block font-medium" x-text="notification.data.message"></span>
                            </span>

                            <!-- Details Row -->
                            <div class="flex items-center gap-x-3 mb-1.5 overflow-hidden">
                                <span class="flex items-center gap-1 text-[10px] text-gray-400 font-bold uppercase whitespace-nowrap bg-gray-50 dark:bg-gray-800 px-1.5 py-0.5 rounded-md border border-gray-100 dark:border-gray-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002-2z" />
                                    </svg>
                                    <span x-text="notification.data.pickup_at"></span>
                                </span>
                                
                                <span class="flex items-center gap-1 text-[10px] text-brand-600 font-bold uppercase whitespace-nowrap bg-brand-50 dark:bg-brand-900/20 px-1.5 py-0.5 rounded-md border border-brand-100/50 dark:border-brand-900/30">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-text="notification.data.price"></span>
                                </span>
                            </div>

                            <span class="flex items-center gap-2 text-gray-400 text-[10px] font-medium uppercase tracking-wider">
                                <span x-text="notification.data.shipper_name || 'Système'"></span>
                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                <span x-text="new Date(notification.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                            </span>
                        </span>
                    </a>
                </li>
            </template>
        </ul>

        <!-- View All Button -->
        <a
            href="{{ route('notifications.index') }}"
            class="mt-3 flex justify-center rounded-lg border border-gray-300 bg-white p-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
        >
            Voir toutes les notifications
        </a>
    </div>
    <!-- Dropdown End -->
</div>
