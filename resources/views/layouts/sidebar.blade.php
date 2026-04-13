@php
    $currentPath = request()->path();
    $locale = LaravelLocalization::getCurrentLocale();
    $isRtl = LaravelLocalization::getCurrentLocaleDirection() === 'rtl';
    $submenuIndentClass = $isRtl ? 'me-5' : 'ms-5';
    //   $submenuIndentClass = $isRtl ? 'mr-20' : 'ml-20';
@endphp


<aside id="sidebar"
    class="fixed flex flex-col mt-0 top-0 px-5 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-99999
           {{ $isRtl ? 'right-0 border-l' : 'left-0 border-r' }} border-gray-200"
    x-data="{
        openSubmenus: {},
        init() { this.initializeActiveMenus(); },
        initializeActiveMenus() {
            this.$nextTick(() => {
                const activeDropdown = this.$el.querySelector('.menu-dropdown-item-active');
                if (!activeDropdown) return;
    
                const wrapper = activeDropdown.closest('[data-submenu-key]');
                if (!wrapper) return;
    
                this.openSubmenus[wrapper.dataset.submenuKey] = true;
            });
        },
        toggleSubmenu(key) {
            const newState = !this.openSubmenus[key];
            if (newState) this.openSubmenus = {};
            this.openSubmenus[key] = newState;
        },
        isSubmenuOpen(key) { return this.openSubmenus[key] || false; },
        {{-- isActive(path) { return window.location.pathname === path || '{{ $currentPath }}' === path.replace(/^\//, ''); } --}}
        isActive(path) {
            const baseTarget = path.split('?')[0];
            return window.location.pathname.endsWith(baseTarget);
        }
    }"
    :class="{
        'w-[290px]': $store.sidebar.isExpanded || $store.sidebar.isMobileOpen || $store.sidebar.isHovered,
        'w-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
        'translate-x-0': $store.sidebar.isMobileOpen,
        '{{ $isRtl ? 'translate-x-full xl:translate-x-0' : '-translate-x-full xl:translate-x-0' }}': !$store.sidebar
            .isMobileOpen
    }"
    @mouseenter="if (!$store.sidebar.isExpanded) $store.sidebar.setHovered(true)"
    @mouseleave="$store.sidebar.setHovered(false)">

    <!-- Logo Section -->
    <div class="pt-8 pb-7 flex"
        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
        'xl:justify-center' : 'justify-start'">
        <a href="/">
            <img x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                class="dark:hidden" src="{{ asset('images/logo/logo.svg') }}" alt="Logo" width="150"
                height="40" />
            <img x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                class="hidden dark:block" src="{{ asset('images/logo/logo-dark.svg') }}" alt="Logo" width="150"
                height="40" />
            <img x-show="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen"
                src="{{ asset('images/logo/logo-icon.svg') }}" alt="Logo" width="32" height="32" />
        </a>
    </div>

    <!-- Navigation Menu -->
    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <nav class="mb-6">
            <div class="flex flex-col gap-4">

                <!-- ===================== GROUP: Dashboard ===================== -->
                <div>
                    <h2 class="mb-4 text-xs uppercase flex leading-[20px] text-gray-400"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                        'lg:justify-center' : 'justify-start'">

                        <template
                            x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                            <div class="flex items-center gap-3 w-full">
                                <span class="w-4 h-px bg-gray-300 dark:bg-gray-700"></span>
                                <span class="whitespace-nowrap">Dashboard</span>
                            </div>
                        </template>

                        <template
                            x-if="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
                                    fill="currentColor" />
                            </svg>
                        </template>
                    </h2>

                    <ul class="flex flex-col gap-1">
                        <!-- Dashboard -->
                        <li>
                            <a href="{{ route('dashboard.index') }}" class="menu-item group"
                                :class="[
                                    isActive('/dashboard') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                ]">
                                <span
                                    :class="isActive('/dashboard') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Dashboard
                                </span>
                            </a>
                        </li>

                        <!-- Products Firm Bids -->
                        <li>
                            <a href="{{ route('transport-firm-bid.index') }}" class="menu-item group"
                                :class="[
                                    isActive('/transport-firm-bid') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                ]">
                                <span
                                    :class="isActive('/transport-firm-bid') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M3.25 6.5C3.25 5.67157 3.7835 4.93265 4.57115 4.66312L11.3212 2.33079C11.7584 2.17896 12.2416 2.17896 12.6788 2.33079L19.4288 4.66312C20.2165 4.93265 20.75 5.67157 20.75 6.5V17.5C20.75 18.3284 20.2165 19.0674 19.4288 19.3369L12.6788 21.6692C12.2416 21.821 11.7584 21.821 11.3212 21.6692L4.57115 19.3369C3.7835 19.0674 3.25 18.3284 3.25 17.5V6.5ZM12 3.75L5.5 6L12 8.25L18.5 6L12 3.75Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M14.5 11.25C14.0858 11.25 13.75 11.5858 13.75 12V13H13.25C12.4216 13 11.75 13.6716 11.75 14.5V17C11.75 17.8284 12.4216 18.5 13.25 18.5H17.25C18.0784 18.5 18.75 17.8284 18.75 17V14.5C18.75 13.6716 18.0784 13 17.25 13H16.75V12C16.75 11.5858 16.4142 11.25 16 11.25C15.5858 11.25 15.25 11.5858 15.25 12V13H15.25V12C15.25 11.5858 14.9142 11.25 14.5 11.25Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Transport Firm Bids
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- ===================== GROUP: Contact ===================== -->
                <div>
                    <h2 class="mb-4 text-xs uppercase flex leading-[20px] text-gray-400"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                        'lg:justify-center' : 'justify-start'">

                        <template
                            x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                            <div class="flex items-center gap-3 w-full">
                                <span class="w-4 h-px bg-gray-300 dark:bg-gray-700"></span>
                                <span class="whitespace-nowrap">Contacts</span>
                            </div>
                        </template>

                        <template
                            x-if="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
                                    fill="currentColor" />
                            </svg>
                        </template>
                    </h2>

                    <ul class="flex flex-col gap-1">
                        <li>
                            <a href="{{ route('dashboard.contacts.index') }}" class="menu-item group"
                                :class="[
                                    isActive('/dashboard/contacts') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                ]">
                                <span
                                    :class="isActive('/dashboard/contacts') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M3 6.5C3 5.67157 3.67157 5 4.5 5H19.5C20.3284 5 21 5.67157 21 6.5V15.5C21 16.3284 20.3284 17 19.5 17H14L12 19L10 17H4.5C3.67157 17 3 16.3284 3 15.5V6.5Z"
                                            stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                        <path d="M3 7L12 12L21 7" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" />
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Contact Messages
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- ===================== GROUP: Users ===================== -->
                <div>
                    <h2 class="mb-4 text-xs uppercase flex leading-[20px] text-gray-400"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                        'lg:justify-center' : 'justify-start'">

                        <template
                            x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                            <div class="flex items-center gap-3 w-full">
                                <span class="w-4 h-px bg-gray-300 dark:bg-gray-700"></span>
                                <span class="whitespace-nowrap">Users</span>
                            </div>
                        </template>

                        <template
                            x-if="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
                                    fill="currentColor" />
                            </svg>
                        </template>
                    </h2>

                    <ul class="flex flex-col gap-1">
                        <!-- Pending users -->
                        <li>
                            <a href="{{ route('dashboard.users.pending') }}" class="menu-item group"
                                :class="[
                                    isActive('/dashboard/users/pending') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                ]">
                                <span
                                    :class="isActive('/dashboard/users/pending') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M9 3.5C7.34315 3.5 6 4.84315 6 6.5C6 8.15685 7.34315 9.5 9 9.5C10.6569 9.5 12 8.15685 12 6.5C12 4.84315 10.6569 3.5 9 3.5Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4 17C4 14.5147 6.51472 12.5 9 12.5C11.4853 12.5 14 14.5147 14 17V18C14 18.4142 13.6642 18.75 13.25 18.75H4.75C4.33579 18.75 4 18.4142 4 18V17Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M17.5 10C14.7386 10 12.5 12.2386 12.5 15C12.5 17.7614 14.7386 20 17.5 20C20.2614 20 22.5 17.7614 22.5 15C22.5 12.2386 20.2614 10 17.5 10ZM14 15C14 13.067 15.567 11.5 17.5 11.5C19.433 11.5 21 13.067 21 15C21 16.933 19.433 18.5 17.5 18.5C15.567 18.5 14 16.933 14 15Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M17.5 12.75C17.9142 12.75 18.25 13.0858 18.25 13.5V14.6893L19.0303 15.4697C19.3232 15.7626 19.3232 16.2374 19.0303 16.5303C18.7374 16.8232 18.2626 16.8232 17.9697 16.5303L17 15.5607C16.8593 15.42 16.7803 15.2293 16.7803 15.0303V13.5C16.7803 13.0858 17.0858 12.75 17.5 12.75Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Pending users
                                </span>
                            </a>
                        </li>

                        <!-- Transporters -->
                        <li>
                            <a href="{{ route('dashboard.users.transporters') }}" class="menu-item group"
                                :class="[
                                    isActive('{{ route('dashboard.users.transporters', [], false) }}') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                ]">
                                <span
                                    :class="isActive('/calendar') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M2.75 6.5C2.75 5.25736 3.75736 4.25 5 4.25H14C15.2426 4.25 16.25 5.25736 16.25 6.5V7.75H18.1569C18.7545 7.75 19.3194 8.02024 19.6944 8.48548L21.5375 10.7816C21.8577 11.1806 22.0312 11.6764 22.0312 12.1875V16C22.0312 17.2426 21.0239 18.25 19.7812 18.25H19.25C19.25 19.4926 18.2426 20.5 17 20.5C15.7574 20.5 14.75 19.4926 14.75 18.25H8.25C8.25 19.4926 7.24264 20.5 6 20.5C4.75736 20.5 3.75 19.4926 3.75 18.25H3.5C2.25736 18.25 1.25 17.2426 1.25 16V6.5H2.75ZM16.25 9.25V14.75H21.25V12.1875L19.4069 9.89139C19.3145 9.7766 19.1733 9.75 19.0312 9.75H16.25ZM6 19C6.41421 19 6.75 18.6642 6.75 18.25C6.75 17.8358 6.41421 17.5 6 17.5C5.58579 17.5 5.25 17.8358 5.25 18.25C5.25 18.6642 5.58579 19 6 19ZM17 19C17.4142 19 17.75 18.6642 17.75 18.25C17.75 17.8358 17.4142 17.5 17 17.5C16.5858 17.5 16.25 17.8358 16.25 18.25C16.25 18.6642 16.5858 19 17 19Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Transporters
                                </span>
                            </a>
                        </li>

                        <!-- Customers / Transporters -->
                        <li>
                            <a href="{{ route('dashboard.users.customers') }}" class="menu-item group"
                                :class="[
                                    isActive('{{ route('dashboard.users.customers', [], false) }}') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                ]">
                                <span
                                    :class="isActive('/calendar') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M9 3.5C7.34315 3.5 6 4.84315 6 6.5C6 8.15685 7.34315 9.5 9 9.5C10.6569 9.5 12 8.15685 12 6.5C12 4.84315 10.6569 3.5 9 3.5Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4 17C4 14.5147 6.51472 12.5 9 12.5C11.4853 12.5 14 14.5147 14 17V18C14 18.4142 13.6642 18.75 13.25 18.75H4.75C4.33579 18.75 4 18.4142 4 18V17Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M14.5 10.25H18.1569C18.7545 10.25 19.3194 10.5202 19.6944 10.9855L21.5375 13.2816C21.8577 13.6806 22.0312 14.1764 22.0312 14.6875V17.25C22.0312 18.4926 21.0239 19.5 19.7812 19.5H19.25C19.25 20.7426 18.2426 21.75 17 21.75C15.7574 21.75 14.75 20.7426 14.75 19.5H14.5V10.25ZM15.5 11.75V16H21V14.6875L19.1569 12.3914C19.0645 12.2766 18.9233 12.25 18.7812 12.25H15.5Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M17 20.25C17.4142 20.25 17.75 19.9142 17.75 19.5C17.75 19.0858 17.4142 18.75 17 18.75C16.5858 18.75 16.25 19.0858 16.25 19.5C16.25 19.9142 16.5858 20.25 17 20.25Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Customers / Transporters
                                </span>
                            </a>
                        </li>

                        <!-- Admins -->
                        <li>
                            <a href="{{ route('dashboard.users.admins') }}" class="menu-item group"
                                :class="[
                                    isActive('{{ route('dashboard.users.admins', [], false) }}') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                ]">
                                <span
                                    :class="isActive('/calendar') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M10 3.5C8.34315 3.5 7 4.84315 7 6.5C7 8.15685 8.34315 9.5 10 9.5C11.6569 9.5 13 8.15685 13 6.5C13 4.84315 11.6569 3.5 10 3.5Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.5 17C4.5 14.5147 7.01472 12.5 10 12.5C12.9853 12.5 15.5 14.5147 15.5 17V18C15.5 18.4142 15.1642 18.75 14.75 18.75H5.25C4.83579 18.75 4.5 18.4142 4.5 18V17Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M16.5 10.75C14.8431 10.75 13.5 12.0931 13.5 13.75C13.5 15.4069 14.8431 16.75 16.5 16.75C18.1569 16.75 19.5 15.4069 19.5 13.75C19.5 12.0931 18.1569 10.75 16.5 10.75ZM15 13.75C15 12.9216 15.6716 12.25 16.5 12.25C17.3284 12.25 18 12.9216 18 13.75C18 14.5784 17.3284 15.25 16.5 15.25C15.6716 15.25 15 14.5784 15 13.75Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Admins
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- ===================== GROUP: Statistics & Satisfaction ===================== -->
                <div>
                    <h2 class="mb-4 text-xs uppercase flex leading-[20px] text-gray-400"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                        'lg:justify-center' : 'justify-start'">

                        <template
                            x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                            <div class="flex items-center gap-3 w-full">
                                <span class="w-4 h-px bg-gray-300 dark:bg-gray-700"></span>
                                <span class="whitespace-nowrap">Statistics & Satisfaction</span>
                            </div>
                        </template>

                        <template
                            x-if="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
                                    fill="currentColor" />
                            </svg>
                        </template>
                    </h2>

                    <ul class="flex flex-col gap-1">
                        <li>
                            <a href="/calendar" class="menu-item group"
                                :class="[
                                    isActive('/calendar') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                ]">
                                <span
                                    :class="isActive('/calendar') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M12 3.5C10.067 3.5 8.5 5.067 8.5 7C8.5 8.933 10.067 10.5 12 10.5C13.933 10.5 15.5 8.933 15.5 7C15.5 5.067 13.933 3.5 12 3.5Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M6.5 18C6.5 14.9624 9.46243 12.5 12 12.5C14.5376 12.5 17.5 14.9624 17.5 18V19C17.5 19.4142 17.1642 19.75 16.75 19.75H7.25C6.83579 19.75 6.5 19.4142 6.5 19V18Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M19.75 8.5C19.8819 8.23527 20.1181 8.23527 20.25 8.5L21.4861 10.9775C21.5399 11.0853 21.6469 11.1596 21.766 11.1767L24.5 11.5666C24.7929 11.6094 24.9092 11.9623 24.6966 12.1675L22.7361 14.0606C22.6536 14.1403 22.6162 14.256 22.6357 14.3694L23.118 17.1796C23.1689 17.4761 22.8652 17.6993 22.6045 17.5639L20.25 16.334C20.1422 16.2777 19.8578 16.2777 19.75 16.334L17.3955 17.5639C17.1348 17.6993 16.8311 17.4761 16.882 17.1796L17.3643 14.3694C17.3838 14.256 17.3464 14.1403 17.2639 14.0606L15.3034 12.1675C15.0908 11.9623 15.2071 11.6094 15.5 11.5666L18.234 11.1767C18.3531 11.1596 18.4601 11.0853 18.5139 10.9775L19.75 8.5Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Customer Satisfaction
                                </span>
                            </a>
                        </li>

                        <li>
                            <a href="/calendar" class="menu-item group"
                                :class="[
                                    isActive('/calendar') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                ]">
                                <span
                                    :class="isActive('/calendar') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4 3.25C4.41421 3.25 4.75 3.58579 4.75 4V19.25H20C20.4142 19.25 20.75 19.5858 20.75 20C20.75 20.4142 20.4142 20.75 20 20.75H4C3.58579 20.75 3.25 20.4142 3.25 20V4C3.25 3.58579 3.58579 3.25 4 3.25Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M7 16.75C6.58579 16.75 6.25 16.4142 6.25 16V11C6.25 10.5858 6.58579 10.25 7 10.25C7.41421 10.25 7.75 10.5858 7.75 11V16C7.75 16.4142 7.41421 16.75 7 16.75Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M11 16.75C10.5858 16.75 10.25 16.4142 10.25 16V8C10.25 7.58579 10.5858 7.25 11 7.25C11.4142 7.25 11.75 7.58579 11.75 8V16C11.75 16.4142 11.4142 16.75 11 16.75Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M15 16.75C14.5858 16.75 14.25 16.4142 14.25 16V13C14.25 12.5858 14.5858 12.25 15 12.25C15.4142 12.25 15.75 12.5858 15.75 13V16C15.75 16.4142 15.4142 16.75 15 16.75Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M19 16.75C18.5858 16.75 18.25 16.4142 18.25 16V6C18.25 5.58579 18.5858 5.25 19 5.25C19.4142 5.25 19.75 5.58579 19.75 6V16C19.75 16.4142 19.4142 16.75 19 16.75Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Statistics
                                </span>
                            </a>
                        </li>

                        <li>
                            <a href="/calendar" class="menu-item group"
                                :class="[
                                    isActive('/calendar') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                ]">
                                <span
                                    :class="isActive('/calendar') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M7 2.75C5.75736 2.75 4.75 3.75736 4.75 5V19C4.75 20.2426 5.75736 21.25 7 21.25H17C18.2426 21.25 19.25 20.2426 19.25 19V8.12132C19.25 7.52458 19.0129 6.95228 18.591 6.53033L15.4697 3.40901C15.0477 2.98705 14.4754 2.75 13.8787 2.75H7ZM6.25 5C6.25 4.58579 6.58579 4.25 7 4.25H13.5V7C13.5 8.24264 14.5074 9.25 15.75 9.25H17.75V19C17.75 19.4142 17.4142 19.75 17 19.75H7C6.58579 19.75 6.25 19.4142 6.25 19V5ZM15 5.06066L16.9393 7H15.75C15.3358 7 15 6.66421 15 6.25V5.06066Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M9.75 11.25C10.1642 11.25 10.5 11.5858 10.5 12C10.5 12.1989 10.421 12.3897 10.2803 12.5303L9.56066 13.25L10.2803 13.9697C10.5732 14.2626 10.5732 14.7374 10.2803 15.0303C9.98744 15.3232 9.51256 15.3232 9.21967 15.0303L8.5 14.3107L7.78033 15.0303C7.48744 15.3232 7.01256 15.3232 6.71967 15.0303C6.42678 14.7374 6.42678 14.2626 6.71967 13.9697L7.43934 13.25L6.71967 12.5303C6.42678 12.2374 6.42678 11.7626 6.71967 11.4697C7.01256 11.1768 7.48744 11.1768 7.78033 11.4697L8.5 12.1893L9.21967 11.4697C9.36032 11.329 9.55108 11.25 9.75 11.25Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M13.25 11.75C13.25 11.3358 13.5858 11 14 11H16.75C17.1642 11 17.5 11.3358 17.5 11.75V14.5C17.5 14.9142 17.1642 15.25 16.75 15.25C16.3358 15.25 16 14.9142 16 14.5V13.5607L14.7803 14.7803C14.4874 15.0732 14.0126 15.0732 13.7197 14.7803C13.4268 14.4874 13.4268 14.0126 13.7197 13.7197L14.9393 12.5H14C13.5858 12.5 13.25 12.1642 13.25 11.75Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Export My Firm Bids
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- ===================== GROUP: System ===================== -->
                <div>
                    <h2 class="mb-4 text-xs uppercase flex leading-[20px] text-gray-400"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                        'lg:justify-center' : 'justify-start'">

                        <template
                            x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                            <div class="flex items-center gap-3 w-full">
                                <span class="w-4 h-px bg-gray-300 dark:bg-gray-700"></span>
                                <span class="whitespace-nowrap">System</span>
                            </div>
                        </template>

                        <template
                            x-if="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
                                    fill="currentColor" />
                            </svg>
                        </template>
                    </h2>

                    <ul class="flex flex-col gap-1">
                        <!-- Translations (submenu) -->
                        <li>
                            <button @click="toggleSubmenu('3-0')" class="menu-item group w-full"
                                :class="[
                                    isSubmenuOpen('3-0') ? 'menu-item-active' : 'menu-item-inactive',
                                    !$store.sidebar.isExpanded && !$store.sidebar.isHovered ? 'xl:justify-center' :
                                    'xl:justify-start'
                                ]">
                                <span
                                    :class="isSubmenuOpen('3-0') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2ZM3.5 12C3.5 7.30558 7.30558 3.5 12 3.5C16.6944 3.5 20.5 7.30558 20.5 12C20.5 16.6944 16.6944 20.5 12 20.5C7.30558 20.5 3.5 16.6944 3.5 12Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M12 3.5C9.51472 3.5 7.5 7.30558 7.5 12C7.5 16.6944 9.51472 20.5 12 20.5C14.4853 20.5 16.5 16.6944 16.5 12C16.5 7.30558 14.4853 3.5 12 3.5ZM9 12C9 8.41015 10.3431 5 12 5C13.6569 5 15 8.41015 15 12C15 15.5899 13.6569 19 12 19C10.3431 19 9 15.5899 9 12Z"
                                            fill="currentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4 12C4 11.5858 4.33579 11.25 4.75 11.25H19.25C19.6642 11.25 20 11.5858 20 12C20 12.4142 19.6642 12.75 19.25 12.75H4.75C4.33579 12.75 4 12.4142 4 12Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Management Translation
                                </span>

                                <svg x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="ms-auto w-5 h-5 transition-transform duration-200"
                                    :class="{ 'rotate-180 text-brand-500': isSubmenuOpen('3-0') }" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div data-submenu-key="3-0"
                                x-show="isSubmenuOpen('3-0') && ($store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen)">
                                <ul class="mt-3 space-y-1  {{ $submenuIndentClass }}">
                                    <li>
                                        <a href="{{ route('dashboard.localization.languages.index') }}"
                                            class="menu-dropdown-item"
                                            :class="isActive('/dashboard/localization/languages') ?
                                                'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                                            Languages
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('dashboard.localization.keys.index') }}"
                                            class="menu-dropdown-item"
                                            :class="isActive('/dashboard/localization/keys') ? 'menu-dropdown-item-active' :
                                                'menu-dropdown-item-inactive'">
                                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                                            Keys
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('dashboard.localization.translations.index') }}"
                                            class="menu-dropdown-item"
                                            :class="isActive('/dashboard/localization/translations') ?
                                                'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                                            Translations
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <!-- Sectors -->
                        <li>
                            <a href="{{ route('dashboard.sectors.index') }}" class="menu-item group"
                                :class="[
                                    isActive('/dashboard/sectors') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ?
                                    'xl:justify-center' :
                                    'justify-start'
                                ]">
                                <span
                                    :class="isActive('/dashboard/sectors') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M12 3 3 7.5 12 12l9-4.5L12 3Zm0 10L3 8.5V13l9 4.5 9-4.5V8.5L12 13Zm0 6L3 14.5V19l9 4.5 9-4.5v-4.5L12 19Z"
                                            fill="currentColor" />
                                    </svg>

                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Sectors
                                </span>
                            </a>
                        </li>

                        <!-- Contact Subjects -->
                        <li>
                            <a href="{{ route('dashboard.contact-subjects.index') }}" class="menu-item group"
                                :class="[
                                    isActive('/dashboard/contact-subjects') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ?
                                    'xl:justify-center' :
                                    'justify-start'
                                ]">
                                <span
                                    :class="isActive('/dashboard/contact-subjects') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"
                                            fill="currentColor" />
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Contact Subject
                                </span>
                            </a>
                        </li>


                        {{-- <!-- Regions -->
                        <li>
                            <a href="{{ route('dashboard.regions.index') }}" class="menu-item group"
                                :class="[
                                    isActive('/dashboard/regions') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ?
                                    'xl:justify-center' :
                                    'justify-start'
                                ]">
                                <span
                                    :class="isActive('/dashboard/regions') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Regions
                                </span>
                            </a>
                        </li>

                        <!-- Countries -->
                        <li>
                            <a href="{{ route('dashboard.countries.index') }}" class="menu-item group"
                                :class="[
                                    isActive('/dashboard/countries') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ?
                                    'xl:justify-center' :
                                    'justify-start'
                                ]">
                                <span
                                    :class="isActive('/dashboard/countries') ? 'menu-item-icon-active' :
                                        'menu-item-icon-inactive'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18.5c-4.687 0-8.5-3.813-8.5-8.5S7.313 3.5 12 3.5s8.5 3.813 8.5 8.5-3.813 8.5-8.5 8.5z"
                                            fill="currentColor" />
                                        <path
                                            d="M12 3.5c-2.485 0-4.5 3.806-4.5 8.5s2.015 8.5 4.5 8.5 4.5-3.806 4.5-8.5-2.015-8.5-4.5-8.5z"
                                            stroke="currentColor" stroke-width="1.5" />
                                        <path d="M3.5 12h17" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" />
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Countries
                                </span>
                            </a>
                        </li>

                        <!-- Region-Countries -->
                        <li>
                            <a href="{{ route('dashboard.region-countries.index') }}" class="menu-item group"
                                :class="[
                                    isActive('/dashboard/region-countries') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ?
                                    'xl:justify-center' :
                                    'justify-start'
                                ]">
                                <span
                                    :class="isActive('/dashboard/region-countries') ? 'menu-item-icon-active' :
                                        'menu-item-icon-inactive'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 1.5c4.687 0 8.5 3.813 8.5 8.5s-3.813 8.5-8.5 8.5S3.5 16.687 3.5 12 7.313 3.5 12 3.5z"
                                            fill="currentColor" />
                                        <path d="M12 3.5v17M3.5 12h17" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" />
                                        <path
                                            d="M7.5 7.5c1.5 1 3 1.5 4.5 1.5s3-.5 4.5-1.5M7.5 16.5c1.5-1 3-1.5 4.5-1.5s3 .5 4.5 1.5"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Region-Countries
                                </span>
                            </a>
                        </li> --}}

                        <!-- Geography (submenu) -->
                        <li>
                            <button @click="toggleSubmenu('geo')" class="menu-item group w-full"
                                :class="[
                                    isSubmenuOpen('geo') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered) ? 'xl:justify-center' :
                                    'xl:justify-start'
                                ]">

                                <span
                                    :class="isSubmenuOpen('geo') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">

                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M3 12h18" />
                                        <path d="M12 3c3 3 3 15 0 18" />
                                    </svg>


                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Geography
                                </span>

                                <svg x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="ms-auto w-5 h-5 transition-transform duration-200"
                                    :class="{ 'rotate-180 text-brand-500': isSubmenuOpen('geo') }" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div data-submenu-key="geo"
                                x-show="isSubmenuOpen('geo') && ($store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen)">
                                <ul class="mt-3 space-y-1  {{ $submenuIndentClass }}">
                                    <li>
                                        <a href="{{ route('dashboard.regions.index') }}" class="menu-dropdown-item"
                                            :class="isActive('/dashboard/regions') ? 'menu-dropdown-item-active' :
                                                'menu-dropdown-item-inactive'">
                                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                                            Regions
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ route('dashboard.countries.index') }}" class="menu-dropdown-item"
                                            :class="isActive('/dashboard/countries') ? 'menu-dropdown-item-active' :
                                                'menu-dropdown-item-inactive'">
                                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                                            Countries
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ route('dashboard.region-countries.index') }}"
                                            class="menu-dropdown-item"
                                            :class="isActive('/dashboard/region-countries') ? 'menu-dropdown-item-active' :
                                                'menu-dropdown-item-inactive'">
                                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                                            Region Countries
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>



                        <!-- Calendar -->
                        <li>
                            <a href="/calendar" class="menu-item group"
                                :class="[
                                    isActive('/calendar') ? 'menu-item-active' : 'menu-item-inactive',
                                    (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar
                                        .isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                ]">
                                <span
                                    :class="isActive('/calendar') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M8 2C8.41421 2 8.75 2.33579 8.75 2.75V3.75H15.25V2.75C15.25 2.33579 15.5858 2 16 2C16.4142 2 16.75 2.33579 16.75 2.75V3.75H18.5C19.7426 3.75 20.75 4.75736 20.75 6V9V19C20.75 20.2426 19.7426 21.25 18.5 21.25H5.5C4.25736 21.25 3.25 20.2426 3.25 19V9V6C3.25 4.75736 4.25736 3.75 5.5 3.75H7.25V2.75C7.25 2.33579 7.58579 2 8 2ZM8 5.25H5.5C5.08579 5.25 4.75 5.58579 4.75 6V8.25H19.25V6C19.25 5.58579 18.9142 5.25 18.5 5.25H16H8ZM19.25 9.75H4.75V19C4.75 19.4142 5.08579 19.75 5.5 19.75H18.5C18.9142 19.75 19.25 19.4142 19.25 19V9.75Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Calendar
                                </span>
                            </a>
                        </li>

                        <!-- Role & Permissions (submenu) -->
                        <li>
                            <button @click="toggleSubmenu('access-control')" class="menu-item group w-full"
                                :class="[
                                    isSubmenuOpen('access-control') ? 'menu-item-active' : 'menu-item-inactive',
                                    !$store.sidebar.isExpanded && !$store.sidebar.isHovered ? 'xl:justify-center' :
                                    'xl:justify-start'
                                ]">
                                <span
                                    :class="isSubmenuOpen('access-control') ? 'menu-item-icon-active' :
                                        'menu-item-icon-inactive'">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.8" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 3l7 4v5c0 5-3.5 7.5-7 9-3.5-1.5-7-4-7-9V7l7-4z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9.75 11.25V9.75a2.25 2.25 0 114.5 0v1.5" />
                                        <rect x="8.25" y="11.25" width="7.5" height="6" rx="1.5" />
                                    </svg>

                                </span>

                                <span
                                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text flex items-center gap-2 text-start">
                                    Access Control
                                </span>

                                <svg x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="ms-auto w-5 h-5 transition-transform duration-200"
                                    :class="{ 'rotate-180 text-brand-500': isSubmenuOpen('access-control') }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div data-submenu-key="access-control"
                                x-show="isSubmenuOpen('access-control') && ($store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen)">
                                <ul class="mt-3 space-y-1 {{ $submenuIndentClass }}">
                                    <li>
                                        <a href="{{ route('dashboard.roles.index') }}" class="menu-dropdown-item"
                                            :class="isActive('/dashboard/roles') ?
                                                'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                                            Roles
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('dashboard.permissions.index') }}"
                                            class="menu-dropdown-item"
                                            :class="isActive('/dashboard/permissions') ?
                                                'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                                            Permissions
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('dashboard.users.index') }}" class="menu-dropdown-item"
                                            :class="isActive('/dashboard/users') ?
                                                'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                                            User Management
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>
        </nav>

        <!-- Sidebar Widget -->
        {{-- <div x-data x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
            x-transition class="mt-auto">
            @include('layouts.sidebar-widget')
        </div> --}}

    </div>
</aside>

<!-- Mobile Overlay -->
<div x-show="$store.sidebar.isMobileOpen" @click="$store.sidebar.setMobileOpen(false)"
    class="fixed z-50 h-screen w-full bg-gray-900/50"></div>
