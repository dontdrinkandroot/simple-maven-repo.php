<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser;

#[ORM\Entity]
class User extends BaseUser
{
    /** @var int */
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;
}
