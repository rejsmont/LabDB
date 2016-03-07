<?php

namespace VIB\ImapAuthenticationBundle\User;

class ImapUser implements ImapUserInterface
{
    protected $username;
    protected $roles;
    
    public function getUsername()
    {
        return $this->username;
    }
    
    public function setUsername($username)
    {
        $this->username = $username;
        
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
            || $user->getUsername() !== $this->username
            || count(array_diff($user->getRoles(), $this->roles)) > 0) {
            
            return false;
        }

        return true;
    }

    public function serialize()
    {
        return serialize(array(
            $this->username,
            $this->roles
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->username,
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
        return $this->getUsername();
    }
}
