<?php

namespace App\DataFixtures\Users;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class UserAdmin extends Fixture
{
    const REFERENCE = 'user-admin';

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = $this->userManager->createUser();
        $user->setUsername('admin');
        $user->setEmail($user->getUsername() . '@example.com');
        $user->setPlainPassword($user->getUsername());
        $user->setEnabled(true);
        $user->addRole('ROLE_SUPER_ADMIN');
        $this->userManager->updateUser($user);

        $this->addReference(self::REFERENCE, $user);
    }
}
