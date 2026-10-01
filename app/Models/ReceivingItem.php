<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceivingItem extends Model
{
    protected $table = 'receiving_items';

    public $timestamps = false;

    protected $fillable = [
        'receiving_id',
        'item_id',
        'quantity_received',
    ];

    protected function casts(): array
    {
        return [
            'receiving_id' => 'integer',
            'item_id' => 'integer',
            'quantity_received' => 'decimal:2',
        ];
    }

    public function receiving(): BelongsTo
    {
        return $this->belongsTo(Receiving::class, 'receiving_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}