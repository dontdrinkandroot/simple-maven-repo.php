<?php

namespace App\Service;

use App\Entity\MavenRepository;
use App\Entity\User;
use App\Repository\MavenRepositoryRepository;
use Dontdrinkandroot\Path\DirectoryPath;
use Dontdrinkandroot\Path\FilePath;
use Dontdrinkandroot\Path\Path;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class MavenRepositoryService
{
    private readonly Filesystem $filesystem;

    public function __construct(
        private readonly MavenRepositoryRepository $mavenRepositoryRepository,
        private readonly string $storageRoot
    ) {
        $this->filesystem = new Filesystem();
    }

    /**
     * @param string|resource $resource
     */
    public function storeFile(MavenRepository $mavenRepository, FilePath $path, $resource): void
    {
        $fileName = $this->getFilename($mavenRepository, $path);
        $directory = dirname($fileName);
        if (!$this->filesystem->exists($directory)) {
            $this->filesystem->mkdir($directory);
        }

        file_put_contents($fileName, $resource);
    }

    public function hasFile(MavenRepository $mavenRepository, FilePath $path): bool
    {
        $filename = $this->getFilename($mavenRepository, $path);

        return $this->filesystem->exists($filename);
    }

    public function hasDirectory(MavenRepository $mavenRepository, DirectoryPath $path): bool
    {
        $filename = $this->getFilename($mavenRepository, $path);

        return $this->filesystem->exists($filename);
    }

    public function getFilename(MavenRepository $mavenRepository, Path $path): string
    {
        return $this->storageRoot . '/' . $mavenRepository->shortName . '/' . $path->toRelativeFileSystemString();
    }

    public function readGranted(MavenRepository $mavenRepository, ?User $user = null): bool
    {
        if ($mavenRepository->visible) {
            return true;
        }

        return null !== $user && $mavenRepository->readUsers->contains($user);
    }

    public function writeGranted(MavenRepository $mavenRepository, User $user): bool
    {
        return $mavenRepository->writeUsers->contains($user);
    }

    public function listReadableRepositories(?User $user = null): array
    {
        return $this->mavenRepositoryRepository->listReadableRepositories($user);
    }

    /**
     * @return iterable<string, SplFileInfo>
     */
    public function listDirectories(MavenRepository $mavenRepository, DirectoryPath $path): iterable
    {
        $directory = $this->getFilename($mavenRepository, $path);
        $finder = new Finder();

        return $finder->in($directory)->directories()->depth(0);
    }

    /**
     * @return iterable<string, SplFileInfo>
     */
    public function listFiles(MavenRepository $mavenRepository, DirectoryPath $path): iterable
    {
        $directory = $this->getFilename($mavenRepository, $path);
        $finder = new Finder();

        return $finder->in($directory)->files()->depth(0);
    }
}
