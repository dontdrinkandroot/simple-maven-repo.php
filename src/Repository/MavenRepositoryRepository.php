<?php

namespace App\Repository;

use App\Entity\MavenRepository;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MavenRepositoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MavenRepository::class);
    }

    public function listReadableRepositories(?User $user = null): array
    {
        $queryBuilder = $this->createQueryBuilder('mvnRepository');
        $queryBuilder->where('mvnRepository.visible = true');

        if (null !== $user) {
            $queryBuilder->join('mvnRepository.readUsers', 'readUsers');
            $queryBuilder->orWhere('readUsers = :user');
            $queryBuilder->setParameter('user', $user);
        }

        $queryBuilder->orderBy('mvnRepository.shortName', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }
}
