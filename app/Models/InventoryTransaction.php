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
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'change' => 'integer',
            'meta' => 'array',
            'created_at' => 'datetime',
        ];
    }

    // Les transactions d'inventaire ne doivent jamais être modifiées après création
    // On utilise seulement created_at, pas updated_at
    const UPDATED_AT = null;

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
















