<?php

namespace Project\Bookworm\Model\Repository;

use PDO;
use Project\Bookworm\Model\Review;
use Project\Bookworm\Model\ReviewRepository;

class MysqlReviewRepository implements ReviewRepository
{
    private PDOSingleton $pdoSingleton;

    public function __construct(PDOSingleton $pdoSingleton) {
        $this->pdoSingleton = $pdoSingleton;
    }

    private function getDbConnection(): PDO {
        return $this->pdoSingleton->connection();
    }

    public function save(Review $review): void {
        $query = <<<'QUERY'
        INSERT INTO reviews (user_id, book_id, review_text, created_at, updated_at)
        VALUES (:userId, :bookId, :reviewText, NOW(), NOW())
        ON DUPLICATE KEY UPDATE review_text = :reviewText, updated_at = NOW()
        QUERY;

        $statement = $this->getDbConnection()->prepare($query);

        $userId = $review->getUserId();
        $bookId = $review->getBookId();
        $reviewText = $review->getReviewText();

        $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $statement->bindParam(':bookId', $bookId, PDO::PARAM_INT);
        $statement->bindParam(':reviewText', $reviewText, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getReviewByBookAndUser(int $userId, int $bookId): ?Review {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM reviews WHERE user_id = :userId AND book_id = :bookId";
        $stmt = $db->prepare($query);
        $stmt->execute([':userId' => $userId, ':bookId' => $bookId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return new Review($data['user_id'], $data['book_id'], $data['review_text']);
        }
        return null;
    }

    public function getAllReviewsByBook(int $bookId): array {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM reviews WHERE book_id = :bookId";
        $stmt = $db->prepare($query);
        $stmt->execute([':bookId' => $bookId]);
        $reviews = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reviews[] = new Review($row['user_id'], $row['book_id'], $row['review_text']);
        }
        return $reviews;
    }

    public function deleteReviewByBookAndUser(int $userId, int $bookId): void {
        $query = "DELETE FROM reviews WHERE user_id = :userId AND book_id = :bookId";
        $statement = $this->getDbConnection()->prepare($query);
        $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $statement->bindParam(':bookId', $bookId, PDO::PARAM_INT);
        $statement->execute();
    }    
}