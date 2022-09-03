<?php

namespace App\DataFixtures\Users;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Dontdrinkandroot\Common\Asserted;
use Sonata\UserBundle\Entity\UserManager;

class UserAdmin extends Fixture
{
    final const REFERENCE = 'user-admin';

    public function __construct(private readonly UserManager $userManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = Asserted::instanceOf($this->userManager->create(), User::class);
        $user->setUsername('admin');
        $user->setEmail($user->getUsername() . '@example.com');
        $user->setPlainPassword($user->getUsername());
        $user->setEnabled(true);
        $user->addRole('ROLE_SUPER_ADMIN');
        $this->userManager->save($user);

        $this->addReference(self::REFERENCE, $user);
    }
}
