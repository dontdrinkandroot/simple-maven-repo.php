<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class MavenRepositoryGroup implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public readonly int $id;

    /** @var Collection<array-key,User> */
    #[ORM\JoinTable(name: 'repository_group_read_users')]
    #[ORM\ManyToMany(targetEntity: User::class)]
    public Collection $readUsers;

    /** @var Collection<array-key,MavenRepository> */
    #[ORM\JoinTable(name: 'repository_group_repositories')]
    #[ORM\ManyToMany(targetEntity: MavenRepository::class)]
    public Collection $mavenRepositories;

    public function __construct(
        #[ORM\Column(type: 'string', unique: true, nullable: false)]
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^[a-z0-9_-]+$/', message: 'Only lowercase letters, numbers, dashes or underscores permitted')]
        public string $shortName,

        #[ORM\Column(type: 'string', nullable: false)]
        #[Assert\NotBlank]
        public string $name,

        #[ORM\Column(type: 'boolean', nullable: false)]
        #[Assert\NotNull]
        public bool $visible = false
    ) {
        $this->readUsers = new ArrayCollection();
        $this->mavenRepositories = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
