<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Models\Batch;
use App\Models\StockMovement;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_name',
        'brand',
        'model',
        'price',
        'quantity',
        'type',
        'image',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'price' => 'float',
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    protected static function booted(): void
    {
        static::creating(function (self $product) {
            if (Auth::check()) {
                $product->created_by = Auth::id();
                $product->updated_by = Auth::id();
            }
        });

        static::created(function (self $product) {
            if ($product->quantity > 0) {
                StockMovement::create([
                    'product_id'      => $product->id,
                    'user_id'         => Auth::id() ?: null,
                    'type'            => 'initial',
                    'quantity_before' => 0,
                    'quantity_after'  => $product->quantity,
                    'quantity_change' => $product->quantity,
                    'note'            => 'Initial stock on product creation',
                ]);
            }
        });

        static::updating(function (self $product) {
            if (Auth::check()) {
                $product->updated_by = Auth::id();
            }
        });

        static::updated(function (self $product) {
            $before = (int) $product->getOriginal('quantity');
            $after  = (int) $product->quantity;

            if ($before !== $after) {
                StockMovement::create([
                    'product_id'      => $product->id,
                    'user_id'         => Auth::id() ?: null,
                    'type'            => 'adjustment',
                    'quantity_before' => $before,
                    'quantity_after'  => $after,
                    'quantity_change' => $after - $before,
                    'note'            => null,
                ]);
            }
        });

        static::deleting(function (self $product) {
            if (!$product->isForceDeleting() && Auth::check()) {
                $product->deleted_by = Auth::id();
                $product->saveQuietly();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function batches()
    {
        return $this->hasMany(Batch::class)->orderBy('expiry_date');
    }

    public function getAvailableQuantityAttribute(): int
    {
        return (int) $this->batches()->sum('quantity');
    }
}
