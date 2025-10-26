<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center">
                        <div class="mx-auto h-16 w-16 bg-primary-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Selamat datang di Giscana!</h3>
                        <p class="text-gray-600 mb-6">
                            Halo {{ Auth::user()->name }}, selamat datang di sistem informasi geografis untuk mitigasi bencana alam.
                        </p>
                        <a href="{{ route('map.index') }}" class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition-colors">
                            Akses Peta Interaktif
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
