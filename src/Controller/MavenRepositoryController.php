<?php

namespace App\Controller;

use App\Entity\MavenRepository;
use App\Security\SecurityService;
use App\Service\MavenRepositoryService;
use Dontdrinkandroot\Path\DirectoryPath;
use Dontdrinkandroot\Path\FilePath;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class MavenRepositoryController
{
    private LoggerInterface $logger;

    private Filesystem $filesystem;

    private MavenRepositoryService $mavenRepositoryService;

    private EngineInterface $templateEngine;

    private SecurityService $securityService;

    public function __construct(
        EngineInterface $templateEngine,
        MavenRepositoryService $mavenRepositoryService,
        SecurityService $securityService,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->filesystem = new Filesystem();
        $this->mavenRepositoryService = $mavenRepositoryService;
        $this->templateEngine = $templateEngine;
        $this->securityService = $securityService;
    }

    public function directoryIndex(MavenRepository $mavenRepository, DirectoryPath $path): Response
    {
        if (!$this->mavenRepositoryService->readGranted($mavenRepository, $this->securityService->findCurrentUser())) {
            throw new AccessDeniedException();
        }

        $directories = $this->mavenRepositoryService->listDirectories($mavenRepository, $path);
        $files = $this->mavenRepositoryService->listFiles($mavenRepository, $path);

        return new Response(
            $this->templateEngine->render(
                'Repository/directory.html.twig',
                [
                    'mavenRepository' => $mavenRepository,
                    'path'            => $path,
                    'files'           => $files,
                    'directories'     => $directories
                ]
            )
        );
    }

    public function download(MavenRepository $mavenRepository, FilePath $path): Response
    {
        if (!$this->mavenRepositoryService->readGranted($mavenRepository, $this->securityService->findCurrentUser())) {
            throw new AccessDeniedException();
        }

        $this->logger->info(sprintf('Download, repo: %s, path: %s', $mavenRepository->getShortName(), $path));

        if (!$this->mavenRepositoryService->hasFile($mavenRepository, $path)) {
            throw new NotFoundHttpException();
        }

        return new BinaryFileResponse($this->mavenRepositoryService->getFilename($mavenRepository, $path));
    }

    public function upload(Request $request, MavenRepository $mavenRepository, FilePath $path): Response
    {
        if (!$this->mavenRepositoryService->writeGranted(
            $mavenRepository,
            $this->securityService->fetchCurrentUser()
        )) {
            throw new AccessDeniedException();
        }

        $this->logger->info(
            sprintf('Upload, repo: %s, path: %s', $mavenRepository->getShortName(), $path)
        );

        $this->mavenRepositoryService->storeFile($mavenRepository, $path, $request->getContent(true));

        return new Response(null, Response::HTTP_CREATED);
    }
}
