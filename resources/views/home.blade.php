@extends('layouts.landing')

@section('title', 'Beranda - Giscana')

@section('content')
<!-- Hero Section -->
<section class="bg-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Giscana
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-primary-100">
                Geographic Information System for Natural Disaster
            </p>
            <p class="text-lg mb-10 text-primary-200 max-w-3xl mx-auto">
                Sistem informasi geografis berbasis web untuk mendukung kesiapsiagaan dan penanggulangan bencana alam 
                di Kawasan Pesisir Bone, Kabupaten Bone Bolango, Provinsi Gorontalo.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
              @guest
                <!-- <a href="{{ route('register') }}" class="bg-white text-primary-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                  Daftar Sekarang
                </a> -->
                @endguest
                <a href="{{ route('map.index') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                      Lihat Peta Interaktif
                    </a>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
{{-- <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Data Sistem</h2>
            <p class="text-lg text-gray-600">Statistik terkini dari sistem Giscana</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-6 rounded-lg text-center">
                <div class="text-3xl font-bold mb-2">{{ $stats['disaster_zones'] }}</div>
                <div class="text-red-100">Zona Risiko Bencana</div>
            </div>
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-lg text-center">
                <div class="text-3xl font-bold mb-2">{{ $stats['evacuation_routes'] }}</div>
                <div class="text-orange-100">Rute Evakuasi</div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-lg text-center">
                <div class="text-3xl font-bold mb-2">{{ $stats['evacuation_facilities'] }}</div>
                <div class="text-green-100">Fasilitas Evakuasi</div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-lg text-center">
                <div class="text-3xl font-bold mb-2">{{ $stats['aid_disasters'] }}</div>
                <div class="text-purple-100">Pusat Bantuan</div>
            </div>
        </div>
    </div>
</section> --}}

<!-- Features Section -->
<section id="features" class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Fitur Utama</h2>
            <p class="text-lg text-gray-600">Kemampuan sistem Giscana dalam penanggulangan bencana</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-primary-600 mb-4">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Peta Interaktif</h3>
                <p class="text-gray-600">
                    Visualisasi real-time zona risiko bencana dengan peta interaktif yang dapat di-zoom dan difilter.
                </p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-primary-600 mb-4">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Rute Evakuasi</h3>
                <p class="text-gray-600">
                    Perencanaan rute evakuasi yang optimal dengan informasi kapasitas dan kondisi aksesibilitas.
                </p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-primary-600 mb-4">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Fasilitas Evakuasi</h3>
                <p class="text-gray-600">
                    Database lengkap fasilitas evakuasi dengan informasi kapasitas dan kontak person.
                </p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-primary-600 mb-4">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Distribusi Bantuan</h3>
                <p class="text-gray-600">
                    Manajemen pusat distribusi bantuan dengan informasi jenis bantuan dan kapasitas harian.
                </p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-primary-600 mb-4">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Multi-Role Access</h3>
                <p class="text-gray-600">
                    Sistem akses bertingkat untuk admin BPBD, staf, dan masyarakat umum dengan kontrol yang sesuai.
                </p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-primary-600 mb-4">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Pencarian Lokasi</h3>
                <p class="text-gray-600">
                    Fitur pencarian cerdas untuk menemukan lokasi, fasilitas, dan zona risiko dengan mudah.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Recent Disaster Zones -->
@if($recent_zones->count() > 0)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Zona Risiko Terbaru</h2>
            <p class="text-lg text-gray-600">Data zona risiko bencana yang baru ditambahkan</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($recent_zones as $zone)
            <div class="bg-gray-50 p-6 rounded-lg border-l-4 {{ $zone->risk_level === 'critical' ? 'border-red-500' : ($zone->risk_level === 'high' ? 'border-orange-500' : ($zone->risk_level === 'medium' ? 'border-yellow-500' : 'border-green-500')) }}">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $zone->name }}</h3>
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $zone->risk_level === 'critical' ? 'bg-red-100 text-red-800' : ($zone->risk_level === 'high' ? 'bg-orange-100 text-orange-800' : ($zone->risk_level === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                        {{ ucfirst($zone->risk_level) }}
                    </span>
                </div>
                <p class="text-sm text-gray-600 mb-3">{{ Str::limit($zone->description, 100) }}</p>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>{{ ucfirst($zone->disaster_type) }}</span>
                    @if($zone->area_hectares)
                        <span>{{ $zone->area_hectares }} ha</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- About Section -->
<section id="about" class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center"> --}}
        <div class="grid grid-cols-1 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Tentang Giscana</h2>
                <p class="text-lg text-gray-600 mb-6">
                    Giscana adalah sistem informasi geografis berbasis web yang dirancang khusus untuk mendukung 
                    kesiapsiagaan dan penanggulangan bencana alam di Kawasan Pesisir Bone, Kabupaten Bone Bolango, 
                    Provinsi Gorontalo.
                </p>
                <p class="text-lg text-gray-600 mb-6">
                    Sistem ini fokus pada bencana alam seperti banjir dan tanah longsor yang merupakan bahaya 
                    paling sering terjadi di Kabupaten Bone Bolango.
                </p>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Teknologi GIS modern dengan Leaflet.js</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Data real-time dan akurat</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Antarmuka yang mudah digunakan</span>
                    </div>
                </div>
            </div>
            {{-- <div class="bg-white p-8 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold mb-4">Teknologi yang Digunakan</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-primary-600 mb-2">Laravel</div>
                        <div class="text-sm text-gray-600">Backend Framework</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-primary-600 mb-2">Leaflet.js</div>
                        <div class="text-sm text-gray-600">Mapping Library</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-primary-600 mb-2">TailwindCSS</div>
                        <div class="text-sm text-gray-600">CSS Framework</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-primary-600 mb-2">MySQL</div>
                        <div class="text-sm text-gray-600">Database</div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Siap Meningkatkan Kesiapsiagaan Bencana?</h2>
        <p class="text-xl mb-8 text-primary-100">
            Bergabunglah dengan sistem Giscana untuk mendapatkan akses penuh ke informasi bencana terkini.
        </p>
        @guest
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <!-- <a href="{{ route('register') }}" class="bg-white text-primary-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Daftar Sekarang
                </a> -->
                <a href="{{ route('login') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                    Login
                </a>
            </div>
        @else
            <a href="{{ route('dashboard') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                Akses Dashboard
            </a>
        @endguest
    </div>
</section>
@endsection
