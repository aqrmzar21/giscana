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
        'polygon_coordinates',
        'area_hectares',
        'affected_population',
        'is_active',
    ];

    protected $casts = [
        'polygon_coordinates' => 'array',
        'area_hectares' => 'decimal:2',
        'affected_population' => 'integer',
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
     * Get GeoJSON representation.
     * Mendukung point [lng, lat] atau polygon (array of rings).
     */
    public function toGeoJSON()
    {
        $coords = $this->polygon_coordinates;
        $isPoint = is_array($coords) && count($coords) === 2
            && is_numeric($coords[0] ?? null) && is_numeric($coords[1] ?? null);

        return [
            'type' => 'Feature',
            'properties' => [
                'id' => $this->id,
                'name' => $this->name,
                'disaster_type' => $this->disaster_type,
                'risk_level' => $this->risk_level,
                'area_hectares' => $this->area_hectares,
                'affected_population' => $this->affected_population,
            ],
            'geometry' => $isPoint
                ? ['type' => 'Point', 'coordinates' => $coords]
                : ['type' => 'Polygon', 'coordinates' => $coords],
        ];
    }
}
