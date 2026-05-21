<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasUuid;

class EvacuationRoute extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'name',
        'description',
        'disaster_type',
        'line_coordinates',
        'route_type',
        'is_accessible',
        'is_active',
    ];

    protected $casts = [
        'line_coordinates' => 'array',
        'evacuation_facility_id' => 'integer',
        'is_accessible' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the evacuation facility associated with the route.
     */
    public function evacuationFacility()
    {
        return $this->belongsTo(EvacuationFacility::class);
    }

    /**
     * Scope for active routes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for accessible routes
     */
    public function scopeAccessible($query)
    {
        return $query->where('is_accessible', true);
    }

    /**
     * Scope for specific disaster type
     */
    public function scopeByDisasterType($query, $type)
    {
        return $query->where('disaster_type', $type)->orWhere('disaster_type', 'other');
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
                'disaster_type' => $this->disaster_type,
                'route_type' => $this->route_type,
            ],
            'geometry' => [
                'type' => 'LineString',
                'coordinates' => $this->line_coordinates ?? [],
            ],
        ];
    }
}
