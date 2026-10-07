<x-guest-layout>
    <div class="w-full max-w-md relative">
        <!-- Ambient Glowing Backdrop -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-30 h-30 bg-blue-500/10 dark:bg-blue-600/15 blur-2xl rounded-full pointer-events-none"></div>

        <div class="relative bg-white/90 dark:bg-slate-800/80 backdrop-blur-xl p-8 sm:p-10 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-lg transition-colors duration-200">
            <!-- Header Text -->
            <div class="text-center mb-4">
                <!-- <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg> -->
                <a href="{{ route('home') }}" class="flex justify-center items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-violet-600 flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        Giscana
                    </span>
                </a>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Silahkan masukkan email dan password akun petugas BPBD
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-3">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username" 
                           placeholder="uuername@gmail.com"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-sm font-medium">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-rose-500" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Password</label>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="current-password" 
                           placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-sm font-medium">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-rose-500" />
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-500 hover:via-indigo-500 hover:to-purple-500 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-blue-500/25 transition-all duration-200 hover:scale-[1.01] active:scale-[0.99] text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>Masuk</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-guest-layout>
