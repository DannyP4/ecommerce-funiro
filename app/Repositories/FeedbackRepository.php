<?php

namespace App\Repositories;

use App\Models\Feedback;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface FeedbackRepository
{
    public function getAllFeedbacks(int $perPage = 10): LengthAwarePaginator;
    
    public function getFeedbackById(int $id): ?Feedback;
    
    public function getFeedbacksByProductId(int $productId): Collection;
    
    public function getFeedbacksByUserId(int $userId, int $perPage): LengthAwarePaginator;
    
    public function createFeedback(array $data): Feedback;
    
    public function updateFeedback(int $id, array $data): ?Feedback;
    
    public function deleteFeedback(int $id): bool;
    
    public function getRecentFeedbacks(int $limit = 10): Collection;
    
    public function getAverageRatingByProduct(int $productId): float;
    
    public function count(): int;
    
    public function countByProduct(int $productId): int;
    
    public function countByDate(string $date): int;
    
    public function countByRating(int $rating): int;
}
