<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $table = 'expenses';

    public $timestamps = false;

    protected $fillable = [
        'location_id',
        'expense_number',
        'expense_date',
        'category',
        'description',
        'amount',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'location_id' => 'integer',
            'expense_date' => 'date',
            'amount' => 'decimal:2',
            'created_by' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
