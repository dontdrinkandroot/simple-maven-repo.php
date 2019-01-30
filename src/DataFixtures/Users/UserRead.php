<?php

namespace App\DataFixtures\Users;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class UserRead extends Fixture
{
    const REFERENCE = 'user-read';

    const USERNAME = 'userread';

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
        $user->setUsername(self::USERNAME);
        $user->setEmail($user->getUsername() . '@example.com');
        $user->setPlainPassword($user->getUsername());
        $user->setEnabled(true);
        $this->userManager->updateUser($user);

        $this->addReference(self::REFERENCE, $user);
    }
}
