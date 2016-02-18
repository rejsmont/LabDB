<?php

namespace VIB\ImapUserBundle\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

interface ImapUserInterface extends UserInterface, EquatableInterface, \Serializable
{
    public function getEmail();
    public function setEmail($email);
    public function __toString();
}
