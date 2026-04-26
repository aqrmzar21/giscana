<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DisasterZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'disaster_type',
        'description',
        'risk_level',
        'point_coordinates',
        'area_hectares',
        'affected_population',
        'is_active',
    ];

    protected $casts = [
        'point_coordinates' => 'array',
        'area_hectares' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active disaster zones
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific disaster type
     */
    public function scopeByDisasterType($query, $type)
    {
        return $query->where('disaster_type', $type);
    }

    /**
     * Scope for specific risk level
     */
    public function scopeByRiskLevel($query, $level)
    {
        return $query->where('risk_level', $level);
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
                'risk_level' => $this->risk_level,
                'area_hectares' => $this->area_hectares,
            ],
            'geometry' => [
                'type' => 'Point',
                'coordinates' => $this->point_coordinates,
            ],
        ];
    }
}
