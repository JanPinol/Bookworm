<?php

namespace Project\Bookworm\Model\Repository;

use DateTime;
use PDO;
use Project\Bookworm\Model\Rating;
use Project\Bookworm\Model\RatingRepository;

class MysqlRatingRepository implements RatingRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database) {
        $this->database = $database;
    }

    private function getDbConnection(): PDO {
        return $this->database->connection();
    }

    public function save(Rating $rating): void {
        $query = <<<'QUERY'
        INSERT INTO ratings (user_id, book_id, rating, created_at, updated_at)
        VALUES (:userId, :bookId, :rating, :createdAt, :updatedAt)
        ON DUPLICATE KEY UPDATE rating = :rating, updated_at = :updatedAt
        QUERY;

        $statement = $this->getDbConnection()->prepare($query);

        $userId = $rating->getUserId();
        $bookId = $rating->getBookId();
        $ratingValue = $rating->getRating();
        $createdAt = (new DateTime())->format(self::DATE_FORMAT);
        $updatedAt = (new DateTime())->format(self::DATE_FORMAT);

        $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $statement->bindParam(':bookId', $bookId, PDO::PARAM_INT);
        $statement->bindParam(':rating', $ratingValue, PDO::PARAM_INT);
        $statement->bindParam(':createdAt', $createdAt, PDO::PARAM_STR);
        $statement->bindParam(':updatedAt', $updatedAt, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getRatingByBookAndUser(int $userId, int $bookId): ?Rating {
        $query = <<<'QUERY'
        SELECT * FROM ratings WHERE user_id = :userId AND book_id = :bookId
        QUERY;

        $statement = $this->getDbConnection()->prepare($query);
        $statement->bindParam('userId', $userId, PDO::PARAM_INT);
        $statement->bindParam('bookId', $bookId, PDO::PARAM_INT);
        $statement->execute();

        $data = $statement->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return new Rating($data['user_id'], $data['book_id'], $data['rating']);
        }
        return null;
    }

    public function getAllRatingsByBook(int $bookId): array {
        $query = <<<'QUERY'
        SELECT * FROM ratings WHERE book_id = :bookId
        QUERY;

        $statement = $this->getDbConnection()->prepare($query);
        $statement->bindParam('bookId', $bookId, PDO::PARAM_INT);
        $statement->execute();

        $ratings = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $ratings[] = new Rating($row['user_id'], $row['book_id'], $row['rating']);
        }
        return $ratings;
    }
    
    public function deleteRatingByBookAndUser(int $userId, int $bookId): void {
        $query = "DELETE FROM ratings WHERE user_id = :userId AND book_id = :bookId";
        $statement = $this->getDbConnection()->prepare($query);
        $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $statement->bindParam(':bookId', $bookId, PDO::PARAM_INT);
        $statement->execute();
    }

}
