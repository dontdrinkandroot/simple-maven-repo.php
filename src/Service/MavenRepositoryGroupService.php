<?php

namespace App\Service;

use App\Entity\MavenRepositoryGroup;
use App\Entity\User;
use App\Repository\MavenRepositoryGroupRepository;
use Dontdrinkandroot\Path\DirectoryPath;
use Dontdrinkandroot\Path\FilePath;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class MavenRepositoryGroupService
{
    /**
     * @var MavenRepositoryService
     */
    private $mavenRepositoryService;

    /**
     * @var MavenRepositoryGroupRepository
     */
    private $mavenRepositoryGroupRepository;

    public function __construct(
        MavenRepositoryGroupRepository $mavenRepositoryGroupRepository,
        MavenRepositoryService $mavenRepositoryService
    ) {
        $this->mavenRepositoryService = $mavenRepositoryService;
        $this->mavenRepositoryGroupRepository = $mavenRepositoryGroupRepository;
    }

    public function readGranted(
        MavenRepositoryGroup $mavenRepositoryGroup,
        ?User $user
    ): bool {
        if (!$mavenRepositoryGroup->isVisible() && !$mavenRepositoryGroup->getReadUsers()->contains($user)) {
            return false;
        }

        foreach ($mavenRepositoryGroup->getMavenRepositories() as $mavenRepository) {
            if (!$mavenRepository->isVisible() && !$mavenRepository->getReadUsers()->contains($user)) {
                return false;
            }
        }

        return true;
    }

    public function listDirectories(
        MavenRepositoryGroup $mavenRepositoryGroup,
        DirectoryPath $path
    ) {
        $directories = [];
        foreach ($mavenRepositoryGroup->getMavenRepositories() as $mavenRepository) {
            foreach ($this->mavenRepositoryService->listDirectories($mavenRepository, $path) as $directory) {
                if (!in_array($directory, $directories)) {
                    $directories[] = $directory;
                }
            }
        }

        return $directories;
    }

    public function listFiles(
        MavenRepositoryGroup $mavenRepositoryGroup,
        DirectoryPath $path
    ) {
        $files = [];
        foreach ($mavenRepositoryGroup->getMavenRepositories() as $mavenRepository) {
            foreach ($this->mavenRepositoryService->listFiles($mavenRepository, $path) as $file) {
                if (!in_array($file, $files)) {
                    $files[] = $file;
                }
            }
        }

        return $files;
    }

    public function getFilename(
        MavenRepositoryGroup $mavenRepositoryGroup,
        FilePath $path
    ): string {
        foreach ($mavenRepositoryGroup->getMavenRepositories() as $mavenRepository) {
            if ($this->mavenRepositoryService->hasFile($mavenRepository, $path)) {
                return $this->mavenRepositoryService->getFilename($mavenRepository, $path);
            }
        }

        return null;
    }

    public function listReadableGroups(?User $user)
    {
        return $this->mavenRepositoryGroupRepository->listReadableGroups($user);
    }
}