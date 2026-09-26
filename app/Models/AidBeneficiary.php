<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class AidBeneficiary extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'district_id',
        'village_id',
        'recipient_name',
        'identity_card_number',
        'address_detail',
        'aid_status',
    ];

    protected static function booted()
    {
        static::saved(function ($beneficiary) {
            $aidDisaster = AidDisaster::where('district_id', $beneficiary->district_id)->first();
            if ($aidDisaster) {
                $aidDisaster->recalculate();
            }
        });

        static::deleted(function ($beneficiary) {
            $aidDisaster = AidDisaster::where('district_id', $beneficiary->district_id)->first();
            if ($aidDisaster) {
                $aidDisaster->recalculate();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function distributions()
    {
        return $this->hasMany(AidDistribution::class);
    }
}