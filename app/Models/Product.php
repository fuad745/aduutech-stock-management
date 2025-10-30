<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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

        static::updating(function (self $product) {
            if (Auth::check()) {
                $product->updated_by = Auth::id();
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
}
