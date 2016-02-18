<?php

namespace VIB\ImapUserBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use VIB\ImapUserBundle\User\ImapUserInterface;

class ImapUserEvent extends Event
{
    private $user;

    public function __construct(ImapUserInterface $user)
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
