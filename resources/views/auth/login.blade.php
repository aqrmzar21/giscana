<x-guest-layout>
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                {{-- <div class="mx-auto h-16 w-16 bg-white rounded-full flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                </div> --}}
                <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700">
                <h2 class="text-3xl font-bold text-black mb-2">Giscana</h2>
                </a>
                <p class="text-primary-100">Silahkan masukkan email dan Password</p>
                <!-- <p class="text-primary-100">Masuk ke akun Anda</p> -->
            </div>

            <div class="bg-white rounded-lg shadow-xl p-8">
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full"
                                        type="password"
                                        name="password"
                                        required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>


                    <div class="flex items-center justify-center mt-6">

                        <button class="inline-flex rounded px-4 py-2 text-sm font-medium text-white shadow-sm text-white-600 hover:text-gray-500 bg-gray-600 hover:bg-gray-700 whitespace-nowrap">
                            {{ __('Log in') }}
                        </button>
                    </div>
                </form>


            </div>
        </div>
</x-guest-layout>
