<?php

namespace VIB\ImapAuthenticationBundle\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

interface ImapUserInterface extends UserInterface, EquatableInterface, \Serializable
{
    public function __toString();
}
