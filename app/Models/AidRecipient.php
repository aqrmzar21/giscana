<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AidRecipient extends Model
{
    use \App\Traits\HasUuid;

    protected static function booted()
    {
        static::saved(function ($aidRecipient) {
            if ($aidRecipient->aidDisaster) {
                $aidRecipient->aidDisaster->recalculateDistributedAid();
            }
            
            // Handle if aid_disaster_id changed
            if ($aidRecipient->isDirty('aid_disaster_id')) {
                $originalDisasterId = $aidRecipient->getOriginal('aid_disaster_id');
                if ($originalDisasterId) {
                    $originalDisaster = AidDisaster::find($originalDisasterId);
                    if ($originalDisaster) {
                        $originalDisaster->recalculateDistributedAid();
                    }
                }
            }
        });

        static::deleted(function ($aidRecipient) {
            if ($aidRecipient->aidDisaster) {
                $aidRecipient->aidDisaster->recalculateDistributedAid();
            }
        });
    }

    protected $fillable = [
        'date',
        'aid_type',
        'amount',
        'recipient_name',
        'village_id',
        'aid_disaster_id',
        'name',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'integer',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    // Relationship to aid disaster
    public function aidDisaster()
    {
        return $this->belongsTo(AidDisaster::class);
    }
}

