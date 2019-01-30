<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
trait CurrentUserTrait
{
    protected function findCurrentUser(): ?User
    {
        $token = $this->getTokenStorage()->getToken();
        if (null === $token) {
            return null;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }

    protected function fetchCurrentUser(): User
    {
        $user = $this->findCurrentUser();
        if (null === $user) {
            throw new AccessDeniedException();
        }

        return $user;
    }

    protected abstract function getTokenStorage(): TokenStorageInterface;
}
