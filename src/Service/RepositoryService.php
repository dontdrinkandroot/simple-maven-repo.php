<?php

namespace App\Service;

use App\Repository\RepositoryRepository;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class RepositoryService
{
    /**
     * @var RepositoryRepository
     */
    private $repositoryRepository;

    /**
     * @var string
     */
    private $storageRoot;

    public function __construct(RepositoryRepository $repositoryRepository, string $storageRoot)
    {
        $this->repositoryRepository = $repositoryRepository;
        $this->storageRoot = $storageRoot;
    }
}
