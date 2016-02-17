<?php

namespace VIB\ImapUserBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Symfony\Component\Security\Core\User\UserInterface;

use VIB\ImapUserBundle\User\LdapUserInterface;

class LdapUserEvent extends Event
{
    private $user;

    public function __construct(LdapUserInterface $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
