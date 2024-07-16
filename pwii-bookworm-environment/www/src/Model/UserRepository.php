<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

interface UserRepository
{
    public function save(User $user): void;
}