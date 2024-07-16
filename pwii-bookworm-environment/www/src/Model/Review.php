<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

use DateTime;

class Review
{
    private ?int $id;
    private int $userId;
    private int $bookId;
    private string $reviewText;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(int $userId, int $bookId, string $reviewText, ?int $id = null) {
        $this->userId = $userId;
        $this->bookId = $bookId;
        $this->reviewText = $reviewText;
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

    public function getReviewText(): string {
        return $this->reviewText;
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

    public function setUserId(int $userId): self {
        $this->userId = $userId;
        return $this;
    }

    public function setBookId(int $bookId): self {
        $this->bookId = $bookId;
        return $this;
    }

    public function setReviewText(string $reviewText): self {
        $this->reviewText = $reviewText;
        return $this;
    }

    public function setCreatedAt(DateTime $createdAt): self {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(DateTime $updatedAt): self {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
