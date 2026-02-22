<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AidDisaster extends Model
{
    use HasFactory;

    protected $table = 'aid_disasters';

    protected $fillable = [
        'nama_kecamatan',
        'jumlah_penerima_bantuan',
        'bantuan_terdistribusi',
        'is_active',
        'last_synced_at',
    ];

    protected $casts = [
        'jumlah_penerima_bantuan' => 'integer',
        'bantuan_terdistribusi'   => 'integer',
        'is_active'               => 'boolean',
        'last_synced_at'          => 'datetime',
    ];

    /**
     * Scope untuk data aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Hitung persentase distribusi bantuan
     */
    public function getPersentaseDistribusiAttribute(): float|null
    {
        if (!$this->jumlah_penerima_bantuan || $this->jumlah_penerima_bantuan === 0) {
            return null;
        }
        return round(($this->bantuan_terdistribusi / $this->jumlah_penerima_bantuan) * 100, 2);
    }

    /**
     * Sisa bantuan yang belum terdistribusi
     */
    public function getSisaBantuanAttribute(): int|null
    {
        if (is_null($this->jumlah_penerima_bantuan) || is_null($this->bantuan_terdistribusi)) {
            return null;
        }
        return max(0, $this->jumlah_penerima_bantuan - $this->bantuan_terdistribusi);
    }
}
