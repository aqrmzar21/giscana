<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'regency',
        'province',
        'remark',
    ];

    /**
     * Get GeoJSON representation
     */
    public function toGeoJSON()
    {
        // Using ST_AsGeoJSON to get geometry as JSON string, 
        // which needs to be parsed in the application or raw query.
        return [
            'type' => 'Feature',
            'properties' => [
                'id' => $this->id,
                'name' => $this->name,
                'regency' => $this->regency,
                'province' => $this->province,
                'code' => $this->code,
                'remark' => $this->remark,
            ],
            // We need the raw geometry for this to work correctly
            'geometry' => isset($this->geom_json) ? json_decode($this->geom_json, true) : null,
        ];
    }
}
