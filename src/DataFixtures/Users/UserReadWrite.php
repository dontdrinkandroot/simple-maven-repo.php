<?php

namespace App\DataFixtures\Users;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Dontdrinkandroot\Common\Asserted;
use Sonata\UserBundle\Entity\UserManager;

class UserReadWrite extends Fixture
{
    final const REFERENCE = 'user-read-write';
    final const USERNAME = 'userreadwrite';

    public function __construct(private readonly UserManager $userManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = Asserted::instanceOf($this->userManager->create(), User::class);
        $user->setUsername(self::USERNAME);
        $user->setEmail($user->getUsername() . '@example.com');
        $user->setPlainPassword($user->getUsername());
        $user->setEnabled(true);
        $this->userManager->save($user);

        $this->addReference(self::REFERENCE, $user);
    }
}
