<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

use DateTime;

class Rating
{
    private ?int $id;
    private int $userId;
    private int $bookId;
    private int $rating;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(int $userId, int $bookId, int $rating, ?int $id = null) {
        $this->userId = $userId;
        $this->bookId = $bookId;
        $this->rating = $rating;
        $this->createdAt = new DateTime(); 
        $this->updatedAt = new DateTime(); 
        $this->id = $id;
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getUserId(): int {
        return $this->userId;
    }

    public function getBookId(): int {
        return $this->bookId;
    }

    public function getRating(): int {
        return $this->rating;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    // Setters
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function setRating(int $rating): self {
        $this->rating = $rating;
        return $this;
    }

    public function setUpdatedAt(DateTime $updatedAt): self {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
