<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class MavenRepositoryGroup
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[a-z0-9_-]+$/",
     *     message="Only lowercase letters, numbers, dashes or underscores permitted"
     * )
     */
    private string $shortName;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private bool $visible = false;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     * @ORM\JoinTable(name="repository_group_read_users")
     *
     * @var Collection<array-key,User>
     */
    private Collection $readUsers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\MavenRepository")
     * @ORM\JoinTable(name="repository_group_repositories")
     *
     * @var Collection<array-key,MavenRepository>
     */
    private Collection $mavenRepositories;

    public function __construct()
    {
        $this->readUsers = new ArrayCollection();
        $this->mavenRepositories = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): void
    {
        $this->shortName = $shortName;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function isVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): void
    {
        $this->visible = $visible;
    }

    /**
     * @return Collection<array-key,User>
     */
    public function getReadUsers(): Collection
    {
        return $this->readUsers;
    }

    /**
     * @param Collection<array-key,User> $readUsers
     */
    public function setReadUsers(Collection $readUsers): void
    {
        $this->readUsers = $readUsers;
    }

    /**
     * @return Collection<array-key,MavenRepository>
     */
    public function getMavenRepositories(): Collection
    {
        return $this->mavenRepositories;
    }

    /**
     * @param Collection<array-key,MavenRepository> $mavenRepositories
     */
    public function setMavenRepositories(Collection $mavenRepositories): void
    {
        $this->mavenRepositories = $mavenRepositories;
    }

    public function isNew()
    {
        return !isset($this->id);
    }

    public function addReadUser(User $user)
    {
        $this->readUsers->add($user);
    }

    public function addMavenRepository(MavenRepository $mavenRepository)
    {
        $this->mavenRepositories->add($mavenRepository);
    }
}
