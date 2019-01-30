<?php

namespace App\Controller;

use App\Entity\MavenRepository;
use App\Entity\User;
use App\Service\MavenRepositoryService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class MavenRepositoryController
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    private $filesystem;

    /**
     * @var MavenRepositoryService
     */
    private $mavenRepositoryService;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        MavenRepositoryService $mavenRepositoryService,
        TokenStorageInterface $tokenStorage,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->filesystem = new Filesystem();
        $this->mavenRepositoryService = $mavenRepositoryService;
        $this->tokenStorage = $tokenStorage;
    }

    public function download(MavenRepository $mavenRepository, string $path)
    {
        if (!$this->mavenRepositoryService->readGranted($mavenRepository, $this->findCurrentUser())) {
            throw new AccessDeniedException();
        }

        $this->logger->info(sprintf('Download, repo: %s, path: %s', $mavenRepository->getShortName(), $path));

        if (!$this->mavenRepositoryService->hasFile($mavenRepository, $path)) {
            throw new NotFoundHttpException();
        }

        return new BinaryFileResponse($this->mavenRepositoryService->getFilename($mavenRepository, $path));
    }

    public function upload(Request $request, MavenRepository $mavenRepository, string $path)
    {
        if (!$this->mavenRepositoryService->writeGranted($mavenRepository, $this->fetchCurrentUser())) {
            throw new AccessDeniedException();
        }

        $this->logger->info(
            sprintf('Upload, repo: %s, path: %s', $mavenRepository->getShortName(), $path)
        );

        $this->mavenRepositoryService->storeFile($mavenRepository, $path, $request->getContent(true));

        return new Response(null, Response::HTTP_CREATED);
    }

    private function startsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return (substr($haystack, 0, $length) === $needle);
    }

    private function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    private function findCurrentUser(): ?User
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return null;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }

    private function fetchCurrentUser(): User
    {
        $user = $this->findCurrentUser();
        if (null === $user) {
            throw new AccessDeniedException();
        }

        return $user;
    }
}
