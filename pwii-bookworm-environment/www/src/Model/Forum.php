<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

use DateTime;

class Forum
{
    private int $id;
    private string $title;
    private string $description;
    private DateTime $created_at;
    private DateTime $updated_at;

    public function __construct(int $id, string $title, string $description, string $createdAt, string $updatedAt)
{
    $this->id = $id;
    $this->title = $title;
    $this->description = $description;
    $this->created_at = new \DateTime($createdAt);
    $this->updated_at = new \DateTime($updatedAt);
}
    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }
    
    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }
    public function toArray()
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'description' => $this->description,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ];
}
public function setId(int $id): void
    {
        $this->id = $id;
    }
}