<?php

namespace Project\Bookworm\Controller;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Project\Bookworm\Model\Book;
use Project\Bookworm\Model\BookRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class CatalogueController
{

    private BookRepository $bookRepository;
    private Twig $twig;

    public function __construct(Twig $twig, BookRepository $bookRepository)
    {
        $this->twig = $twig;
        $this->bookRepository = $bookRepository;
    }

    public function showCatalogue(Request $request, Response $response): ResponseInterface
    {
        $authenticated = isset($_SESSION['user_id']);
        if (!$authenticated) {
            return $response->withHeader('Location', '/sign-in');
        }

        $books = $this->bookRepository->getAllBooks();

        return $this->twig->render($response, 'book-form.twig', [
            'books' => $books
        ]);
    }


    public function handleFormBook (Request $request, Response $response): ResponseInterface
    {
        $data = $request->getParsedBody();

        if (!empty($data['isbn'])) {
            return $this->handleIsbnImport($request, $response);
        } else {
            return $this->handleManualBookAddition($request, $response);
        }

    }

    public function handleManualBookAddition(Request $request, Response $response): ResponseInterface
    {
        $data = $request->getParsedBody();
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = 'The title is required.';
        }

        if (empty($data['author'])) {
            $errors['author'] = 'The author is required.';
        }

        if (empty($data['description'])) {
            $errors['description'] = 'The description is required.';
        }

        if (empty($data['page_number'])) {
            $errors['page_number'] = 'The page number is required.';
        }

        if (empty($data['page_number'])) {
            $errors['page_number'] = 'The page number must be a positive number.';
        } else {
            $data['page_number'] = (int) $data['page_number'];
            if ($data['page_number'] < 0) {
                $errors['page_number'] = 'The page number must be a positive number.';
            }
        }

        if (empty($errors)) {

            $book = new Book(
                $data['title'],
                $data['author'],
                $data['description'],
                $data['page_number'],
                $data['cover_image'],
                new DateTime(),
                new DateTime()
            );

            $this->bookRepository->save($book);
            return $response->withHeader('Location', '/catalogue');
        } else {
            $books = $this->bookRepository->getAllBooks();

            return $this->twig->render($response, 'book-form.twig', [
                'books' => $books,
                'errors' => $errors,
                'formData' => $data
            ]);
        }
    }

    public function handleIsbnImport(Request $request, Response $response): ResponseInterface
    {
        $data = $request->getParsedBody();
        $errorsIsbn = [];

        $bookData = $this->fetchBookDataByIsbn($data['isbn']);

        if ($bookData === null) {
            $errorsIsbn['isbn'] = 'Book not found with provided ISBN.';

            $books = $this->bookRepository->getAllBooks();

            return $this->twig->render($response, 'book-form.twig', [
                'books' => $books,
                'errorsIsbn' => $errorsIsbn,
                'formData' => $data
            ]);
        }

        $book = new Book(
            $bookData['title'],
            $bookData['author'],
            $bookData['description'],
            $bookData['page_number'],
            $bookData['cover_image'],
            new DateTime(),
            new DateTime()
        );

        $this->bookRepository->save($book);

        return $response->withHeader('Location', '/catalogue');
    }

    private function fetchBookDataByIsbn(string $isbn): ?array
    {
        /*
         ISBNs de Proba
        9780140328721 - FOX
        9780393064506 - DRACULA
        9782743433956 - LITTLE WOMEN
        9789176370728 - FRANKENSTEIN
        9780393923209 - METAMORPHOSIS
        9780060174903 - THE BELL JAR
        9781943138425 - ANIMAL FARM
        */

        $client = new Client();

        $apiUrl = "https://openlibrary.org/isbn/{$isbn}.json";
        try {
            $response = $client->get($apiUrl);
        } catch (RequestException $e) {
            return null;
        }

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $bookDataIsbn = json_decode($response->getBody(), true);

        if (!isset($bookDataIsbn['title'], $bookDataIsbn['number_of_pages'], $bookDataIsbn['works'][0]['key'])) {
            return null;
        }

        $worksKey = $bookDataIsbn['works'][0]['key'];

        $apiUrl = "https://openlibrary.org{$worksKey}.json";
        try {
            $response = $client->get($apiUrl);
        } catch (RequestException $e) {
            return null;
        }

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $bookDataWorks = json_decode($response->getBody(), true);

        // sometimes description is ['description']['value'] and sometimes ['description']
        if (isset($bookDataWorks['description']['value'])) {
            $description = $bookDataWorks['description']['value'];
        } elseif (isset($bookDataWorks['description'])) {
            $description = $bookDataWorks['description'];
        } else {
            return null;
        }

        if (!isset($bookDataWorks['authors'][0]['author']['key'])) {
            return null;
        }

        $authorKey = $bookDataWorks['authors'][0]['author']['key'];

        $apiUrl = "https://openlibrary.org{$authorKey}.json";
        try {
            $response = $client->get($apiUrl);
        } catch (RequestException $e) {
            return null;
        }

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $bookDataAuthor = json_decode($response->getBody(), true);

        if (!isset($bookDataAuthor['name'])) {
            return null;
        }

        $cover_image = isset($bookDataIsbn['covers'][0]) ?
            "https://covers.openlibrary.org/b/id/{$bookDataIsbn['covers'][0]}-L.jpg" : null;

        if ($cover_image === null) {
            $cover_image = 'default.jpg';
        }

        return [
            'title' => $bookDataIsbn['title'],
            'author' => $bookDataAuthor['name'],
            'description' => $description,
            'page_number' => $bookDataIsbn['number_of_pages'],
            'cover_image' => $cover_image,
        ];
    }



}