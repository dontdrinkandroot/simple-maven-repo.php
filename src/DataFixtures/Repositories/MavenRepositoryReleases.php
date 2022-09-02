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

class MavenRepositoryReleases extends Fixture implements DependentFixtureInterface
{
    const REFERENCE = 'maven-repository-releases';

    /**
     * @var MavenRepositoryService
     */
    private $mavenRepositoryService;

    public function __construct(MavenRepositoryService $mavenRepositoryService)
    {
        $this->mavenRepositoryService = $mavenRepositoryService;
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
        $mavenRepository = new MavenRepository();
        $mavenRepository->setShortName('releases');
        $mavenRepository->setName('Releases');
        $mavenRepository->setVisible(true);

        /** @var User $userRW */
        $userRW = $this->getReference(UserReadWrite::REFERENCE);
        $mavenRepository->addWriteUser($userRW);

        $manager->persist($mavenRepository);
        $manager->flush();

        $this->addReference(self::REFERENCE, $mavenRepository);

        $this->mavenRepositoryService->storeFile(
            $mavenRepository,
            FilePath::parse('/artifact1/0.1-release'),
            'releases'
        );
    }
}
