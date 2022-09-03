<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class MavenRepository implements Stringable
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
     * @ORM\JoinTable(name="repository_read_users")
     *
     * @var Collection<array-key,User>
     */
    private Collection $readUsers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     * @ORM\JoinTable(name="repository_write_users")
     *
     * @var Collection<array-key,User>
     */
    private Collection $writeUsers;

    public function __construct()
    {
        $this->readUsers = new ArrayCollection();
        $this->writeUsers = new ArrayCollection();
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

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): void
    {
        $this->visible = $visible;
    }

    public function isNew()
    {
        return !isset($this->id);
    }

    /**
     * @return Collection<array-key,User>
     */
    public function getReadUsers(): Collection
    {
        return $this->readUsers;
    }

    /**
     * @return Collection<array-key,User>
     */
    public function getWriteUsers(): Collection
    {
        return $this->writeUsers;
    }

    public function addReadUser(User $user)
    {
        $this->readUsers->add($user);
    }

    public function addWriteUser(User $user)
    {
        $this->writeUsers->add($user);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->getName() ?? 'n/a';
    }
}
