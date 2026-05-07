<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AidRecipient extends Model
{
    use \App\Traits\HasUuid;

    protected $fillable = [
        'date',
        'aid_type',
        'amount',
        'recipient_name',
        'village_id',
        'name',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'integer',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
}

