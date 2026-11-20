<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'contact_person',
        'phone',
        'email',
        'address',
        'city',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
