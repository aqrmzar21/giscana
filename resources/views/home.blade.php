@extends('layouts.landing')

@section('title', 'Beranda | Giscana')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-gradient-to-b from-blue-50/80 via-slate-50 to-white dark:from-slate-900 dark:via-slate-900 dark:to-slate-950 pt-16 pb-24 lg:pt-24 lg:pb-32 border-b border-slate-200 dark:border-slate-800/80 transition-colors duration-200">
    <!-- Ambient Lighting & Glowing Orbs -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-500/10 dark:bg-blue-600/15 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-10 right-10 w-[400px] h-[400px] bg-indigo-500/10 dark:bg-indigo-600/15 blur-[100px] rounded-full pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-4xl mx-auto">
            <!-- Badge -->
            <div class="scroll-reveal delay-75 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-100/80 dark:bg-slate-800/80 border border-blue-200/80 dark:border-slate-700/80 backdrop-blur-md mb-8 shadow-md">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">WebGIS & SIMBA Bencana Bone Bolango</span>
                <span class="text-slate-400 dark:text-slate-500">•</span>
                <span class="text-xs sm:text-sm text-blue-600 dark:text-blue-400 font-bold">BPBD Gorontalo</span>
            </div>

            <!-- Title -->
            <h1 class="scroll-reveal delay-100 text-4xl sm:text-6xl lg:text-7xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-[1.15] mb-6">
                Kesiapsiagaan & Mitigasi Bencana
                <span class="block mt-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 dark:from-blue-400 dark:via-indigo-300 dark:to-purple-400 bg-clip-text text-transparent">
                    Kawasan Pesisir Bone
                </span>
            </h1>

            <!-- Subtitle -->
            <p class="scroll-reveal delay-150 text-lg sm:text-xl text-slate-600 dark:text-slate-300 font-normal leading-relaxed mb-10 max-w-3xl mx-auto">
                Sistem Informasi Geografis cerdas dan Manajemen Bantuan terpadu untuk percepatan evakuasi, 
                pemetaan zona rawan banjir & longsor, serta pencegahan <span class="text-slate-900 dark:text-white font-semibold">double claim</span> logistik di 5 Kecamatan Fokus Kabupaten Bone Bolango.
            </p>

            <!-- Action Buttons -->
            <div class="scroll-reveal delay-200 flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('map.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-500 hover:via-indigo-500 hover:to-purple-500 text-white font-semibold px-8 py-4 rounded-xl shadow-xl shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-300 hover:scale-[1.02]">
                    <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <span>Jelajahi Peta Interaktif</span>
                </a>

                @auth
                    <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-slate-800/90 hover:bg-slate-700/90 text-slate-200 hover:text-white font-semibold px-8 py-4 rounded-xl border border-slate-700 transition-all duration-200">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Akses Dashboard Admin</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white dark:bg-slate-800/90 hover:bg-slate-100 dark:hover:bg-slate-700/90 text-slate-800 dark:text-slate-200 font-semibold px-8 py-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-md transition-all duration-200">
                        <svg class="w-5 h-5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>Login Petugas / Admin</span>
                    </a>
                @endauth
            </div>
        </div>

        <!-- Quick Stats Floating Card Bar -->
        <div class="scroll-reveal delay-300 mt-16 grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white/90 dark:bg-slate-800/60 backdrop-blur-md p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 hover:border-blue-500/50 transition-all shadow-md dark:shadow-none group">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Titik Rawan</span>
                    <div class="p-2 rounded-lg bg-red-500/10 text-red-500 dark:text-red-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>
                <div class="text-3xl font-extrabold text-slate-900 dark:text-white mb-1">{{ $stats['disaster_zones'] }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">Zona Risiko Terpetakan</div>
            </div>

            <div class="bg-white/90 dark:bg-slate-800/60 backdrop-blur-md p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 hover:border-emerald-500/50 transition-all shadow-md dark:shadow-none group">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Titik Kumpul</span>
                    <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
                <div class="text-3xl font-extrabold text-slate-900 dark:text-white mb-1">{{ $stats['evacuation_facilities'] }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">Fasilitas Evakuasi Posko</div>
            </div>

            <div class="bg-white/90 dark:bg-slate-800/60 backdrop-blur-md p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 hover:border-indigo-500/50 transition-all shadow-md dark:shadow-none group">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Rute Evakuasi</span>
                    <div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
                <div class="text-3xl font-extrabold text-slate-900 dark:text-white mb-1">{{ $stats['evacuation_routes'] }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">Rute Evakuasi Aktif</div>
            </div>

            <div class="bg-white/90 dark:bg-slate-800/60 backdrop-blur-md p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 hover:border-purple-500/50 transition-all shadow-md dark:shadow-none group">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Target Penerima</span>
                    <div class="p-2 rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
                <div class="text-3xl font-extrabold text-slate-900 dark:text-white mb-1">{{ number_format($stats['total_beneficiaries'] ?? 0) }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">KK Terdata di SIMBA</div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-24 bg-slate-100/70 dark:bg-slate-900 relative transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="scroll-reveal text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white mb-4 tracking-tight">
                Fitur Unggulan GIScana
            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-lg">
                Teknologi spasial terkini dan arsitektur SIMBA terintegrasi untuk keakuratan data dan penanganan bencana yang efisien.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature Card 1 -->
            <div class="scroll-reveal delay-100 reveal-scale bg-white dark:bg-slate-800/40 p-8 rounded-2xl border border-slate-200/90 dark:border-slate-700/60 hover:border-blue-500/60 dark:hover:bg-slate-800/70 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-500/10 shadow-md group">
                <div class="w-14 h-14 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Peta Interaktif Spasial</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm">
                    Visualisasi real-time zona rawan banjir & longsor dengan pewarnaan warm colors per kecamatan serta filter tingkat risiko.
                </p>
            </div>

            <!-- Feature Card 2 -->
            <div class="scroll-reveal delay-150 reveal-scale bg-white dark:bg-slate-800/40 p-8 rounded-2xl border border-slate-200/90 dark:border-slate-700/60 hover:border-emerald-500/60 dark:hover:bg-slate-800/70 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-emerald-500/10 shadow-md group">
                <div class="w-14 h-14 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Rute Evakuasi Haversine</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm">
                    Perhitungan jarak presisi ke titik kumpul terdekat menggunakan lokasi GPS pengguna dengan animasi marker berkedip.
                </p>
            </div>

            <!-- Feature Card 3 -->
            <div class="scroll-reveal delay-200 reveal-scale bg-white dark:bg-slate-800/40 p-8 rounded-2xl border border-slate-200/90 dark:border-slate-700/60 hover:border-purple-500/60 dark:hover:bg-slate-800/70 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-purple-500/10 shadow-md group">
                <div class="w-14 h-14 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Anti Double-Claim Logistik</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm">
                    Pencegahan penerimaan bantuan ganda berbasis NIK KK dan kategori barang di tabel <code class="text-purple-600 dark:text-purple-300 font-mono text-xs">aid_distributions</code>.
                </p>
            </div>

            <!-- Feature Card 4 -->
            <div class="scroll-reveal delay-250 reveal-scale bg-white dark:bg-slate-800/40 p-8 rounded-2xl border border-slate-200/90 dark:border-slate-700/60 hover:border-indigo-500/60 dark:hover:bg-slate-800/70 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-indigo-500/10 shadow-md group">
                <div class="w-14 h-14 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Master Stok & Fasilitas</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm">
                    Manajemen stok masuk & keluar barang logistik serta informasi penanggung jawab posko & daya tampung fasilitas evakuasi.
                </p>
            </div>

            <!-- Feature Card 5 -->
            <div class="scroll-reveal delay-300 reveal-scale bg-white dark:bg-slate-800/40 p-8 rounded-2xl border border-slate-200/90 dark:border-slate-700/60 hover:border-amber-500/60 dark:hover:bg-slate-800/70 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-amber-500/10 shadow-md group">
                <div class="w-14 h-14 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Agregasi Real-Time Desa</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm">
                    Kalkulasi otomatis persentase penyaluran bantuan per wilayah desa & kecamatan tanpa pengisian manual statis.
                </p>
            </div>

            <!-- Feature Card 6 -->
            <div class="scroll-reveal delay-350 reveal-scale bg-white dark:bg-slate-800/40 p-8 rounded-2xl border border-slate-200/90 dark:border-slate-700/60 hover:border-rose-500/60 dark:hover:bg-slate-800/70 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-rose-500/10 shadow-md group">
                <div class="w-14 h-14 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-rose-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Multi-Role Access Control</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm">
                    Hak akses terpisah untuk Admin BPBD, Staf Pencatat Lapangan, Pimpinan Monitoring, serta Publik Peta.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Recent Disaster Zones Showcase -->
@if(isset($recent_zones) && $recent_zones->count() > 0)
<section class="py-24 bg-white dark:bg-slate-950 border-t border-b border-slate-200 dark:border-slate-800/80 relative transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="scroll-reveal text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white mb-4 tracking-tight">
                Zona Risiko Bencana Terbaru
            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-lg">
                Titik lokasi rawan bencana banjir & tanah longsor di Kabupaten Bone Bolango
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($recent_zones as $index => $zone)
            <div class="scroll-reveal delay-{{ ($index + 1) * 100 }} reveal-scale bg-slate-50 dark:bg-slate-900 p-6 rounded-2xl border-l-4 {{ $zone->risk_level === 'critical' ? 'border-red-500 bg-red-500/5 dark:bg-red-950/10' : ($zone->risk_level === 'high' ? 'border-orange-500 bg-orange-500/5 dark:bg-orange-950/10' : ($zone->risk_level === 'medium' ? 'border-yellow-500 bg-yellow-500/5 dark:bg-yellow-950/10' : 'border-emerald-500 bg-emerald-500/5 dark:bg-emerald-950/10')) }} border-y border-r border-slate-200 dark:border-slate-800 shadow-lg hover:scale-[1.02] transition-transform duration-300">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white truncate max-w-[200px]">{{ $zone->name }}</h3>
                    <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full {{ $zone->risk_level === 'critical' ? 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-500/30' : ($zone->risk_level === 'high' ? 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-500/30' : ($zone->risk_level === 'medium' ? 'bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-500/30' : 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30')) }}">
                        {{ ucfirst($zone->risk_level) }}
                    </span>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 line-clamp-2 leading-relaxed">
                    {{ $zone->description ?? 'Lokasi pemantauan zona rawan bencana di kawasan pesisir Bone Bolango.' }}
                </p>
                <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-500 border-t border-slate-200 dark:border-slate-800 pt-4">
                    <span class="font-medium text-slate-700 dark:text-slate-300 flex items-center gap-1">
                        <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M13 7h.01M13 11h.01M13 15h.01M19 7h.01M19 11h.01M19 15h.01"/></svg>
                        {{ ucfirst($zone->disaster_type ?? 'Bencana') }}
                    </span>
                    @if($zone->area_hectares)
                        <span class="bg-slate-200 dark:bg-slate-800 px-2.5 py-1 rounded-md text-slate-700 dark:text-slate-300 font-mono">{{ $zone->area_hectares }} Ha</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="scroll-reveal delay-300 text-center mt-12">
            <a href="{{ route('map.index') }}" class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold text-sm group">
                <span>Lihat Seluruh Titik Rawan di Peta Interaktif</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>
</section>
@endif

<!-- About System & Focus Region Section -->
<section id="about" class="py-24 bg-slate-100/70 dark:bg-slate-900 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left Text Content -->
            <div class="scroll-reveal reveal-left lg:col-span-7">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400 text-xs font-semibold mb-6 border border-blue-500/20">
                    <span>Wilayah Studi & Kerangka Sistem</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white mb-6 tracking-tight">
                    Fokus 5 Kecamatan Pesisir Bone, Kabupaten Bone Bolango
                </h2>
                <p class="text-slate-700 dark:text-slate-300 text-lg leading-relaxed mb-6">
                    Sistem GIScana berfokus pada mitigasi bencana banjir dan tanah longsor di kawasan pesisir Kabupaten Bone Bolango, meliputi kecamatan:
                </p>

                <!-- 5 Districts Tags -->
                <div class="flex flex-wrap gap-2.5 mb-8">
                    @foreach(['Bone Raya', 'Bulawa', 'Bone', 'Bonepantai', 'Kabila Bone'] as $kec)
                    <span class="px-4 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        {{ $kec }}
                    </span>
                    @endforeach
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-slate-700 dark:text-slate-300">Peta GIS spasial modern berbasis Leaflet.js & HTML5 Geolocation API</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-slate-700 dark:text-slate-300">Integrasi database transaksi penyaluran bantuan dengan kunci anti-double claim</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-slate-700 dark:text-slate-300">Pembaruan data real-time untuk pengambilan keputusan tanggap darurat BPBD</span>
                    </div>
                </div>
            </div>

            <!-- Right Visual Card -->
            <div class="scroll-reveal reveal-right lg:col-span-5">
                <div class="bg-gradient-to-tr from-white via-slate-50 to-blue-50 dark:from-slate-800 dark:via-slate-800/80 dark:to-blue-950/80 p-8 rounded-3xl border border-slate-200 dark:border-slate-700/80 shadow-xl relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500/10 dark:bg-blue-500/20 blur-3xl rounded-full pointer-events-none"></div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Arsitektur Sistem Terintegrasi
                    </h3>
                    <div class="space-y-4 text-sm">
                        <div class="p-4 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/50 flex justify-between items-center shadow-sm dark:shadow-none">
                            <span class="text-slate-700 dark:text-slate-300">Framework Backend</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400 font-mono">Laravel PHP</span>
                        </div>
                        <div class="p-4 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/50 flex justify-between items-center shadow-sm dark:shadow-none">
                            <span class="text-slate-700 dark:text-slate-300">Engine Peta Spasial</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 font-mono">Leaflet GIS Engine</span>
                        </div>
                        <div class="p-4 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/50 flex justify-between items-center shadow-sm dark:shadow-none">
                            <span class="text-slate-700 dark:text-slate-300">Frontend Router</span>
                            <span class="font-bold text-purple-600 dark:text-purple-400 font-mono">Instant PJAX & Alpine</span>
                        </div>
                        <div class="p-4 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/50 flex justify-between items-center shadow-sm dark:shadow-none">
                            <span class="text-slate-700 dark:text-slate-300">Pencegah Double Claim</span>
                            <span class="font-bold text-amber-600 dark:text-amber-400 font-mono">NIK Unique Hash</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-20 bg-gradient-to-r from-blue-600 via-indigo-700 to-slate-900 dark:from-blue-950 dark:via-indigo-950 dark:to-slate-950 text-white relative overflow-hidden border-t border-slate-200 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <div class="scroll-reveal reveal-scale max-w-3xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-6 tracking-tight">
                Akses Peta Bencana & Sistem Penyaluran Bantuan Sekarang
            </h2>
            <p class="text-blue-100 dark:text-slate-300 text-lg mb-10 leading-relaxed">
                Temukan lokasi titik kumpul terdekat, rute evakuasi tercepat, dan status logistik bantuan di Kabupaten Bone Bolango.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('map.index') }}" class="bg-white text-blue-700 hover:bg-slate-100 font-bold px-8 py-4 rounded-xl text-base shadow-xl transition-all duration-200 hover:scale-105">
                    Buka Peta Interaktif Spasial
                </a>
                @guest
                    <a href="{{ route('login') }}" class="border border-white/40 bg-white/10 hover:bg-white/20 text-white px-8 py-4 rounded-xl font-bold text-base transition-colors backdrop-blur-sm">
                        Login Petugas SIMBA
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="border border-white/40 bg-white/10 hover:bg-white/20 text-white px-8 py-4 rounded-xl font-bold text-base transition-colors backdrop-blur-sm">
                        Ke Dashboard Management
                    </a>
                @endguest
            </div>
        </div>
    </div>
</section>
@endsection
