<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SecurityService
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public function findCurrentUser(): ?User
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return null;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }

    public function fetchCurrentUser(): User
    {
        $user = $this->findCurrentUser();
        if (null === $user) {
            throw new AccessDeniedException();
        }

        return $user;
    }
}
