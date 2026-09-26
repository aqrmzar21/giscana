<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class AidDistribution extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'aid_distributions';

    protected $fillable = [
        'beneficiary_id',
        'aid_inventory_id',
        'village_id',
        'aid_disaster_id',
        'quantity_received',
        'distribution_date',
        'user_id',
        'description',
    ];

    protected $casts = [
        'distribution_date' => 'date',
        'quantity_received' => 'integer',
    ];

    /**
     * Relasi ke penerima bantuan (AidBeneficiary)
     */
    public function beneficiary()
    {
        return $this->belongsTo(AidBeneficiary::class, 'beneficiary_id');
    }

    /**
     * Alias relasi
     */
    public function aidBeneficiary()
    {
        return $this->belongsTo(AidBeneficiary::class, 'beneficiary_id');
    }

    public function aidInventory()
    {
        return $this->belongsTo(AidInventory::class, 'aid_inventory_id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function aidDisaster()
    {
        return $this->belongsTo(AidDisaster::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}