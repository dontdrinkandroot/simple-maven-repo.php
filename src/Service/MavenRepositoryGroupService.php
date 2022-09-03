<?php

namespace App\Service;

use App\Entity\MavenRepositoryGroup;
use App\Entity\User;
use App\Repository\MavenRepositoryGroupRepository;
use Dontdrinkandroot\Path\DirectoryPath;
use Dontdrinkandroot\Path\FilePath;

class MavenRepositoryGroupService
{
    public function __construct(
        private readonly MavenRepositoryGroupRepository $mavenRepositoryGroupRepository,
        private readonly MavenRepositoryService $mavenRepositoryService
    ) {
    }

    public function readGranted(
        MavenRepositoryGroup $mavenRepositoryGroup,
        ?User $user
    ): bool {
        if (
            !$mavenRepositoryGroup->visible
            && (null === $user || !$mavenRepositoryGroup->readUsers->contains($user))
        ) {
            return false;
        }

        foreach ($mavenRepositoryGroup->mavenRepositories as $mavenRepository) {
            if (
                !$mavenRepository->visible
                && (null === $user || !$mavenRepository->readUsers->contains($user))
            ) {
                return false;
            }
        }

        return true;
    }

    public function listDirectories(
        MavenRepositoryGroup $mavenRepositoryGroup,
        DirectoryPath $path
    ): array {
        $directories = [];
        foreach ($mavenRepositoryGroup->mavenRepositories as $mavenRepository) {
            if ($this->mavenRepositoryService->hasDirectory($mavenRepository, $path)) {
                foreach ($this->mavenRepositoryService->listDirectories($mavenRepository, $path) as $directory) {
                    if (!in_array($directory, $directories, true)) {
                        $directories[] = $directory;
                    }
                }
            }
        }

        return $directories;
    }

    public function listFiles(
        MavenRepositoryGroup $mavenRepositoryGroup,
        DirectoryPath $path
    ): array {
        $files = [];
        foreach ($mavenRepositoryGroup->mavenRepositories as $mavenRepository) {
            if ($this->mavenRepositoryService->hasDirectory($mavenRepository, $path)) {
                foreach ($this->mavenRepositoryService->listFiles($mavenRepository, $path) as $file) {
                    if (!in_array($file, $files)) {
                        $files[] = $file;
                    }
                }
            }
        }

        return $files;
    }

    public function getFilename(
        MavenRepositoryGroup $mavenRepositoryGroup,
        FilePath $path
    ): ?string {
        foreach ($mavenRepositoryGroup->mavenRepositories as $mavenRepository) {
            if ($this->mavenRepositoryService->hasFile($mavenRepository, $path)) {
                return $this->mavenRepositoryService->getFilename($mavenRepository, $path);
            }
        }

        return null;
    }

    public function listReadableGroups(?User $user): array
    {
        return $this->mavenRepositoryGroupRepository->listReadableGroups($user);
    }
}
