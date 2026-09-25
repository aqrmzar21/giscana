# Giscana - Geographic Information System for Natural Disaster Mitigation

A web-based Geographic Information System (GIS) designed to support disaster preparedness and response efforts in the Bone Coastal Area, Bone Bolango Regency, Gorontalo Province, Indonesia.

## Overview

Giscana is a comprehensive disaster management system that focuses on natural disasters such as banjirs and longsors. The system provides interactive maps, evacuation planning, and aid distribution management to enhance community resilience.

## Features

### 🗺️ Interactive Mapping
- Real-time visualization of disaster-prone zones
- Interactive evacuation routes and facilities
- Aid distribution point mapping
- Risk level visualization with color-coded zones

### 🚨 Disaster Management
- banjir and longsor risk assessment
- Evacuation route planning
- Emergency facility management
- Aid distribution coordination

### 👥 Multi-Role Access
- **BPBD Administrators**: Full system access and data management
- **BPBD Staff**: Data input and management capabilities
- **Public Users**: View-only access to disaster information

### 📊 Data Management
- CRUD operations for spatial data
- Geospatial data storage using MySQL
- RESTful API for data exchange
- Real-time data filtering and search

## Technology Stack

- **Backend**: Laravel 12 (PHP 8.3+)
- **Frontend**: Laravel Blade templates with TailwindCSS
- **Mapping**: Leaflet.js with OpenStreetMap
- **Database**: MySQL with JSON storage for geospatial data
- **API**: RESTful JSON APIs
- **Authentication**: Laravel's built-in authentication system

## Installation

### Prerequisites
- PHP 8.3 or higher
- Composer
- Node.js and npm
- MySQL 8.0 or higher

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd giscana
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database configuration**
   - Update your `.env` file with database credentials
   - Create a MySQL database for the application

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

# GISCANA_PROJECT_FRAMEWORK.md

Dokumen panduan arsitektur, skema basis data, logika bisnis, dan standar UI/UX untuk proyek **GIScana (Sistem Informasi Geografis & Manajemen Bantuan Bencana Bone Bolango)**. Dokumen ini dapat digunakan sebagai berkas patokan (*context standard*) untuk pengembang maupun kolaborator AI (seperti Claude, ChatGPT, atau Gemini).

---

## 1. Ringkasan Proyek & Teknologi

* **Nama Sistem:** GIScana (WebGIS & SIMBA - Sistem Informasi Manajemen Bantuan)


* **Wilayah Studi:** Kabupaten Bone Bolango (5 Kecamatan Fokus: Bone Raya, Bulawa, Bone, Bonepantai, Kabila Bone)


* **Teknologi Utama:**
* **Backend:** Laravel (Eloquent ORM, Blade Templating)
* **Frontend & UI:** Tailwind CSS, Alpine.js
* **Sistem Peta Spasial:** Leaflet.js, Leaflet Routing Machine, HTML5 Geolocation API



---

## 2. Refactoring Skema Basis Data (Laravel Migration)

Skema basis data mengalami pemisahan antara **Master Stok Logistik**, **Master Target Penerima (Warga Terdampak)**, dan **Transaksi Penyaluran** untuk mencegah *double claim* serta menghitung progres per wilayah secara dinamis.

```
                ┌───────────────────────────┐
                │         districts         │ (Master Kecamatan)
                └─────────────┬─────────────┘
                              │ 1:N
                ┌─────────────┴───────────┐
                │         villages        │ (Master Desa)
                └─────────────┬───────────┘
                              │ 1:N
                ┌─────────────┴───────────┐
                │      beneficiaries      │ (Master Target KK/Warga)
                └─────────────┬───────────┘
                              │ 1:N
┌──────────────────────────┐  │  ┌──────────────────────────┐
│     aid_inventories      │──┴──│     aid_distributions    │
│  (Master Stok Logistik)  │ 1:N │  (Transaksi Penyaluran)  │
└──────────────────────────┘     └──────────────────────────┘

```


## 5. Standar Visual Peta Interaktif & UI/UX

### A. Skema Warna Batas Kecamatan (Warm Colors)

* **Garis Batas (Border/Stroke):** Seragam Kuning (`#facc15`) untuk menjaga kesesuaian legenda peta tanpa mengubah UI legenda.
* **Isi Area (Fill Color):** Ditingkatkan kekentalannya (`fillOpacity: 0.5`) sesuai ID/Nama Kecamatan:



| ID Kecamatan | Nama Kecamatan

 | Border Color | Fill Color | Visual Style |
| --- | --- | --- | --- | --- |
| **1** | Bone Raya | `#facc15` | `#f59e0b` | Amber / Emas Pekat |
| **2** | Bulawa | `#facc15` | `#f97316` | Oranye Jingga |
| **3** | Bone | `#facc15` | `#ef4444` | Merah |
| **4** | Bonepantai | `#facc15` | `#f43f5e` | Rose / Merah Muda Pekat |
| **5** | Kabila Bone | `#facc15` | `#b45309` | Cokelat Terakota |

### B. Animasi Marker Titik Kumpul Terdekat

* **Perhitungan Terdekat:** Dilakukan via pencocokan koordinat Haversine di backend atau fungsi `userLatLng.distanceTo(marker.getLatLng())` pada seluruh layer fasilitas di frontend.
* **Animasi:** Menambahkan class CSS `.is-blinking` (`facilityPulse 1.2s infinite ease-in-out`).
* **Timer Otomatis:** Menggunakan `setTimeout` JavaScript sebesar **60.000 ms (1 menit)** untuk mematikan kedipan secara otomatis setelah diaktifkan.

### C. Komponen Panel Kontrol & Modal Lokasi

* **Pintas Navigasi 2-Arah:**
1. *Quick-Tool Floating Button* (Ikon Pin melayang di kiri atas canvas peta di bawah tombol Zoom).
2. *Action Panel Card* (Tombol "Atur Lokasi" & "Reset" di panel kontrol kanan bawah).


* **Modal Input Lokasi:**
* Didukung tombol **"Gunakan Lokasi GPS Saya Saat Ini"** (*Geolocation API*).
* Opsi input manual (Latitude & Longitude).


* **Responsivitas Layar:**
* **Desktop (`md:`):** Memiliki tombol *strip handle* di sisi kiri panel untuk menggeser (*slide-out*) panel kontrol ke kanan layar (`translate-x-full`).
* **Mobile:** *Bottom sheet* melayang yang dapat ditarik/ditutup dengan *handle bar* atas.

## Usage

### For Public Users
1. Visit the main page to view the interactive map
2. Use filters to view specific disaster types or risk levels
3. Click on map features to view detailed information
4. Use the search functionality to find specific locations

### For BPBD Staff and Administrators
1. Log in to access data management features
2. Use the API endpoints to manage spatial data
3. Update evacuation routes and facility information
4. Monitor aid distribution points

## Development

### Running in Development Mode
```bash
# Terminal 1: Laravel development server
php artisan serve

# Terminal 2: Vite development server
npm run dev
```

### Building for Production
```bash
npm run build
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is developed for academic purposes as part of a thesis project.

## Contact

For questions or support, please contact the development team.

---

**Giscana** - Enhancing community resilience through geospatial technology
