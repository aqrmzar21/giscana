<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class AidInventory extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'item_name',
        'category',
        'source',
        'initial_stock',
        'date_stock',
        'remaining_stock',
        'is_active',
    ];

    protected $casts = [
        'initial_stock'   => 'integer',
        'remaining_stock' => 'integer',
        'is_active'       => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function distributions()
    {
        return $this->hasMany(AidDistribution::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}