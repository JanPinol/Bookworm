<?php

namespace Project\Bookworm\Model\Repository;

use PDO;
use Project\Bookworm\Model\Forum;
use Project\Bookworm\Model\ForumRepository;

class MysqlForumRepository implements ForumRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function save(Forum $forum): void
    {
        $query = <<<'QUERY'
        INSERT INTO forums(title, description, created_at, updated_at)
        VALUES(:title, :description, :created_at, :updated_at)
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $title = $forum->getTitle();
    $description = $forum->getDescription();
    $createdAt = $forum->getCreatedAt()->format(self::DATE_FORMAT);
    $updatedAt = $forum->getUpdatedAt()->format(self::DATE_FORMAT);

    $statement->bindParam('title', $title, PDO::PARAM_STR);
    $statement->bindParam('description', $description, PDO::PARAM_STR);
    $statement->bindParam('created_at', $createdAt, PDO::PARAM_STR);
    $statement->bindParam('updated_at', $updatedAt, PDO::PARAM_STR);

        $statement->execute();

        $forum->setId((int)$this->database->connection()->lastInsertId());
    }
    public function findAll(): array
    {
        $query = "SELECT * FROM forums";
        $statement = $this->database->connection()->prepare($query);
        $statement->execute();
    
        $forums = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $forum = new Forum(
                (int)$row['id'], 
                $row['title'], 
                $row['description'], 
                $row['created_at'], 
                $row['updated_at']
            );
            $forums[] = $forum;
        }
    
        return $forums;
    }
public function findById(int $id): ?Forum
{
    $query = "SELECT * FROM forums WHERE id = :id";
    $statement = $this->database->connection()->prepare($query);
    $statement->bindParam('id', $id, PDO::PARAM_INT);
    $statement->execute();

    $row = $statement->fetch(PDO::FETCH_ASSOC);

    if ($row === false) {
        return null;
    }

    return new Forum(
        (int)$row['id'], 
        $row['title'], 
        $row['description'], 
        new \DateTime($row['created_at']), 
        new \DateTime($row['updated_at'])
    );
}
}