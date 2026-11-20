<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'batch_id',
        'user_id',
        'type',
        'quantity_before',
        'quantity_after',
        'quantity_change',
        'note',
    ];

    protected $casts = [
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
        'quantity_change' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
