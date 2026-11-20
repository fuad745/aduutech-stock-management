<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StockCountSession extends Model
{
    protected $fillable = [
        'name',
        'status',
        'notes',
        'completed_at',
        'completed_by',
        'created_by',
    ];

    protected $casts = [
        'status'       => 'string',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $session) {
            if (Auth::check()) {
                $session->created_by = Auth::id();
            }
        });
    }

    public function items()
    {
        return $this->hasMany(StockCountItem::class, 'session_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
