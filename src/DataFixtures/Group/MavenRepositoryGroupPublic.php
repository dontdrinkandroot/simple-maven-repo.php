<?php

namespace App\DataFixtures\Group;

use App\DataFixtures\Repositories\MavenRepositoryReleases;
use App\DataFixtures\Repositories\MavenRepositorySnapshots;
use App\Entity\MavenRepository;
use App\Entity\MavenRepositoryGroup;
use App\Service\MavenRepositoryService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class MavenRepositoryGroupPublic extends Fixture implements DependentFixtureInterface
{
    const REFERENCE = 'maven-repository-group-public';

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
        return [MavenRepositoryReleases::class, MavenRepositorySnapshots::class];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $mavenRepositoryGroup = new MavenRepositoryGroup();
        $mavenRepositoryGroup->setShortName('public');
        $mavenRepositoryGroup->setName('Public');
        $mavenRepositoryGroup->setVisible(true);

        /** @var MavenRepository $releases */
        $releases = $this->getReference(MavenRepositoryReleases::REFERENCE);
        /** @var MavenRepository $snapshots */
        $snapshots = $this->getReference(MavenRepositorySnapshots::REFERENCE);

        $mavenRepositoryGroup->addMavenRepository($releases);
        $mavenRepositoryGroup->addMavenRepository($snapshots);

        $manager->persist($mavenRepositoryGroup);
        $manager->flush();

        $this->addReference(self::REFERENCE, $mavenRepositoryGroup);
    }
}
