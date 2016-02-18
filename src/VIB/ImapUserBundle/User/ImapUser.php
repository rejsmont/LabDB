<?php

namespace VIB\ImapUserBundle\User;

class ImapUser implements ImapUserInterface
{
    protected $email;
    protected $roles;

    public function getUsername()
    {
        $parts = explode("@", $this->email);
        return $parts[0];
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
    
    public function getPassword()
    {
        return null;
    }

    public function getSalt()
    {
        return null;
    }

    public function getRoles()
    {
        return $this->roles;
    }
    
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole($role)
    {
        $this->roles[] = $role;

        return $this;
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function isEqualTo(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        if (!$user instanceof ImapUserInterface
            || $user->getEmail() !== $this->email
            || count(array_diff($user->getRoles(), $this->roles)) > 0) {
            
            return false;
        }

        return true;
    }

    public function serialize()
    {
        return serialize(array(
            $this->email,
            $this->roles
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->email,
            $this->roles
        ) = unserialize($serialized);
    }

    /**
     * Return email when converting class to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getEmail();
    }
}
