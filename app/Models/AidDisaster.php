<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasUuid;
use App\Models\AidBeneficiary;
use App\Models\AidDistribution;
use App\Models\District;
use App\Models\EvacuationFacility;

class AidDisaster extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'aid_disasters';

    protected $fillable = [
        'district_id',
        'district_name',
        'total_recipients',
        'total_received', // baru
        'distributed_aid',
        'is_active',
        'last_synced_at',
    ];

    protected $casts = [
        'total_recipients' => 'integer',
        'total_received' => 'integer', // baru
        'distributed_aid'  => 'integer',
        'is_active'        => 'boolean',
        'last_synced_at'   => 'datetime',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function evacuationFacilities()
    {
        return $this->hasMany(EvacuationFacility::class, 'aid_disaster_id');
    }

    public function distributions()
    {
        return $this->hasMany(AidDistribution::class, 'aid_disaster_id');
    }

    /**
     * Alias method untuk kompatibilitas dengan observer/seeder lama
     */
    public function recalculateDistributedAid()
    {
        $this->recalculate();
    }

    /**
     * Hitung ulang akumulasi target warga dan bantuan tersalur secara otomatis
     */
    public function recalculate()
    {
        if ($this->district_id) {
            $this->total_recipients = AidBeneficiary::where('district_id', $this->district_id)->count();

            $this->distributed_aid = AidDistribution::whereHas('beneficiary', function ($q) {
                $q->where('district_id', $this->district_id);
            })->sum('quantity_received');

            // Tambahan: hitung KK unik yang sudah menerima
            $this->total_received = AidDistribution::whereHas('beneficiary', function ($q) {
                $q->where('district_id', $this->district_id);
            })->distinct('beneficiary_id')->count('beneficiary_id');
        } else {
            $this->total_recipients = AidBeneficiary::whereHas('district', function ($q) {
                $q->where('name', $this->district_name);
            })->count();

            $this->distributed_aid = AidDistribution::whereHas('beneficiary.district', function ($q) {
                $q->where('name', $this->district_name);
            })->sum('quantity_received');

            $this->total_received = AidDistribution::whereHas('beneficiary.district', function ($q) {
                $q->where('name', $this->district_name);
            })->distinct('beneficiary_id')->count('beneficiary_id');
        }

        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getReceivedPercentageAttribute(): float
    {
        if (!$this->total_recipients || $this->total_recipients === 0) {
            return 0.0;
        }
        return round(($this->total_received / $this->total_recipients) * 100, 2);
    }

    public function getRemainingAidAttribute(): int|null
    {
        if (is_null($this->total_recipients) || is_null($this->total_received)) {
            return 0;
        }
        return max(0, $this->total_recipients - $this->total_received);
    }
}