<?php

namespace App\Controller;

use App\Entity\MavenRepositoryGroup;
use App\Security\SecurityService;
use App\Service\MavenRepositoryGroupService;
use Dontdrinkandroot\Path\DirectoryPath;
use Dontdrinkandroot\Path\FilePath;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MavenRepositoryGroupController extends AbstractController
{
    public function __construct(
        private readonly MavenRepositoryGroupService $mavenRepositoryGroupService,
        private readonly SecurityService $securityService,
        private readonly LoggerInterface $logger
    ) {
    }

    public function directoryIndex(MavenRepositoryGroup $mavenRepositoryGroup, DirectoryPath $path): Response
    {
        if (!$this->mavenRepositoryGroupService->readGranted(
            $mavenRepositoryGroup,
            $this->securityService->findCurrentUser()
        )) {
            throw new AccessDeniedException();
        }

        $directories = $this->mavenRepositoryGroupService->listDirectories($mavenRepositoryGroup, $path);
        $files = $this->mavenRepositoryGroupService->listFiles($mavenRepositoryGroup, $path);

        return $this->render(
            'RepositoryGroup/directory.html.twig',
            [
                'mavenRepositoryGroup' => $mavenRepositoryGroup,
                'path'                 => $path,
                'files'                => $files,
                'directories'          => $directories
            ]
        );
    }

    public function download(MavenRepositoryGroup $mavenRepositoryGroup, FilePath $path): Response
    {
        if (!$this->mavenRepositoryGroupService->readGranted(
            $mavenRepositoryGroup,
            $this->securityService->findCurrentUser()
        )) {
            throw new AccessDeniedException();
        }

        $this->logger->info(
            sprintf('Download, repo: %s, path: %s', $mavenRepositoryGroup->shortName, (string)$path)
        );

        $filename = $this->mavenRepositoryGroupService->getFilename($mavenRepositoryGroup, $path);

        if (null === $filename) {
            throw new NotFoundHttpException();
        }

        return new BinaryFileResponse($filename);
    }
}
