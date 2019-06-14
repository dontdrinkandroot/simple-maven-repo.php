<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 *
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class MavenRepositoryGroup
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[a-z0-9_-]+$/",
     *     message="Only lowercase letters, numbers, dashes or underscores permitted"
     * )
     *
     * @var string
     */
    private $shortName;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\NotNull()
     *
     * @var bool
     */
    private $visible = false;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     * @ORM\JoinTable(name="repository_group_read_users")
     *
     * @var User[]|Collection
     */
    private $readUsers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\MavenRepository")
     * @ORM\JoinTable(name="repository_group_repositories")
     *
     * @var MavenRepository[]|Collection
     */
    private $mavenRepositories;

    public function __construct()
    {
        $this->readUsers = new ArrayCollection();
        $this->mavenRepositories = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    /**
     * @param string $shortName
     */
    public function setShortName(string $shortName): void
    {
        $this->shortName = $shortName;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isVisible(): ?bool
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     */
    public function setVisible(bool $visible): void
    {
        $this->visible = $visible;
    }

    /**
     * @return User[]|Collection
     */
    public function getReadUsers()
    {
        return $this->readUsers;
    }

    /**
     * @param User[]|Collection $readUsers
     */
    public function setReadUsers($readUsers): void
    {
        $this->readUsers = $readUsers;
    }

    /**
     * @return MavenRepository[]|Collection
     */
    public function getMavenRepositories()
    {
        return $this->mavenRepositories;
    }

    /**
     * @param MavenRepository[]|Collection $mavenRepositories
     */
    public function setMavenRepositories($mavenRepositories): void
    {
        $this->mavenRepositories = $mavenRepositories;
    }

    public function isNew()
    {
        return null === $this->id;
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
