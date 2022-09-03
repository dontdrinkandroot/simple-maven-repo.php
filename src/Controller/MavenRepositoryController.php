<?php

namespace App\Controller;

use App\Entity\MavenRepository;
use App\Security\SecurityService;
use App\Service\MavenRepositoryService;
use Dontdrinkandroot\Path\DirectoryPath;
use Dontdrinkandroot\Path\FilePath;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MavenRepositoryController extends AbstractController
{
    public function __construct(
        private readonly MavenRepositoryService $mavenRepositoryService,
        private readonly SecurityService $securityService,
        private readonly LoggerInterface $logger
    ) {
    }

    public function directoryIndex(MavenRepository $mavenRepository, DirectoryPath $path): Response
    {
        if (!$this->mavenRepositoryService->readGranted($mavenRepository, $this->securityService->findCurrentUser())) {
            throw new AccessDeniedException();
        }

        $directories = $this->mavenRepositoryService->listDirectories($mavenRepository, $path);
        $files = $this->mavenRepositoryService->listFiles($mavenRepository, $path);

        return $this->render(
            'Repository/directory.html.twig',
            [
                'mavenRepository' => $mavenRepository,
                'path'            => $path,
                'files'           => $files,
                'directories'     => $directories
            ]
        );
    }

    public function download(MavenRepository $mavenRepository, FilePath $path): Response
    {
        if (!$this->mavenRepositoryService->readGranted($mavenRepository, $this->securityService->findCurrentUser())) {
            throw new AccessDeniedException();
        }

        $this->logger->info(sprintf('Download, repo: %s, path: %s', $mavenRepository->getShortName(), (string)$path));

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
            sprintf('Upload, repo: %s, path: %s', $mavenRepository->getShortName(), (string)$path)
        );

        $this->mavenRepositoryService->storeFile($mavenRepository, $path, $request->getContent(true));

        return new Response(null, Response::HTTP_CREATED);
    }
}
