<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockCountItem extends Model
{
    protected $fillable = [
        'session_id',
        'batch_id',
        'system_quantity',
        'counted_quantity',
        'discrepancy',
        'note',
    ];

    protected $casts = [
        'system_quantity'  => 'integer',
        'counted_quantity' => 'integer',
        'discrepancy'      => 'integer',
    ];

    public function session()
    {
        return $this->belongsTo(StockCountSession::class, 'session_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class)->with('product');
    }

    public function getStatusAttribute(): string
    {
        if ($this->counted_quantity === null) {
            return 'uncounted';
        }

        return match (true) {
            $this->discrepancy === 0  => 'match',
            $this->discrepancy < 0    => 'short',
            default                   => 'over',
        };
    }
}
