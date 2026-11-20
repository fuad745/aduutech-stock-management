<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Batch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'lot_number',
        'expiry_date',
        'supplier',
        'received_date',
        'landed_cost',
        'storage_condition',
        'quantity',
        'created_by',
    ];

    protected $casts = [
        'expiry_date'   => 'date',
        'received_date' => 'date',
        'landed_cost'   => 'decimal:2',
        'quantity'      => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $batch) {
            if (Auth::check()) {
                $batch->created_by = Auth::id();
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeExpiringSoon($query, int $days = 90)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays($days))
            ->whereDate('expiry_date', '>=', now());
    }

    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }
}
