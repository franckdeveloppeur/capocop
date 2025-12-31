<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Observers\CategoryObserver;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

#[ObservedBy([CategoryObserver::class])]
class Category extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'order_column',
    ];

    protected function casts(): array
    {
        return [
            'order_column' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['parent_id', 'name', 'slug', 'description', 'order_column'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => __('Catégorie créée'),
                'updated' => __('Catégorie modifiée'),
                'deleted' => __('Catégorie supprimée'),
                default => __('Activité sur la catégorie'),
            });
    }
}





















