<?php

namespace App\Repository;

use App\Entity\MavenRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class MavenRepositoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MavenRepository::class);
    }
}
