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

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class MavenRepositoryService
{
    /**
     * @var MavenRepositoryRepository
     */
    private $mavenRepositoryRepository;

    /**
     * @var string
     */
    private $storageRoot;

    private $filesystem;

    public function __construct(MavenRepositoryRepository $mavenRepositoryRepository, string $storageRoot)
    {
        $this->mavenRepositoryRepository = $mavenRepositoryRepository;
        $this->storageRoot = $storageRoot;
        $this->filesystem = new Filesystem();
    }

    public function storeFile(MavenRepository $mavenRepository, FilePath $path, $resource)
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

    public function getFilename(MavenRepository $mavenRepository, Path $path): string
    {
        return $this->storageRoot . '/' . $mavenRepository->getShortName() . '/' . $path->toRelativeFileSystemString();
    }

    public function readGranted(MavenRepository $mavenRepository, ?User $user = null): bool
    {
        if ($mavenRepository->isVisible()) {
            return true;
        }

        return $mavenRepository->getReadUsers()->contains($user);
    }

    public function writeGranted(MavenRepository $mavenRepository, User $user): bool
    {
        return $mavenRepository->getWriteUsers()->contains($user);
    }

    public function listReadableRepositories(?User $user = null): array
    {
        return $this->mavenRepositoryRepository->listReadableRepositories($user);
    }

    public function listDirectories(MavenRepository $mavenRepository, DirectoryPath $path)
    {
        $directory = $this->getFilename($mavenRepository, $path);
        $finder = new Finder();

        return $finder->in($directory)->directories()->depth(0);
    }

    public function listFiles(MavenRepository $mavenRepository, DirectoryPath $path)
    {
        $directory = $this->getFilename($mavenRepository, $path);
        $finder = new Finder();

        return $finder->in($directory)->files()->depth(0);
    }
}
