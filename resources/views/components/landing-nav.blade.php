<nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50 shrink-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <h1 class="text-xl font-bold text-primary-600">
                        <a href="{{ route('home') }}" class="flex items-center">
                            <svg class="w-8 h-8 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                            Giscana
                        </a>
                    </h1>
                </div>
                <div class="hidden md:ml-6 md:flex md:space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        Beranda
                    </a>
                    <a href="{{ route('map.index') }}" class="text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        Peta Interaktif
                    </a>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="border-2 border-blue text-blue hover:text-white px-8 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
