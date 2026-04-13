<div class="w-full">
    @if ($step === 1)
        {{-- Step 1: Role Selection --}}
        <div class="animate-in fade-in slide-in-from-bottom-4 duration-500">
            <div class="mb-8 text-center sm:text-left">
                <h1 class="text-title-sm sm:text-title-md mb-2 font-bold text-gray-900 dark:text-white/90">
                    How would you like to use Danosla?
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Choose the type of account that fits your needs.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 max-w-xl mx-auto md:mx-0">
                {{-- Shipper Card --}}
                <div wire:click="selectRole('shipper')"
                    class="group relative overflow-hidden rounded-2xl border-2 border-transparent bg-white p-6 shadow-sm transition-all hover:border-brand-500 hover:shadow-xl dark:bg-gray-800/50 dark:hover:bg-gray-800 cursor-pointer">
                    <div class="relative z-10 flex items-center gap-6">
                        <div class="shrink-0 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition-colors group-hover:bg-brand-500 group-hover:text-white dark:bg-brand-500/10">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="mb-1 text-xl font-bold text-gray-900 dark:text-white">Shipper</h3>
                            <p class="text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                                I want to ship goods, items or equipment and find professional transporters.
                            </p>
                        </div>
                    </div>
                    <div class="absolute -right-4 -bottom-4 h-20 w-20 rounded-full bg-brand-500/5 transition-transform group-hover:scale-150"></div>
                </div>

                {{-- Carrier Card --}}
                <div wire:click="selectRole('carrier')"
                    class="group relative overflow-hidden rounded-2xl border-2 border-transparent bg-white p-6 shadow-sm transition-all hover:border-brand-500 hover:shadow-xl dark:bg-gray-800/50 dark:hover:bg-gray-800 cursor-pointer">
                    <div class="relative z-10 flex items-center gap-6">
                        <div class="shrink-0 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition-colors group-hover:bg-brand-500 group-hover:text-white dark:bg-brand-500/10">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="mb-1 text-xl font-bold text-gray-900 dark:text-white">Carrier</h3>
                            <p class="text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                                I am a transport professional and I want to offer my services and find loads.
                            </p>
                        </div>
                    </div>
                    <div class="absolute -right-4 -bottom-4 h-20 w-20 rounded-full bg-brand-500/5 transition-transform group-hover:scale-150"></div>
                </div>
            </div>
            
            <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                Already have an account? <a href="{{ route('login') }}" class="font-semibold text-brand-500 hover:text-brand-600 transition-colors">Sign in</a>
            </div>
        </div>
    @else
        {{-- Step 2: Registration Form --}}
        <div class="animate-in fade-in slide-in-from-right-4 duration-500">
            <div class="mb-5 sm:mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-title-sm sm:text-title-md mb-2 font-bold text-gray-900 dark:text-white/90">
                        Create {{ ucfirst($role) }} Account
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Complete your profile to get started.
                    </p>
                </div>
                <button wire:click="back" class="text-sm font-medium text-brand-500 hover:text-brand-600 flex items-center gap-1 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </button>
            </div>

            <form wire:submit.prevent="register" class="space-y-2">
                {{-- 1. Identity Group --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-3">
                    {{-- Full Name --}}
                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="name" placeholder="Enter your full name"
                            class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 transition-all focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500" />
                        @error('name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" wire:model="email" placeholder="you@example.com"
                            class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 transition-all focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500" />
                        @error('email') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- 2. Location & Contact Group --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Region --}}
                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Region <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select wire:model.live="region_id"
                                class="h-12 w-full appearance-none rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="">Select Region</option>
                                @foreach ($this->regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->getTranslation('name', $locale) }}</option>
                                @endforeach
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-gray-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                        @error('region_id') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Country --}}
                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Country <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select wire:model.live="country_id" @disabled(!$region_id)
                                class="h-12 w-full appearance-none rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 disabled:opacity-50 disabled:bg-gray-50 dark:disabled:bg-gray-800">
                                <option value="">Select Country</option>
                                @foreach ($this->countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->getTranslation('name', $locale) }}</option>
                                @endforeach
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-gray-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                        @error('country_id') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- 3. Contact Detail & Address Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Phone --}}
                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" wire:model="phone" placeholder="+1 (555) 000-0000"
                            class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                        @error('phone') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Address --}}
                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="address" placeholder="Street address, city, etc."
                            class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                        @error('address') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- 4. Professional Group --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Sector (Shipper Only) --}}
                    @if($role === 'shipper')
                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Industry Sector <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select wire:model="sector_id"
                                class="h-12 w-full appearance-none rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="">Select Sector</option>
                                @foreach ($this->sectors as $sector)
                                    <option value="{{ $sector->id }}">{{ $sector->getTranslation('name', $locale) }}</option>
                                @endforeach
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-gray-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                        @error('sector_id') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    @endif

                    {{-- Website --}}
                    <div class=" {{ $role !== 'shipper' ? 'md:col-span-2' : '' }}">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Website
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pr-3 text-gray-400 border-r border-gray-200 dark:border-gray-800">
                                https://
                            </span>
                            <input type="url" wire:model="website" placeholder="www.yourcompany.com"
                                class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 pl-[88px] text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                        </div>
                        @error('website') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- 5. Business Details Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Company Name --}}
                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="company_name" placeholder="Your legal business name"
                            class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                        @error('company_name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Company Number --}}
                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Company Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="company_number" placeholder="Registration / VAT number"
                            class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                        @error('company_number') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- 6. Security Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Password --}}
                    <div class="" x-data="{ showPassword: false }">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" wire:model="password" placeholder="Create a strong password"
                                class="h-12 w-full rounded-xl border border-gray-300 bg-white py-3 pr-12 pl-4 text-sm text-gray-800 placeholder:text-gray-400 transition-all focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.046m4.532-3.058A10.05 10.05 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21m-2.122-2.122L3 3" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-[11px] leading-tight text-gray-500 font-medium mt-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Minimum 8 characters with uppercase, numbers and symbols
                        </p>
                        @error('password') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" wire:model="password_confirmation" placeholder="Confirm your password"
                            class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                </div>

                {{-- 7. Final Step --}}
                <div class="pt-2 space-y-4">
                    <label class="flex cursor-pointer items-start text-sm select-none group">
                        <input type="checkbox" wire:model="acceptedTerms"
                            class="mr-3 mt-1 h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/20 focus:ring-offset-0 transition-all dark:border-gray-600 dark:bg-gray-800">
                        <span class="text-gray-600 dark:text-gray-400 leading-normal">
                            I agree to the <span class="font-medium text-gray-900 dark:text-white hover:underline">Terms & Conditions</span> and <span class="font-medium text-gray-900 dark:text-white hover:underline">Privacy Policy</span>
                        </span>
                    </label>
                    @error('acceptedTerms') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

                    <button type="submit" wire:loading.attr="disabled"
                        class="flex w-full items-center justify-center rounded-xl bg-brand-500 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-brand-500/30 transition-all duration-200 hover:bg-brand-600 hover:shadow-xl active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-brand-500/20 disabled:opacity-50">
                        <span wire:loading.remove>Create {{ ucfirst($role) }} Account</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating Account...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
