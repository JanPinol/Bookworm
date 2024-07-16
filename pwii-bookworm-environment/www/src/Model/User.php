<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

use DateTime;

final class User
{
    private ?int $id;
    private string $email;
    private string $password;
    private ?string $username = null;
    private ?string $profile_picture = 'default.jpg';
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(
        string $email,
        string $password,
        DateTime $createdAt,
        DateTime $updatedAt,
        ?string $username,
        ?string $profile_picture,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->username = $username;
        $this->profile_picture = $profile_picture;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
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

    public function email(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function username(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function profile_picture(): ?string
    {
        return $this->profile_picture;
    }

    public function setProfile_picture(?string $profile_picture): self
    {
        $this->profile_picture = $profile_picture;
        return $this;
    }

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function updatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    public function getProfilePicture(): ?string
{
    return $this->profile_picture;
}
public function setProfilePicture(?string $profilePicture): self
{
    $this->profile_picture = $profilePicture;
    return $this;
}
}