<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'variant_id',
        'change',
        'reason',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'change' => 'integer',
            'meta' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public $timestamps = false;

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}

