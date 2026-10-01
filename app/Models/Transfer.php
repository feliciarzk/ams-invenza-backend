<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    protected $table = 'transfers';

    public $timestamps = false;

    protected $fillable = [
        'transfer_number',
        'from_location_id',
        'to_location_id',
        'status',
        'requested_by',
        'approved_by',
        'requested_at',
        'approved_at',
        'received_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'from_location_id' => 'integer',
            'to_location_id' => 'integer',
            'requested_by' => 'integer',
            'approved_by' => 'integer',
            'requested_at' => 'datetime',
            'approved_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}