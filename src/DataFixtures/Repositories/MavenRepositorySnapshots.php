<?php

namespace App\DataFixtures\Repositories;

use App\DataFixtures\Users\UserReadWrite;
use App\Entity\MavenRepository;
use App\Entity\User;
use App\Service\MavenRepositoryService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Dontdrinkandroot\Path\FilePath;

class MavenRepositorySnapshots extends Fixture implements DependentFixtureInterface
{
    final const REFERENCE = 'maven-repository-snapshots';

    public function __construct(private readonly MavenRepositoryService $mavenRepositoryService)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [UserReadWrite::class];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $mavenRepository = new MavenRepository(
            shortName: 'snapshots',
            name: 'Snapshots',
            visible: true,
        );

        /** @var User $userRW */
        $userRW = $this->getReference(UserReadWrite::REFERENCE);
        $mavenRepository->writeUsers->add($userRW);

        $manager->persist($mavenRepository);
        $manager->flush();

        $this->addReference(self::REFERENCE, $mavenRepository);

        $this->mavenRepositoryService->storeFile(
            $mavenRepository,
            FilePath::parse('/artifact1/0.1-snapshot'),
            'snapshots'
        );

        $this->mavenRepositoryService->storeFile(
            $mavenRepository,
            FilePath::parse('/artifact2/0.1-snapshot'),
            'snapshots'
        );
    }
}
