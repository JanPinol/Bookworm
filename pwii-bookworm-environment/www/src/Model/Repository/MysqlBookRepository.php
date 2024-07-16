<?php

declare(strict_types=1);

namespace Project\Bookworm\Model\Repository;

use DateTime;
use PDO;
use Project\Bookworm\Model\Book;
use Project\Bookworm\Model\BookRepository;

class MysqlBookRepository implements BookRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function save(Book $book): void
    {
        $query = <<<'QUERY'
        INSERT INTO books(title, author, description, page_number, cover_image, created_at, updated_at)
        VALUES(:title, :author, :description, :page_number, :cover_image, :created_at, :updated_at)
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $title = $book->title();
        $author = $book->author();
        $description = $book->description();
        $page_number = $book->page_number();
        $cover_image = $book->cover_image();
        $createdAt = $book->created_at()->format(self::DATE_FORMAT);
        $updatedAt = $book->updated_at()->format(self::DATE_FORMAT);

        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->bindParam('author', $author, PDO::PARAM_STR);
        $statement->bindParam('description', $description, PDO::PARAM_STR);
        $statement->bindParam('page_number', $page_number, PDO::PARAM_INT);
        $statement->bindParam('cover_image', $cover_image, PDO::PARAM_STR);
        $statement->bindParam('created_at', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('updated_at', $updatedAt, PDO::PARAM_STR);

        $statement->execute();

        $book->setId((int)$this->database->connection()->lastInsertId());

    }

    public function getAllBooks(): array
    {
        $query = <<<'QUERY'
        SELECT * FROM books
        QUERY;

        $statement = $this->database->connection()->query($query);
        $books = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $books[] = new Book(
                $row['title'],
                $row['author'],
                $row['description'],
                $row['page_number'],
                $row['cover_image'],
                new DateTime($row['created_at']),
                new DateTime($row['updated_at']),
                (int)$row['id']
            );
        }

        return $books;
    }

    public function getBookById(int $id): ?Book
    {
        $query = <<<'QUERY'
        SELECT * FROM books WHERE id = :id
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return new Book(
            $row['title'],
            $row['author'],
            $row['description'],
            $row['page_number'],
            $row['cover_image'],
            new DateTime($row['created_at']),
            new DateTime($row['updated_at']),
            (int)$row['id']
        );
    }



}