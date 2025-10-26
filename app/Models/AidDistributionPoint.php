<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AidDistributionPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'aid_type',
        'point_coordinates',
        'address',
        'contact_person',
        'contact_phone',
        'capacity_per_day',
        'is_accessible',
        'is_active',
    ];

    protected $casts = [
        'point_coordinates' => 'array',
        'capacity_per_day' => 'integer',
        'is_accessible' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active points
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for accessible points
     */
    public function scopeAccessible($query)
    {
        return $query->where('is_accessible', true);
    }

    /**
     * Scope for specific aid type
     */
    public function scopeByAidType($query, $type)
    {
        return $query->where('aid_type', $type);
    }

    /**
     * Get GeoJSON representation
     */
    public function toGeoJSON()
    {
        return [
            'type' => 'Feature',
            'properties' => [
                'id' => $this->id,
                'name' => $this->name,
                'aid_type' => $this->aid_type,
                'address' => $this->address,
                'contact_person' => $this->contact_person,
                'contact_phone' => $this->contact_phone,
                'capacity_per_day' => $this->capacity_per_day,
            ],
            'geometry' => [
                'type' => 'Point',
                'coordinates' => $this->point_coordinates,
            ],
        ];
    }
}
