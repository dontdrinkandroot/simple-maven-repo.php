<?php

namespace App\Repository;

use App\Entity\MavenRepositoryGroup;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MavenRepositoryGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MavenRepositoryGroup::class);
    }

    public function listReadableGroups(?User $user = null): array
    {
        $queryBuilder = $this->createQueryBuilder('mvnRepositoryGroup');
        $queryBuilder->where('mvnRepositoryGroup.visible = true');

        if (null !== $user) {
            $queryBuilder->join('mvnRepositoryGroup.readUsers', 'readUsers');
            $queryBuilder->orWhere('readUsers = :user');
            $queryBuilder->setParameter('user', $user);
        }

        $queryBuilder->orderBy('mvnRepositoryGroup.shortName', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }
}
