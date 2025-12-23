<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    public function creating(Review $review): void
    {
        // Validate rating
        if ($review->rating < 1 || $review->rating > 5) {
            throw new \InvalidArgumentException('Rating must be between 1 and 5');
        }
    }

    public function created(Review $review): void
    {
        // Auto-approve if user has previous approved reviews
        $approvedCount = Review::where('user_id', $review->user_id)
            ->where('status', 'approved')
            ->count();
        
        if ($approvedCount > 0) {
            $review->update(['status' => 'approved']);
        }
    }
}





















