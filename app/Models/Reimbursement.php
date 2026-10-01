<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reimbursement extends Model
{
    protected $table = 'reimbursements';

    public $timestamps = false;

    protected $fillable = [
        'location_id',
        'requester_id',
        'reimbursement_number',
        'reimbursement_date',
        'description',
        'amount',
        'status',
        'approved_by',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'location_id' => 'integer',
            'requester_id' => 'integer',
            'reimbursement_date' => 'date',
            'amount' => 'decimal:2',
            'approved_by' => 'integer',
            'paid_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}