<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'model_type',
        'model_id',
        'collection_name',
        'file_name',
        'mime_type',
        'disk',
        'size',
        'custom_properties',
        'order_column',
    ];

    protected function casts(): array
    {
        return [
            'custom_properties' => 'array',
            'size' => 'integer',
            'order_column' => 'integer',
        ];
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}

