<?php

namespace App\Service;

use App\Repository\MavenRepositoryRepository;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class MavenRepositoryService
{
    /**
     * @var MavenRepositoryRepository
     */
    private $repositoryRepository;

    /**
     * @var string
     */
    private $storageRoot;

    public function __construct(MavenRepositoryRepository $repositoryRepository, string $storageRoot)
    {
        $this->repositoryRepository = $repositoryRepository;
        $this->storageRoot = $storageRoot;
    }
}
