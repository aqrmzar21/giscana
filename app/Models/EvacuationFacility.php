<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvacuationFacility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'facility_type',
        'point_coordinates',
        'capacity',
        'address',
        'contact_person',
        'contact_phone',
        'has_medical_facility',
        'has_food_storage',
        'is_accessible',
        'is_active',
    ];

    protected $casts = [
        'point_coordinates' => 'array',
        'capacity' => 'integer',
        'has_medical_facility' => 'boolean',
        'has_food_storage' => 'boolean',
        'is_accessible' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active facilities
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for accessible facilities
     */
    public function scopeAccessible($query)
    {
        return $query->where('is_accessible', true);
    }

    /**
     * Scope for facilities with medical support
     */
    public function scopeWithMedical($query)
    {
        return $query->where('has_medical_facility', true);
    }

    /**
     * Scope for facilities with food storage
     */
    public function scopeWithFoodStorage($query)
    {
        return $query->where('has_food_storage', true);
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
                'facility_type' => $this->facility_type,
                'capacity' => $this->capacity,
                'address' => $this->address,
                'contact_person' => $this->contact_person,
                'contact_phone' => $this->contact_phone,
                'has_medical_facility' => $this->has_medical_facility,
                'has_food_storage' => $this->has_food_storage,
            ],
            'geometry' => [
                'type' => 'Point',
                'coordinates' => $this->point_coordinates,
            ],
        ];
    }
}
