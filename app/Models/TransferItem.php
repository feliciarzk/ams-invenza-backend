<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferItem extends Model
{
    protected $table = 'transfer_items';

    public $timestamps = false;

    protected $fillable = [
        'transfer_id',
        'item_id',
        'quantity_requested',
        'quantity_sent',
        'quantity_received',
    ];

    protected function casts(): array
    {
        return [
            'transfer_id' => 'integer',
            'item_id' => 'integer',
            'quantity_requested' => 'decimal:2',
            'quantity_sent' => 'decimal:2',
            'quantity_received' => 'decimal:2',
        ];
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class, 'transfer_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}