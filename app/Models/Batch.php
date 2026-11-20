<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
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

        static::created(function (self $batch) {
            // Skip during seeding/testing to avoid unwanted movements
            if (App::runningUnitTests()) {
                return;
            }

            if ($batch->quantity > 0) {
                StockMovement::create([
                    'product_id'      => $batch->product_id,
                    'batch_id'        => $batch->id,
                    'user_id'         => Auth::id() ?: null,
                    'type'            => 'initial',
                    'quantity_before' => 0,
                    'quantity_after'  => $batch->quantity,
                    'quantity_change' => $batch->quantity,
                    'note'            => 'Initial stock — lot ' . $batch->lot_number,
                ]);
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
