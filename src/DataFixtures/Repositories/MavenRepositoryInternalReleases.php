<?php

namespace App\DataFixtures\Repositories;

use App\DataFixtures\Users\UserRead;
use App\DataFixtures\Users\UserReadWrite;
use App\Entity\MavenRepository;
use App\Entity\User;
use App\Service\MavenRepositoryService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Dontdrinkandroot\Path\FilePath;

class MavenRepositoryInternalReleases extends Fixture implements DependentFixtureInterface
{
    final const REFERENCE = 'maven-repository-internal-releases';

    public function __construct(private readonly MavenRepositoryService $mavenRepositoryService)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [UserRead::class, UserReadWrite::class];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $mavenRepository = new MavenRepository(
            shortName: 'internalreleases',
            name: 'Internal Releases',
            visible: false,
        );

        /** @var User $userR */
        $userR = $this->getReference(UserRead::REFERENCE);
        $mavenRepository->readUsers->add($userR);

        /** @var User $userRW */
        $userRW = $this->getReference(UserReadWrite::REFERENCE);
        $mavenRepository->readUsers->add($userRW);
        $mavenRepository->writeUsers->add($userRW);

        $manager->persist($mavenRepository);
        $manager->flush();

        $this->addReference(self::REFERENCE, $mavenRepository);

        $this->mavenRepositoryService->storeFile(
            $mavenRepository,
            FilePath::parse('/artifact1/0.1-release'),
            'internalreleases'
        );
    }
}
