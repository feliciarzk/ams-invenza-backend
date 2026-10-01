<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockPolicy extends Model
{
    protected $table = 'stock_policies';

    protected $fillable = [
        'location_id',
        'item_id',
        'minimum_stock',
        'maximum_stock',
        'reorder_quantity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'location_id' => 'integer',
            'item_id' => 'integer',
            'minimum_stock' => 'decimal:2',
            'maximum_stock' => 'decimal:2',
            'reorder_quantity' => 'decimal:2',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
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