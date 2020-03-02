<?php

namespace App\Controller;

use App\Entity\MavenRepositoryGroup;
use App\Security\SecurityService;
use App\Service\MavenRepositoryGroupService;
use Dontdrinkandroot\Path\DirectoryPath;
use Dontdrinkandroot\Path\FilePath;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class MavenRepositoryGroupController
{
    private LoggerInterface $logger;

    private Filesystem $filesystem;

    private MavenRepositoryGroupService $mavenRepositoryGroupService;

    private EngineInterface $templateEngine;

    private SecurityService $securityService;

    public function __construct(
        EngineInterface $templateEngine,
        MavenRepositoryGroupService $mavenRepositoryGroupService,
        SecurityService $securityService,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->filesystem = new Filesystem();
        $this->mavenRepositoryGroupService = $mavenRepositoryGroupService;
        $this->templateEngine = $templateEngine;
        $this->securityService = $securityService;
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

        return new Response(
            $this->templateEngine->render(
                'RepositoryGroup/directory.html.twig',
                [
                    'mavenRepositoryGroup' => $mavenRepositoryGroup,
                    'path'                 => $path,
                    'files'                => $files,
                    'directories'          => $directories
                ]
            )
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

        $this->logger->info(sprintf('Download, repo: %s, path: %s', $mavenRepositoryGroup->getShortName(), $path));

        $filename = $this->mavenRepositoryGroupService->getFilename($mavenRepositoryGroup, $path);

        if (null === $filename) {
            throw new NotFoundHttpException();
        }

        return new BinaryFileResponse($filename);
    }
}
