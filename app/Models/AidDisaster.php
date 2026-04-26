<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AidDisaster extends Model
{
    use HasFactory;

    protected $table = 'aid_disasters';

    protected $fillable = [
        'district_name',
        'total_recipients',
        'distributed_aid',
        'is_active',
        'last_synced_at',
    ];

    protected $casts = [
        'total_recipients' => 'integer',
        'distributed_aid'  => 'integer',
        'is_active'        => 'boolean',
        'last_synced_at'   => 'datetime',
    ];

    /**
     * Relation: one aid_disaster has many evacuation facilities.
     */
    public function evacuationFacilities()
    {
        return $this->hasMany(EvacuationFacility::class, 'aid_disaster_id');
    }

    /**
     * Scope for active data.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate distribution percentage.
     */
    public function getDistributionPercentageAttribute(): float|null
    {
        if (!$this->total_recipients || $this->total_recipients === 0) {
            return null;
        }
        return round(($this->distributed_aid / $this->total_recipients) * 100, 2);
    }

    /**
     * Remaining aid not yet distributed.
     */
    public function getRemainingAidAttribute(): int|null
    {
        if (is_null($this->total_recipients) || is_null($this->distributed_aid)) {
            return null;
        }
        return max(0, $this->total_recipients - $this->distributed_aid);
    }
}
