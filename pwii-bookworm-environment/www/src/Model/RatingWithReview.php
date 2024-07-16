<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

class RatingWithReview
{
    private ?int $id;
    private int $rating;
    private string $reviewText;
    private bool $isUserReview;

    public function __construct(?int $id, int $rating, string $reviewText, bool $isUserReview)
    {
        $this->id = $id;
        $this->rating = $rating;
        $this->reviewText = $reviewText;
        $this->isUserReview = $isUserReview;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function getReviewText(): string
    {
        return $this->reviewText;
    }

    public function isUserReview(): bool
    {
        return $this->isUserReview;
    }
}
