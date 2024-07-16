<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

interface ReviewRepository 
{
    public function save(Review $review): void;
    public function getReviewByBookAndUser(int $userId, int $bookId): ?Review;
    public function getAllReviewsByBook(int $bookId): array;
}