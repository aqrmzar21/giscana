<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    protected $fillable = [
        'district_id',
        'code',
        'yard',
        'full_name',
        'regency',
        'province',
    ];

    /**
     * Relasi ke kecamatan (district).
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get GeoJSON representation.
     */
    public function toGeoJSON()
    {
        return [
            'type'       => 'Feature',
            'properties' => [
                'id'            => $this->id,
                'yard'          => $this->yard,
                'full_name'     => $this->full_name,
                'code'          => $this->code,
                'regency'       => $this->regency,
                'province'      => $this->province,
                'district_id'   => $this->district_id,
            ],
            'geometry' => isset($this->geom_json) ? json_decode($this->geom_json, true) : null,
        ];
    }
}
