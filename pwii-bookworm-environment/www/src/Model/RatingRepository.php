<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

interface RatingRepository 
{
    public function save(Rating $rating): void;
    public function getRatingByBookAndUser(int $userId, int $bookId): ?Rating;
    public function getAllRatingsByBook(int $bookId): array;
}