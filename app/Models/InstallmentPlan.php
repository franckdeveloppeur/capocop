<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Observers\InstallmentPlanObserver;

#[ObservedBy([InstallmentPlanObserver::class])]
class InstallmentPlan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'order_id',
        'total_amount',
        'deposit_amount',
        'number_of_installments',
        'interval_days',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'number_of_installments' => 'integer',
            'interval_days' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class, 'plan_id');
    }
}
















