<?php

namespace App\Service;

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;

class NoopMailer implements MailerInterface
{
    /**
     * @inheritDoc
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        /* Noop */
    }

    /**
     * @inheritDoc
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        /* Noop */
    }
}
