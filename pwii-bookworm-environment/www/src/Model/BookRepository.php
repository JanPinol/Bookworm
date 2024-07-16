<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

interface BookRepository
{
    public function save(Book $book): void;
    public function getAllBooks(): array;
}