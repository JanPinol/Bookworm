<?php

declare(strict_types=1);

namespace Project\Bookworm\Model\Repository;

use PDO;
use Project\Bookworm\Model\User;
use Project\Bookworm\Model\UserRepository;

final class MysqlUserRepository implements UserRepository

{
    private const PROFILE_PICTURES_DIR = '/uploadsAAAAAAAA/';
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function save(User $user): void
{
    $query = <<<'QUERY'
        INSERT INTO users (email, password, username, profile_picture, created_at, updated_at)
        VALUES (:email, :password, :username, :profile_picture, :created_at, :updated_at)
    QUERY;

    $statement = $this->database->connection()->prepare($query);

    $email = $user->email();
    $password = $user->password();
    $username = $user->username();
    $profile_picture = $user->profile_picture();
    if ($profile_picture === null) {
        $profile_picture = 'default.jpg';
    }
    $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
    $updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);

    $statement->bindParam('email', $email, PDO::PARAM_STR);
    $statement->bindParam('password', $password, PDO::PARAM_STR);
    $statement->bindParam('username', $username, PDO::PARAM_STR);
    $statement->bindParam('profile_picture', $profile_picture, PDO::PARAM_STR);
    $statement->bindParam('created_at', $createdAt, PDO::PARAM_STR);
    $statement->bindParam('updated_at', $updatedAt, PDO::PARAM_STR);

    $statement->execute();

    $user->setId((int)$this->database->connection()->lastInsertId());
}

    public function findByEmailAndPassword(string $email, string $password): ?User
    {
        $query = <<<'QUERY'
        SELECT * FROM users WHERE email = :email AND password = :password
    QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);

        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }

        return new User(
            $user['email'],
            $user['password'],
            new \DateTime($user['created_at']),
            new \DateTime($user['updated_at']),
            $user['username'],
            null,
            (int)$user['id']
        );
    }

    public function findByEmail(string $email): ?User
    {
        $query = <<<'QUERY'
        SELECT * FROM users WHERE email = :email
    QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }

        return new User(
            $user['email'],
            $user['password'],
            new \DateTime($user['created_at']),
            new \DateTime($user['updated_at']),
            $user['username'],
            null,
            (int)$user['id']
        );
    }

    public function findById(int $id): ?User
    {
        $query = <<<'QUERY'
            SELECT * FROM users WHERE id = :id
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_INT);

        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC); 
        if (!$user) {
            return null;
        }

        return new User(
            $user['email'],
            $user['password'],
            new \DateTime($user['created_at']),
            new \DateTime($user['updated_at']),
            $user['username'],
            $user['profile_picture'],
            (int)$user['id']
        );
    }

    public function update(User $user): void
{
    $query = <<<'QUERY'
        UPDATE users
        SET email = :email, username = :username, password = :password, profile_picture = :profile_picture, updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    QUERY;

    $statement = $this->database->connection()->prepare($query);
    $statement->execute([
        'id' => $user->id(),
        'email' => $user->email(),
        'username' => $user->username(),
        'password' => $user->password(),
        'profile_picture' => $user->profile_picture(),
    ]);
}

public function updateProfilePicture(int $id, string $profile_picture): void
{
    $query = <<<'QUERY'
    UPDATE users SET profile_picture = :profile_picture WHERE id = :id
    QUERY;

    $statement = $this->database->connection()->prepare($query);

    // Extrae el nombre de la imagen del path
    $profile_picture = basename($profile_picture);

    $statement->bindParam('profile_picture', $profile_picture, PDO::PARAM_STR);
    $statement->bindParam('id', $id, PDO::PARAM_INT);

    $statement->execute();
}
    public function findByUsername(string $username): ?User
{
    $query = <<<'QUERY'
    SELECT * FROM users WHERE username = :username
QUERY;

    $statement = $this->database->connection()->prepare($query);
    $statement->execute([
        'username' => $username,
    ]);

    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return null;
    }

    return new User(
        $user['email'],
        $user['password'],
        new \DateTime($user['created_at']),
        new \DateTime($user['updated_at']),
        $user['username'],
        null,
        (int)$user['id']
    );
}
public function getLastInsertId(): int
{
    $query = 'SELECT LAST_INSERT_ID()';
    $statement = $this->database->connection()->prepare($query);
    $statement->execute();
    return (int)$statement->fetchColumn();
}
}