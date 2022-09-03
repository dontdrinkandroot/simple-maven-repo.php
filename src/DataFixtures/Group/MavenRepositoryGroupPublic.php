<?php

namespace App\DataFixtures\Group;

use App\DataFixtures\Repositories\MavenRepositoryReleases;
use App\DataFixtures\Repositories\MavenRepositorySnapshots;
use App\Entity\MavenRepository;
use App\Entity\MavenRepositoryGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MavenRepositoryGroupPublic extends Fixture implements DependentFixtureInterface
{
    final const REFERENCE = 'maven-repository-group-public';

    public function __construct()
    {
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
        $mavenRepositoryGroup = new MavenRepositoryGroup(
            shortName: 'public',
            name: 'Public',
            visible: true
        );

        /** @var MavenRepository $releases */
        $releases = $this->getReference(MavenRepositoryReleases::REFERENCE);
        /** @var MavenRepository $snapshots */
        $snapshots = $this->getReference(MavenRepositorySnapshots::REFERENCE);

        $mavenRepositoryGroup->mavenRepositories->add($releases);
        $mavenRepositoryGroup->mavenRepositories->add($snapshots);

        $manager->persist($mavenRepositoryGroup);
        $manager->flush();

        $this->addReference(self::REFERENCE, $mavenRepositoryGroup);
    }
}
