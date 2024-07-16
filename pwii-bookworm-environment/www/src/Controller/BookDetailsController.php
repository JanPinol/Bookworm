<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Project\Bookworm\Model\BookRepository;
use Project\Bookworm\Model\ReviewRepository;
use Project\Bookworm\Model\RatingRepository;
use Project\Bookworm\Model\Rating;
use Project\Bookworm\Model\Review;
use Project\Bookworm\Model\RatingWithReview;

class BookDetailsController
{
    private Twig $twig;
    private BookRepository $bookRepository;
    private ReviewRepository $reviewRepository;
    private RatingRepository $ratingRepository;

    public function __construct(Twig $twig, BookRepository $bookRepository, ReviewRepository $reviewRepository, RatingRepository $ratingRepository)
    {
        $this->twig = $twig;
        $this->bookRepository = $bookRepository;
        $this->reviewRepository = $reviewRepository;
        $this->ratingRepository = $ratingRepository;
    }

    public function showBookDetails(Request $request, Response $response): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        $authenticated = isset($_SESSION['user_id']);
        if (!$authenticated) {
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }
    
        $bookId = (int) $request->getAttribute('id');
        $userId = $_SESSION['user_id'];
        $book = $this->bookRepository->getBookById($bookId);
        $reviews = $this->reviewRepository->getAllReviewsByBook($bookId);
        $ratings = $this->ratingRepository->getAllRatingsByBook($bookId);

        $reviewsWithRatings = [];

        $ratingsMap = [];
        foreach ($ratings as $rating) {
            $ratingsMap[$rating->getUserId()] = $rating->getRating();
        }
    
        foreach ($reviews as $review) {
            $userIdReview = $review->getUserId();
            $ratingValue = $ratingsMap[$userIdReview] ?? null;
            $isUserReview = ($userIdReview === $userId);
            $reviewsWithRatings[] = new RatingWithReview($review->getId(), $ratingValue, $review->getReviewText(), $isUserReview);
        }
    
        return $this->twig->render($response, 'book-details.twig', [
            'book' => $book,
            'reviewsWithRatings' => $reviewsWithRatings,
        ]);
    }

    public function updateRating(Request $request, Response $response, array $args): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }

        $bookId = (int) $args['id'];
        $userId = $_SESSION['user_id'];

        $data = json_decode($request->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['rating'])) {
            $response->getBody()->write(json_encode(['error' => 'Missing rating field']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $ratingValue = (int) $data['rating'];
        if ($ratingValue < 1 || $ratingValue > 5) {
            $response->getBody()->write(json_encode(['error' => 'Rating value must be between 1 and 5']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $rating = $this->ratingRepository->getRatingByBookAndUser($userId, $bookId) ?? new Rating($userId, $bookId, $ratingValue);
        $rating->setRating($ratingValue);
        $this->ratingRepository->save($rating);

        $response->getBody()->write(json_encode(['success' => 'Rating updated successfully']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateReview(Request $request, Response $response, array $args): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }

        $bookId = (int) $args['id'];
        $userId = $_SESSION['user_id'];

        $data = json_decode($request->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['review_text'])) {
            $response->getBody()->write(json_encode(['error' => 'Invalid JSON or missing review_text field']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $reviewText = trim($data['review_text']);
        if (strlen($reviewText) > 500) {
            $response->getBody()->write(json_encode(['error' => 'Review text too long']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $review = $this->reviewRepository->getReviewByBookAndUser($userId, $bookId) ?? new Review($userId, $bookId, $reviewText);
        $review->setReviewText($reviewText);
        $this->reviewRepository->save($review);

        $response->getBody()->write(json_encode(['success' => 'Review updated successfully']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function deleteReview(Request $request, Response $response, array $args): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        if (!isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }
    
        $bookId = (int) $args['id'];
        $userId = $_SESSION['user_id'];
    
        $this->reviewRepository->deleteReviewByBookAndUser($userId, $bookId);
    
        $response->getBody()->write(json_encode(['success' => 'Review deleted successfully']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
  
    public function deleteRating(Request $request, Response $response, array $args): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }

        $bookId = (int) $args['id'];
        $userId = $_SESSION['user_id'];

        $this->ratingRepository->deleteRatingByBookAndUser($userId, $bookId);

        $response->getBody()->write(json_encode(['success' => 'Rating deleted successfully']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}
