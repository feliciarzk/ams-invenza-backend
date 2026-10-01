<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuPrice extends Model
{
    protected $table = 'menu_prices';

    protected $fillable = [
        'item_id',
        'location_id',
        'selling_price',
        'hpp',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'location_id' => 'integer',
            'selling_price' => 'decimal:2',
            'hpp' => 'decimal:2',
            'is_available' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}