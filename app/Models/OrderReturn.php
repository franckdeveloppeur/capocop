<?php

namespace App\Models;

use App\Observers\OrderReturnObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([OrderReturnObserver::class])]
class OrderReturn extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'order_id',
        'order_item_id',
        'user_id',
        'reason',
        'description',
        'status',
        'refund_amount',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'refund_amount' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
