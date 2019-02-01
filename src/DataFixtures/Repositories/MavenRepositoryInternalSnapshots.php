<?php

namespace App\DataFixtures\Repositories;

use App\DataFixtures\Users\UserRead;
use App\DataFixtures\Users\UserReadWrite;
use App\Entity\MavenRepository;
use App\Entity\User;
use App\Service\MavenRepositoryService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dontdrinkandroot\Path\FilePath;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class MavenRepositoryInternalSnapshots extends Fixture implements DependentFixtureInterface
{
    const REFERENCE = 'maven-repository-internal-snapshots';

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
        return [UserRead::class, UserReadWrite::class];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $mavenRepository = new MavenRepository();
        $mavenRepository->setShortName('internalsnapshots');
        $mavenRepository->setName('Internal Snapshots');
        $mavenRepository->setVisible(false);

        /** @var User $userR */
        $userR = $this->getReference(UserRead::REFERENCE);
        $mavenRepository->addReadUser($userR);

        /** @var User $userRW */
        $userRW = $this->getReference(UserReadWrite::REFERENCE);
        $mavenRepository->addReadUser($userRW);
        $mavenRepository->addWriteUser($userRW);

        $manager->persist($mavenRepository);
        $manager->flush();

        $this->addReference(self::REFERENCE, $mavenRepository);

        $this->mavenRepositoryService->storeFile(
            $mavenRepository,
            FilePath::parse('/directory/file'),
            'internalsnapshots'
        );
    }
}
