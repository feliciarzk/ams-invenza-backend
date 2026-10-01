<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    protected $table = 'stock_opname_items';

    public $timestamps = false;

    protected $fillable = [
        'opname_id',
        'item_id',
        'system_quantity',
        'physical_quantity',
    ];

    protected function casts(): array
    {
        return [
            'opname_id' => 'integer',
            'item_id' => 'integer',
            'system_quantity' => 'decimal:2',
            'physical_quantity' => 'decimal:2',
            'difference' => 'decimal:2',
        ];
    }

    public function opname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class, 'opname_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}