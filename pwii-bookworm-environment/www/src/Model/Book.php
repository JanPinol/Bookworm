<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

use DateTime;

final class Book
{
    private ?int $id;
    private string $title;
    private string $author;
    private string $description;
    private int $page_number;
    private ?string $cover_image;
    private DateTime $created_at;
    private DateTime $updated_at;

    public function __construct(
        string $title,
        string $author,
        string $description,
        int $page_number,
        ?string $cover_image,
        DateTime $created_at,
        DateTime $updated_at,
        ?int $id = null
    ) {
        $this->title = $title;
        $this->author = $author;
        $this->description = $description;
        $this->page_number = $page_number;
        $this->cover_image = $cover_image;
        $this->id = $id;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function author(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function page_number(): int
    {
        return $this->page_number;
    }

    public function setPage_number(int $page_number): self
    {
        $this->page_number = $page_number;
        return $this;
    }

    public function cover_image(): string
    {
        return $this->cover_image;
    }

    public function setCover_image(string $cover_image): self
    {
        $this->cover_image = $cover_image;
        return $this;
    }

    public function created_at(): DateTime
    {
        return $this->created_at;
    }

    public function setCreated_at(DateTime $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function updated_at(): DateTime
    {
        return $this->updated_at;
    }

    public function setUpdated_at(DateTime $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

}