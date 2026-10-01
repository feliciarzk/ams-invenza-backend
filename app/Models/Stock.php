<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $table = 'stocks';

    public $timestamps = false;

    protected $fillable = [
        'location_id',
        'item_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'location_id' => 'integer',
            'item_id' => 'integer',
            'quantity' => 'decimal:2',
            'updated_at' => 'datetime',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}