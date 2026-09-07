<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasUuid;

class DisasterHazardLayer extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'disaster_type',
        'label',
        'geojson_path',
        'fill_color',
        'border_color',
        'fill_opacity',
        'border_weight',
        'icon_emoji',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'fill_opacity'   => 'float',
        'border_weight'  => 'float',
        'is_active'      => 'boolean',
        'sort_order'     => 'integer',
    ];

    /**
     * Scope untuk layer yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope filter berdasarkan jenis bencana.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('disaster_type', $type);
    }

    /**
     * Kembalikan data sebagai array untuk frontend (JSON-safe).
     */
    public function toLayerConfig(): array
    {
        return [
            'id'           => $this->id,
            'disaster_type' => $this->disaster_type,
            'label'        => $this->label,
            'geojson_path' => $this->geojson_path,
            'fill_color'   => $this->fill_color,
            'border_color' => $this->border_color,
            'fill_opacity' => $this->fill_opacity,
            'border_weight' => $this->border_weight,
            'icon_emoji'   => $this->icon_emoji,
            'description'  => $this->description,
        ];
    }
}
