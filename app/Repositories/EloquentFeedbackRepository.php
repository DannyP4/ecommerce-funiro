<?php

namespace App\Repositories;

use App\Models\Feedback;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentFeedbackRepository implements FeedbackRepository
{
    public function getAllFeedbacks(int $perPage = 10): LengthAwarePaginator
    {
        return Feedback::with(['user', 'product'])->orderBy('updated_at', 'desc')->paginate($perPage);
    }

    public function getFeedbackById(int $id): ?Feedback
    {
        return Feedback::with(['user', 'product'])->find($id);
    }

    public function getFeedbacksByProductId(int $productId): Collection
    {
        return Feedback::where('product_id', $productId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getFeedbacksByUserId(int $userId, int $perPage): LengthAwarePaginator
    {
        return Feedback::where('user_id', $userId)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function createFeedback(array $data): Feedback
    {
        return Feedback::create($data);
    }

    public function updateFeedback(int $id, array $data): ?Feedback
    {
        $feedback = $this->getFeedbackById($id);

        if (!$feedback) {
            return null;
        }

        $feedback->update($data);

        return $feedback->fresh();
    }

    public function deleteFeedback(int $id): bool
    {
        $feedback = $this->getFeedbackById($id);

        if (!$feedback) {
            return false;
        }

        return $feedback->delete();
    }

    public function getRecentFeedbacks(int $limit = 10): Collection
    {
        return Feedback::with(['user', 'product'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getAverageRatingByProduct(int $productId): float
    {
        return Feedback::where('product_id', $productId)
            ->avg('rating') ?? 0.0;
    }

    public function count(): int
    {
        return Feedback::count();
    }

    public function countByProduct(int $productId): int
    {
        return Feedback::where('product_id', $productId)->count();
    }
    
    public function countByDate(string $date): int
    {
        return Feedback::whereDate('created_at', $date)->count();
    }
    
    public function countByRating(int $rating): int
    {
        return Feedback::where('rating', $rating)->count();
    }
}
