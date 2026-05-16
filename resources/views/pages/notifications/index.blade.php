@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white">Notifications</h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Gérez vos alertes et mises à jour en temps réel.</p>
                </div>
                
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-brand-50 text-brand-600 rounded-xl font-bold text-sm hover:bg-brand-100 transition-all border border-brand-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Tout marquer comme lu
                        </button>
                    </form>
                @endif
            </div>

            <!-- Notifications List -->
            <div class="space-y-4">
                @forelse($notifications as $notification)
                    <div class="relative group">
                        <a href="{{ route('notifications.read', $notification->id) }}" 
                           class="block p-5 rounded-3xl border transition-all duration-300 {{ $notification->read_at ? 'bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-800' : 'bg-brand-50/30 dark:bg-brand-900/10 border-brand-100/50 dark:border-brand-900/30 shadow-sm' }} hover:shadow-md hover:translate-x-1">
                            
                            <div class="flex gap-5">
                                <!-- Icon/Avatar -->
                                <div class="flex-shrink-0">
                                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center {{ $notification->read_at ? 'bg-gray-100 dark:bg-gray-800 text-gray-500' : 'bg-brand-500 text-white shadow-lg shadow-brand-500/20' }}">
                                        @if(str_contains($notification->type, 'NewShipment'))
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @else
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                        @endif
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <h3 class="font-bold text-lg text-gray-900 dark:text-white truncate">
                                            {{ $notification->data['title'] ?? 'Notification' }}
                                        </h3>
                                        <span class="text-xs font-medium text-gray-400 whitespace-nowrap ml-4">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-3">
                                        {{ $notification->data['message'] ?? '' }}
                                    </p>
                                    
                                    <!-- Detailed Badges -->
                                    @if(isset($notification->data['pickup_at']))
                                        <div class="flex flex-wrap items-center gap-3 mb-3">
                                            <span class="flex items-center gap-1.5 px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-bold border border-gray-200 dark:border-gray-700" title="Enlèvement">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002-2z" />
                                                </svg>
                                                {{ $notification->data['pickup_at'] }}
                                            </span>

                                            <span class="flex items-center gap-1.5 px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-bold border border-gray-200 dark:border-gray-700" title="Livraison">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $notification->data['delivery_at'] }}
                                            </span>
                                            
                                            <span class="flex items-center gap-1.5 px-3 py-1 bg-brand-50 dark:bg-brand-900/20 text-brand-600 dark:text-brand-400 rounded-lg text-xs font-bold border border-brand-100 dark:border-brand-900/30" title="Budget">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $notification->data['price'] }}
                                            </span>

                                            <span class="flex items-center gap-1.5 px-3 py-1 bg-amber-50 dark:bg-amber-900/10 text-amber-600 dark:text-amber-400 rounded-lg text-xs font-bold border border-amber-100 dark:border-amber-900/30" title="Validité">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Jusqu'au {{ $notification->data['validity_at'] }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-3">
                                        <span class="px-2.5 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 text-[10px] font-bold uppercase tracking-wider">
                                            {{ $notification->data['shipper_name'] ?? 'Système' }}
                                        </span>
                                        @if(!$notification->read_at)
                                            <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                                            <span class="text-[10px] font-bold text-brand-500 uppercase tracking-wider">Nouveau</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Arrow -->
                                <div class="flex-shrink-0 self-center text-gray-300 group-hover:text-brand-500 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-20 bg-white dark:bg-gray-900 rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-sm">
                        <div class="w-24 h-24 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Aucune notification</h2>
                        <p class="text-gray-500 dark:text-gray-400">Vous êtes à jour ! Vos futures alertes apparaîtront ici.</p>
                    </div>
                @endforelse

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
