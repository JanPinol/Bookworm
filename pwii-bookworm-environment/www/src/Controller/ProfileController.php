<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller;

use Psr\Http\Message\UploadedFileInterface;
use Slim\Views\Twig;
use Project\Bookworm\Model\Repository\MysqlUserRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class ProfileController
{
    private const UPLOADS_DIR = __DIR__ . '/../../public/uploads';


    private const UNEXPECTED_ERROR = "An unexpected error occurred uploading the file '%s'...";

    private const INVALID_EXTENSION_ERROR = "The received file extension '%s' is not valid";

    private const ALLOWED_EXTENSIONS = ['jpg', 'png', 'pdf'];
    private MysqlUserRepository $userRepository;
    private Twig $twig;

    public function __construct(Twig $twig, MysqlUserRepository $userRepository)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }


    public function showFileFormAction(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'profile.twig', []);
    }

    public function uploadFileAction(Request $request, Response $response): Response
    {
        $uploadedFiles = $request->getUploadedFiles();

        $errors = [];

        /** @var UploadedFileInterface $uploadedFile */
        foreach ($uploadedFiles['files'] as $uploadedFile) {
            if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                $errors[] = sprintf(
                    self::UNEXPECTED_ERROR,
                    $uploadedFile->getClientFilename()
                );
                continue;
            }

            $name = $uploadedFile->getClientFilename();

            $fileInfo = pathinfo($name);

            $format = $fileInfo['extension'];

            if (!$this->isValidFormat($format)) {
                $errors[] = sprintf(self::INVALID_EXTENSION_ERROR, $format);
                continue;
            }
            $uploadedFile->moveTo(self::UPLOADS_DIR . DIRECTORY_SEPARATOR . $name);
        }

        return $this->twig->render($response, 'profile.twig', [
            'errors' => $errors,
        ]);
    }
    public function showProfile(Request $request, Response $response): Response
    {
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId === null) {
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }

        $user = $this->userRepository->findById($userId);

    
        return $this->twig->render($response, 'profile.twig', ['user' => $user]);
    }

    public function updateProfile(Request $request, Response $response): Response
    {
        $userId = $_SESSION['user_id'] ?? null;
    
        if ($userId === null) {
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }
    
        $user = $this->userRepository->findById($userId);
        $data = $request->getParsedBody();
        $uploadedFile = $request->getUploadedFiles()['profile-picture'] ?? null;
        $errors = [];
    
        if (!empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'The email address is not valid.';
            } elseif ($data['email'] !== $user->email() && $this->userRepository->findByEmail($data['email'], $userId)) {
                $errors[] = 'The email address is already registered.';
            }
        }
    
        if (!empty($data['username'])) {
            $trimmedUsername = trim($data['username']);
            if (empty($trimmedUsername)) {
                $errors[] = 'Username cannot be empty.';
            } elseif ($trimmedUsername !== $user->username()) {
                $existingUser = $this->userRepository->findByUsername($trimmedUsername);
                if ($existingUser !== null && $existingUser->id() !== $user->id()) {
                    $errors[] = 'Username already exists. Please choose a different one.';
                }
            }
        }
    
        if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {
            $this->processProfilePictureUpload($uploadedFile, $user, $errors);
        }
    
    
        if (!empty($errors)) {
            return $this->twig->render($response, 'profile.twig', [
                'user' => $user,
                'errors' => $errors
            ]);
        }
    
        if (!empty($data['email'])) {
            $user->setEmail($data['email']);
        }
    
        if (!empty($data['username'])) {
            $trimmedUsername = trim($data['username']);
            $user->setUsername($trimmedUsername);
        }
    
        if (!empty($data['password'])) {
            $user->setPassword($data['password']);
        }

        $this->userRepository->update($user);
        return $response->withHeader('Location', '/profile')->withStatus(302);
    }


    
    private function isValidFormat($format)
    {
        $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
        return in_array(strtolower($format), $allowedFormats);
    }

private function processProfilePictureUpload($uploadedFile, $user, &$errors)
{
    if ($uploadedFile->getError() === UPLOAD_ERR_NO_FILE) {
        return;
    }

    $destinationDirectory = dirname(__DIR__, 2) . '/public/uploads/';
    $filename = uniqid("", true) . "." . pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    if ($uploadedFile->getSize() > 1048576) {
        $errors[] = 'The image size must be less than 1MB.';
        return;
    }

    $allowedMimeTypes = ['image/png', 'image/jpeg', 'image/gif', 'image/svg+xml'];
    if (!in_array($uploadedFile->getClientMediaType(), $allowedMimeTypes)) {
        $errors[] = 'Only PNG, JPG, GIF and SVG images are allowed.';
        return;
    }

    $imageInfo = getimagesize($uploadedFile->getStream()->getMetadata('uri'));
    if ($imageInfo[0] > 400 || $imageInfo[1] > 400) {
        $errors[] = 'The image dimensions must be 400x400 pixels or less.';
        return;
    }

    if (empty($errors)) {
        $uploadedFile->moveTo($destinationDirectory . $filename);
        $user->setProfilePicture($filename);
        $this->userRepository->updateProfilePicture($user->id(), $filename);
    }
}

}