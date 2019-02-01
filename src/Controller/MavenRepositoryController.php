<?php

namespace App\Controller;

use App\Entity\MavenRepository;
use App\Security\CurrentUserTrait;
use App\Service\MavenRepositoryService;
use Dontdrinkandroot\Path\DirectoryPath;
use Dontdrinkandroot\Path\FilePath;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class MavenRepositoryController
{
    use CurrentUserTrait;

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

    /**
     * @var EngineInterface
     */
    private $templateEngine;

    public function __construct(
        EngineInterface $templateEngine,
        MavenRepositoryService $mavenRepositoryService,
        TokenStorageInterface $tokenStorage,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->filesystem = new Filesystem();
        $this->mavenRepositoryService = $mavenRepositoryService;
        $this->tokenStorage = $tokenStorage;
        $this->templateEngine = $templateEngine;
    }

    public function directoryIndex(MavenRepository $mavenRepository, DirectoryPath $path)
    {
        if (!$this->mavenRepositoryService->readGranted($mavenRepository, $this->findCurrentUser())) {
            throw new AccessDeniedException();
        }

        $directories = $this->mavenRepositoryService->listDirectories($mavenRepository, $path);
        $files = $this->mavenRepositoryService->listFiles($mavenRepository, $path);

        return new Response(
            $this->templateEngine->render(
                'directory.html.twig',
                [
                    'mavenRepository' => $mavenRepository,
                    'path'            => $path,
                    'files'           => $files,
                    'directories'     => $directories
                ]
            )
        );
    }

    public function download(MavenRepository $mavenRepository, FilePath $path)
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

    public function upload(Request $request, MavenRepository $mavenRepository, FilePath $path)
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

    /**
     * {@inheritdoc}
     */
    protected function getTokenStorage(): TokenStorageInterface
    {
        return $this->tokenStorage;
    }
}
