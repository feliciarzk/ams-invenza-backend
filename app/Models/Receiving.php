<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receiving extends Model
{
    protected $table = 'receivings';

    public $timestamps = false;

    protected $fillable = [
        'purchase_id',
        'location_id',
        'receiving_number',
        'receiving_date',
        'notes',
        'received_by',
    ];

    protected function casts(): array
    {
        return [
            'purchase_id' => 'integer',
            'location_id' => 'integer',
            'receiving_date' => 'date',
            'received_by' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}