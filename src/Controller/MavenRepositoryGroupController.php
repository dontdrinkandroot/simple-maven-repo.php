<?php

namespace App\Controller;

use App\Entity\MavenRepositoryGroup;
use App\Security\CurrentUserTrait;
use App\Service\MavenRepositoryGroupService;
use Dontdrinkandroot\Path\DirectoryPath;
use Dontdrinkandroot\Path\FilePath;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class MavenRepositoryGroupController
{
    use CurrentUserTrait;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $filesystem;

    /**
     * @var MavenRepositoryGroupService
     */
    private $mavenRepositoryGroupService;

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
        MavenRepositoryGroupService $mavenRepositoryGroupService,
        TokenStorageInterface $tokenStorage,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->filesystem = new Filesystem();
        $this->mavenRepositoryGroupService = $mavenRepositoryGroupService;
        $this->tokenStorage = $tokenStorage;
        $this->templateEngine = $templateEngine;
    }

    public function directoryIndex(MavenRepositoryGroup $mavenRepositoryGroup, DirectoryPath $path)
    {
        if (!$this->mavenRepositoryGroupService->readGranted($mavenRepositoryGroup, $this->findCurrentUser())) {
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

    public function download(MavenRepositoryGroup $mavenRepositoryGroup, FilePath $path)
    {
        if (!$this->mavenRepositoryGroupService->readGranted($mavenRepositoryGroup, $this->findCurrentUser())) {
            throw new AccessDeniedException();
        }

        $this->logger->info(sprintf('Download, repo: %s, path: %s', $mavenRepositoryGroup->getShortName(), $path));

        $filename = $this->mavenRepositoryGroupService->getFilename($mavenRepositoryGroup, $path);

        if (null === $filename) {
            throw new NotFoundHttpException();
        }

        return new BinaryFileResponse($filename);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenStorage(): TokenStorageInterface
    {
        return $this->tokenStorage;
    }
}
