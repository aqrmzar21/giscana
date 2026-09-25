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

### A. Tabel Master Wilayah (`districts` & `villages`)



```php
// Database Table: districts
Schema::create('districts', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique(); // Contoh: BA0080[cite: 2]
    $table->string('nama');           // Bone Raya, Bulawa, Bone, Bonepantai, Kabila Bone[cite: 2]
    $table->string('regency')->default('Bone Bolango');[cite: 2]
    $table->string('province')->default('Gorontalo');[cite: 2]
    $table->geometry('geom')->nullable();[cite: 2]
    $table->timestamps();
});

// Database Table: villages
Schema::create('villages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
    $table->string('code')->unique();
    $table->string('nama');
    $table->geometry('geom')->nullable();
    $table->timestamps();
});

```

### B. Tabel Master Target Penerima (`beneficiaries`)

Menggantikan sistem pendataan langsung pada transaksi untuk mengunci data warga.

```php
Schema::create('beneficiaries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
    $table->foreignId('village_id')->constrained('villages')->onDelete('cascade');
    $table->string('nama_kk');       // Nama Kepala Keluarga[cite: 5]
    $table->string('nik_kk')->nullable()->unique(); // Kunci pencegah pendaftaran warga ganda
    $table->text('alamat_detail')->nullable();
    $table->enum('status_bantuan', ['belum_menerima', 'sudah_menerima'])->default('belum_menerima');
    $table->timestamps();
});

```

### C. Tabel Master Stok Logistik (`aid_inventories`)

Menggantikan fungsi `aid_disasters` lama agar khusus mencatat inventaris stok barang masuk.

```php
Schema::create('aid_inventories', function (Blueprint $table) {
    $table->id();
    $table->string('nama_bantuan'); // Contoh: Sembako Paket A, Obat Paracetamol
    $table->enum('kategori', ['sembako', 'obat', 'pakaian', 'material', 'lainnya']);
    $table->string('sumber_bantuan'); // Contoh: BPBD Provinsi, Donasi PT. X
    $table->integer('jumlah_masuk');
    $table->integer('sisa_stok');
    $table->timestamps();
});

```

### D. Tabel Transaksi Penyaluran Bantuan (`aid_distributions`)

Menggantikan tabel `aid_recipients` lama.

```php
Schema::create('aid_distributions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('beneficiary_id')->constrained('beneficiaries')->onDelete('cascade');
    $table->foreignId('aid_inventory_id')->constrained('aid_inventories')->onDelete('cascade');
    $table->integer('jumlah_diterima');
    $table->date('tanggal_salur'); // Tanggal penyerahan[cite: 5]
    $table->foreignId('user_id')->constrained('users'); // Petugas BPBD/Admin yang mencatat[cite: 5]
    $table->timestamps();
});

```

---

## 3. Logika Bisnis & Pencegahan *Double Claim*

### A. Validasi Transaksi Penyaluran (`AidDistributionController.php`)

Setiap kali transaksi disimpankan, sistem wajib memvalidasi dua aspek:

1. **Aturan Anti-Double Claim:** Warga (`beneficiary_id`) tidak boleh menerima bantuan dengan `kategori` yang sama lebih dari satu kali.
2. **Aturan Stok Barang:** `jumlah_diterima` tidak boleh melebihi `sisa_stok` di tabel `aid_inventories`.

```php
public function store(Request $request)
{
    $request->validate([
        'beneficiary_id'   => 'required|exists:beneficiaries,id',
        'aid_inventory_id' => 'required|exists:aid_inventories,id',
        'jumlah_diterima'  => 'required|numeric|min:1',
        'tanggal_salur'    => 'required|date',
    ]);

    $stok = AidInventory::findOrFail($request->aid_inventory_id);

    // 1. Validasi Double Claim berdasarkan kategori bantuan
    $isDoubleClaim = AidDistribution::where('beneficiary_id', $request->beneficiary_id)
        ->whereHas('aidInventory', function ($query) use ($stok) {
            $query->where('kategori', $stok->kategori);
        })->exists();

    if ($isDoubleClaim) {
        return redirect()->back()->with('error', 'Gagal: Warga/KK tersebut sudah pernah menerima bantuan kategori ini!');
    }

    // 2. Validasi Ketersediaan Stok
    if ($stok->sisa_stok < $request->jumlah_diterima) {
        return redirect()->back()->with('error', 'Gagal: Stok barang tidak mencukupi!');
    }

    // 3. Eksekusi Transaksi Atomic
    DB::transaction(function () use ($request, $stok) {
        AidDistribution::create([
            'beneficiary_id'   => $request->beneficiary_id,
            'aid_inventory_id' => $request->aid_inventory_id,
            'jumlah_diterima'  => $request->jumlah_diterima,
            'tanggal_salur'    => $request->tanggal_salur,
            'user_id'          => auth()->id(),
        ]);

        // Potong sisa stok
        $stok->decrement('sisa_stok', $request->jumlah_diterima);

        // Update status warga
        Beneficiary::where('id', $request->beneficiary_id)->update([
            'status_bantuan' => 'sudah_menerima'
        ]);
    });

    return redirect()->route('admin.distributions.index')->with('success', 'Distribusi bantuan berhasil dicatat.');
}

```

---

## 4. Agregasi Dinamis Per Wilayah (Data Peta & Dashboard)

Ringkasan data persentase distribusi per kecamatan pada halaman dashboard (`image_e38f99.png`) dan peta interaktif dihitung secara otomatis (*real-time aggregation*) tanpa data statis manual.

### A. Output JSON Contract Endpoint (`MapController.php`)

```php
public function getMapData()
{
    $districts = District::all();

    $districtFeatures = $districts->map(function ($district) {
        // Aggregation otomatis dari relational data
        $totalRecipients = Beneficiary::where('district_id', $district->id)->count();
        $distributedAid  = Beneficiary::where('district_id', $district->id)
                            ->where('status_bantuan', 'sudah_menerima')
                            ->count();

        $percentage = $totalRecipients > 0 
            ? round(($distributedAid / $totalRecipients) * 100, 1) 
            : 0;

        return [
            'type' => 'Feature',
            'geometry' => is_string($district->geom) ? json_decode($district->geom) : $district->geom,
            'properties' => [
                'id'                      => $district->id,
                'district_name'           => $district->nama, // Bone Raya, Bulawa, Bone, Bonepantai, Kabila Bone[cite: 2]
                'total_recipients'        => $totalRecipients,
                'distributed_aid'         => $distributedAid,
                'remaining_recipients'    => $totalRecipients - $distributedAid
            ]
        ];
    });

    return response()->json([
        'district_boundaries' => [
            'type' => 'FeatureCollection',
            'features' => $districtFeatures
        ],
        // Feature collection spasial lainnya...
    ]);
}

```

---

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