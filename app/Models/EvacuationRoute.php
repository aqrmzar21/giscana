<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvacuationRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'disaster_type',
        'line_coordinates',
        'length_km',
        'route_type',
        'capacity_per_hour',
        'is_accessible',
        'is_active',
    ];

    protected $casts = [
        'line_coordinates' => 'array',
        'length_km' => 'decimal:2',
        'capacity_per_hour' => 'integer',
        'is_accessible' => 'boolean',
        'is_active' => 'boolean',
    ];

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
                'length_km' => $this->length_km,
                'capacity_per_hour' => $this->capacity_per_hour,
            ],
            'geometry' => [
                'type' => 'LineString',
                'coordinates' => $this->line_coordinates,
            ],
        ];
    }
}
