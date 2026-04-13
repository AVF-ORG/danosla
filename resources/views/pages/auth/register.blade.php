@extends('layouts.fullscreen-layout')

@php
    $locale = LaravelLocalization::getCurrentLocale();
@endphp


@section('content')
    <div class="relative z-1 bg-white p-6 sm:p-0 dark:bg-gray-900">
        <div class="flex h-screen w-full flex-col justify-center sm:p-0 lg:flex-row dark:bg-gray-900">

            {{-- Language Switcher - Top Right --}}
            <div class="fixed top-6 right-6 z-50">
                @include('layouts.header.language-dropdown')
            </div>

            {{-- Form Section --}}
            <div class="flex w-full flex-1 flex-col lg:w-2/3">
                <div class="mx-auto flex w-full flex-1 flex-col justify-center px-16">
                    <div class="mb-5 sm:mb-8">
                        <h1 class="text-title-sm sm:text-title-md mb-2 font-bold text-gray-900 dark:text-white/90">
                            Create Account
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Join us today and start your journey
                        </p>
                    </div>

                    <div>
                        <div x-data="{
                            {{-- Data --}}
                            regions: {{ $regions->toJson() }},
                                selectedRegion: '{{ old('region_id') }}',
                                selectedCountry: '{{ old('country_id') }}',
                                countries: [],
                                phone: '{{ old('phone') }}',
                                password: '',
                                showPassword: false,
                                acceptedTerms: false,
                                role: '{{ old('role', 'carrier') }}',
                        
                                {{-- Country Codes for Phone --}}
                            countryCodes: {
                                    @foreach ($regions as $region)
                                @foreach ($region->countries as $c)
                                    '{{ $c->id }}': '{{ $c->international_code }}', @endforeach
                                    @endforeach
                                },
                        
                                {{-- Methods --}}
                            init() {
                                    if (this.selectedRegion) {
                                        this.updateCountries(true);
                                    }
                                },
                        
                                updateCountries(isInit = false) {
                                    const region = this.regions.find(r => r.id == this.selectedRegion);
                                    this.countries = region ? region.countries : [];
                                    if (!isInit) this.selectedCountry = '';
                                },
                        
                                updatePhonePrefix() {
                                    const prefix = this.countryCodes[this.selectedCountry] || '';
                                    if (prefix && !this.phone.startsWith(prefix)) {
                                        this.phone = prefix + ' ' + this.phone.replace(/^\+\d+\s*/, '');
                                    }
                                },
                        
                                get passwordStrength() {
                                    let score = 0;
                                    if (this.password.length >= 8 && this.password.length <= 20) score++;
                                    if (/[a-z]/.test(this.password)) score++;
                                    if (/[A-Z]/.test(this.password)) score++;
                                    if (/[0-9]/.test(this.password)) score++;
                                    if (/[@#\$%\^&\*\+]/.test(this.password)) score++;
                                    return score;
                                },
                        
                                get strengthColor() {
                                    const colors = ['bg-gray-200 dark:bg-gray-700', 'bg-red-500', 'bg-red-500', 'bg-orange-500', 'bg-blue-500', 'bg-green-500'];
                                    return colors[this.passwordStrength] || colors[0];
                                }
                        }" x-init="$watch('selectedCountry', value => updatePhonePrefix())">
                            <form method="POST" action="{{ route('register.store') }}">
                                @csrf

                                <div class="space-y-6">
                                    {{-- Role Selection --}}
                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Register as <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <select name="role" x-model="role"
                                                class="h-12 w-full appearance-none rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                                <option value="carrier">Carrier</option>
                                                <option value="shipper">Shipper</option>
                                            </select>
                                            <span
                                                class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-gray-400">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </span>
                                        </div>
                                        @error('role')
                                            <p class="text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- 1. Identity Group --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        {{-- Full Name --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Full Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="name" value="{{ old('name') }}"
                                                placeholder="Enter your full name"
                                                class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 transition-all focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500" />
                                            @error('name')
                                                <p class="text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Email --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Email Address <span class="text-red-500">*</span>
                                            </label>
                                            <input type="email" name="email" value="{{ old('email') }}"
                                                placeholder="you@example.com"
                                                class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 transition-all focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500" />
                                            @error('email')
                                                <p class="text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- 2. Location & Contact Group --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        {{-- Region --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Region <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <select name="region_id" x-model="selectedRegion"
                                                    @change="updateCountries()"
                                                    class="h-12 w-full appearance-none rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                                    <option value="">Select Region</option>
                                                    <template x-for="region in regions" :key="region.id">
                                                        <option :value="region.id"
                                                            x-text="region.name['{{ $locale }}']"></option>
                                                    </template>
                                                </select>
                                                <span
                                                    class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-gray-400">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </span>
                                            </div>
                                            @error('region_id')
                                                <p class="text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Country --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Country <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <select name="country_id" x-model="selectedCountry"
                                                    :disabled="!selectedRegion"
                                                    class="h-12 w-full appearance-none rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 disabled:opacity-50 disabled:bg-gray-50 dark:disabled:bg-gray-800 flex items-center">
                                                    <option value="">Select Country</option>
                                                    <template x-for="country in countries" :key="country.id">
                                                        <option :value="country.id"
                                                            x-text="country.name['{{ $locale }}']"></option>
                                                    </template>
                                                </select>
                                                <span
                                                    class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-gray-400">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </span>
                                            </div>
                                            @error('country_id')
                                                <p class="text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- 3. Contact Detail & Address Row --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        {{-- Phone --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Phone Number <span class="text-red-500">*</span>
                                            </label>
                                            <input type="tel" name="phone" x-model="phone"
                                                placeholder="+1 (555) 000-0000"
                                                class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                            @error('phone')
                                                <p class="text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Address --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Address <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="address" value="{{ old('address') }}"
                                                placeholder="Street address, city, etc."
                                                class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                            @error('address')
                                                <p class="text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- 4. Professional Group --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        {{-- Sector --}}
                                        <div class="space-y-1.5" x-show="role === 'shipper'" x-cloak>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Industry Sector <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <select name="sector_id"
                                                    class="h-12 w-full appearance-none rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                                    <option value="">Select Sector</option>
                                                    @foreach ($sectors as $sector)
                                                        <option value="{{ $sector->id }}"
                                                            {{ old('sector_id') == $sector->id ? 'selected' : '' }}>
                                                            {{ $sector->getTranslation('name', $locale) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span
                                                    class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-gray-400">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </span>
                                            </div>
                                            @error('sector_id')
                                                <p class="text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Website --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Website
                                            </label>
                                            <div class="relative">
                                                <span
                                                    class="absolute inset-y-0 left-0 flex items-center pl-3.5 pr-3 text-gray-400 border-r border-gray-200 dark:border-gray-800">
                                                    https://
                                                </span>
                                                <input type="url" name="website" value="{{ old('website') }}"
                                                    placeholder="www.yourcompany.com"
                                                    class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 pl-[88px] text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                            </div>
                                            @error('website')
                                                <p class="text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- 5. Business Details Row --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        {{-- Company Name --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Company Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="company_name" value="{{ old('company_name') }}"
                                                placeholder="Your legal business name"
                                                class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                            @error('company_name')
                                                <p class="text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Company Number --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Company Number <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="company_number"
                                                value="{{ old('company_number') }}"
                                                placeholder="Registration / VAT number"
                                                class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                            @error('company_number')
                                                <p class="text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- 6. Security Row --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        {{-- Password --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Password <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input :type="showPassword ? 'text' : 'password'" name="password"
                                                    x-model="password" placeholder="Create a strong password"
                                                    class="h-12 w-full rounded-xl border border-gray-300 bg-white py-3 pr-12 pl-4 text-sm text-gray-800 placeholder:text-gray-400 transition-all focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                                    :class="passwordStrength > 0 && ('border-' + strengthColor.replace('bg-',
                                                        ''))" />
                                                <button type="button" @click="showPassword = !showPassword"
                                                    class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                    <svg x-show="!showPassword" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <svg x-show="showPassword" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.046m4.532-3.058A10.05 10.05 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21m-2.122-2.122L3 3" />
                                                    </svg>
                                                </button>
                                            </div>
                                            {{-- Strength Progress --}}
                                            <div
                                                class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                                <div class="h-full transition-all duration-500 ease-out"
                                                    :class="strengthColor"
                                                    :style="{ width: (passwordStrength / 5 * 100) + '%' }"></div>
                                            </div>
                                            <p class="text-[10px] leading-tight text-gray-500 uppercase tracking-wider">
                                                Security: min 8 chars, 1 uppercase, 1 special, 1 number</p>
                                            @error('password')
                                                <p class="text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Confirm Password --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Confirm Password <span class="text-red-500">*</span>
                                            </label>
                                            <input type="password" name="password_confirmation"
                                                placeholder="Confirm your password"
                                                class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        </div>
                                    </div>

                                    {{-- 7. Final Step --}}
                                    <div class="pt-2 space-y-4">
                                        <label class="flex cursor-pointer items-start text-sm select-none group">
                                            <input type="checkbox" x-model="acceptedTerms"
                                                class="mr-3 mt-1 h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/20 focus:ring-offset-0 transition-all dark:border-gray-600 dark:bg-gray-800">
                                            <span class="text-gray-600 dark:text-gray-400 leading-normal">
                                                I agree to the <span
                                                    class="font-medium text-gray-900 dark:text-white hover:underline">Terms
                                                    & Conditions</span> and <span
                                                    class="font-medium text-gray-900 dark:text-white hover:underline">Privacy
                                                    Policy</span>
                                            </span>
                                        </label>

                                        <button type="submit" :disabled="!acceptedTerms"
                                            class="flex w-full items-center justify-center rounded-xl bg-brand-500 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-brand-500/30 transition-all duration-200 hover:bg-brand-600 hover:shadow-xl active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-brand-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-brand-500 disabled:shadow-none">
                                            Create Account
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>


                        {{-- Login Link --}}
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Already have an account?
                                <a href="{{ route('login') }}"
                                    class="font-semibold text-brand-500 hover:text-brand-600 transition-colors dark:text-brand-400 dark:hover:text-brand-300">
                                    Sign in
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Panel - Branding --}}
            <div
                class="bg-brand-950 relative hidden h-full w-full items-center justify-center overflow-hidden lg:flex lg:w-1/3 dark:bg-white/5">
                {{-- Gradient Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-br from-brand-600/20 via-transparent to-brand-800/20"></div>

                <div class="z-1 flex items-center justify-center">
                    {{-- Grid Shape --}}
                    @include('components.grid-shape')

                    <div class="flex max-w-md flex-col items-center px-8 text-center">
                        <a href="/" class="mb-6 block">
                            <img src="{{ asset('images/logo/auth-logo.svg') }}" alt="Logo" class="h-12" />
                        </a>
                        <h2 class="mb-3 text-2xl font-bold text-white">
                            Welcome to Our Platform
                        </h2>
                        <p class="text-gray-300 dark:text-white/60 leading-relaxed">
                            Join thousands of users who trust us with their business. Start your journey today and
                            experience the difference.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Theme Toggler --}}
            <div class="fixed right-6 bottom-6 z-50">
                <button
                    class="bg-brand-500 hover:bg-brand-600 inline-flex size-14 items-center justify-center rounded-full text-white shadow-lg transition-all hover:shadow-xl active:scale-95"
                    @click.prevent="$store.theme.toggle()">
                    <svg class="hidden fill-current dark:block" width="20" height="20" viewBox="0 0 20 20"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M9.99998 1.5415C10.4142 1.5415 10.75 1.87729 10.75 2.2915V3.5415C10.75 3.95572 10.4142 4.2915 9.99998 4.2915C9.58577 4.2915 9.24998 3.95572 9.24998 3.5415V2.2915C9.24998 1.87729 9.58577 1.5415 9.99998 1.5415ZM10.0009 6.79327C8.22978 6.79327 6.79402 8.22904 6.79402 10.0001C6.79402 11.7712 8.22978 13.207 10.0009 13.207C11.772 13.207 13.2078 11.7712 13.2078 10.0001C13.2078 8.22904 11.772 6.79327 10.0009 6.79327ZM5.29402 10.0001C5.29402 7.40061 7.40135 5.29327 10.0009 5.29327C12.6004 5.29327 14.7078 7.40061 14.7078 10.0001C14.7078 12.5997 12.6004 14.707 10.0009 14.707C7.40135 14.707 5.29402 12.5997 5.29402 10.0001ZM15.9813 5.08035C16.2742 4.78746 16.2742 4.31258 15.9813 4.01969C15.6884 3.7268 15.2135 3.7268 14.9207 4.01969L14.0368 4.90357C13.7439 5.19647 13.7439 5.67134 14.0368 5.96423C14.3297 6.25713 14.8045 6.25713 15.0974 5.96423L15.9813 5.08035ZM18.4577 10.0001C18.4577 10.4143 18.1219 10.7501 17.7077 10.7501H16.4577C16.0435 10.7501 15.7077 10.4143 15.7077 10.0001C15.7077 9.58592 16.0435 9.25013 16.4577 9.25013H17.7077C18.1219 9.25013 18.4577 9.58592 18.4577 10.0001ZM14.9207 15.9806C15.2135 16.2735 15.6884 16.2735 15.9813 15.9806C16.2742 15.6877 16.2742 15.2128 15.9813 14.9199L15.0974 14.036C14.8045 13.7431 14.3297 13.7431 14.0368 14.036C13.7439 14.3289 13.7439 14.8038 14.0368 15.0967L14.9207 15.9806ZM9.99998 15.7088C10.4142 15.7088 10.75 16.0445 10.75 16.4588V17.7088C10.75 18.123 10.4142 18.4588 9.99998 18.4588C9.58577 18.4588 9.24998 18.123 9.24998 17.7088V16.4588C9.24998 16.0445 9.58577 15.7088 9.99998 15.7088ZM5.96356 15.0972C6.25646 14.8043 6.25646 14.3295 5.96356 14.0366C5.67067 13.7437 5.1958 13.7437 4.9029 14.0366L4.01902 14.9204C3.72613 15.2133 3.72613 15.6882 4.01902 15.9811C4.31191 16.274 4.78679 16.274 5.07968 15.9811L5.96356 15.0972ZM4.29224 10.0001C4.29224 10.4143 3.95645 10.7501 3.54224 10.7501H2.29224C1.87802 10.7501 1.54224 10.4143 1.54224 10.0001C1.54224 9.58592 1.87802 9.25013 2.29224 9.25013H3.54224C3.95645 9.25013 4.29224 9.58592 4.29224 10.0001ZM4.9029 5.9637C5.1958 6.25659 5.67067 6.25659 5.96356 5.9637C6.25646 5.6708 6.25646 5.19593 5.96356 4.90303L5.07968 4.01915C4.78679 3.72626 4.31191 3.72626 4.01902 4.01915C3.72613 4.31204 3.72613 4.78692 4.01902 5.07981L4.9029 5.9637Z"
                            fill="" />
                    </svg>
                    <svg class="fill-current dark:hidden" width="20" height="20" viewBox="0 0 20 20"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M17.4547 11.97L18.1799 12.1611C18.265 11.8383 18.1265 11.4982 17.8401 11.3266C17.5538 11.1551 17.1885 11.1934 16.944 11.4207L17.4547 11.97ZM8.0306 2.5459L8.57989 3.05657C8.80718 2.81209 8.84554 2.44682 8.67398 2.16046C8.50243 1.8741 8.16227 1.73559 7.83948 1.82066L8.0306 2.5459ZM12.9154 13.0035C9.64678 13.0035 6.99707 10.3538 6.99707 7.08524H5.49707C5.49707 11.1823 8.81835 14.5035 12.9154 14.5035V13.0035ZM16.944 11.4207C15.8869 12.4035 14.4721 13.0035 12.9154 13.0035V14.5035C14.8657 14.5035 16.6418 13.7499 17.9654 12.5193L16.944 11.4207ZM16.7295 11.7789C15.9437 14.7607 13.2277 16.9586 10.0003 16.9586V18.4586C13.9257 18.4586 17.2249 15.7853 18.1799 12.1611L16.7295 11.7789ZM10.0003 16.9586C6.15734 16.9586 3.04199 13.8433 3.04199 10.0003H1.54199C1.54199 14.6717 5.32892 18.4586 10.0003 18.4586V16.9586ZM3.04199 10.0003C3.04199 6.77289 5.23988 4.05695 8.22173 3.27114L7.83948 1.82066C4.21532 2.77574 1.54199 6.07486 1.54199 10.0003H3.04199ZM6.99707 7.08524C6.99707 5.52854 7.5971 4.11366 8.57989 3.05657L7.48132 2.03522C6.25073 3.35885 5.49707 5.13487 5.49707 7.08524H6.99707Z"
                            fill="" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endsection
