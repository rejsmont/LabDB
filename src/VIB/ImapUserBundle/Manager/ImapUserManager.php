<?php

namespace VIB\ImapUserBundle\Manager;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use VIB\ImapUserBundle\Exception\ConnectionException;

class ImapUserManager implements ImapUserManagerInterface
{
    private $imapConnection;
    private $username;
    private $password;
    private $params;
    private $roles;
    

    public function __construct(ImapConnectionInterface $conn)
    {
        $this->imapConnection = $conn;
        $this->params = $this->imapConnection->getParameters();
    }

    /**
     * @throws inherit
     */
    public function exists($username)
    {
        $this->setUsername($username);
    }

    
    public function auth()
    {
        if (strlen($this->password) === 0) {
            throw new ConnectionException('Password can\'t be empty');
        }
        $this->bind();
        
        return TRUE;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setUsername($username)
    {
        if ($username === "*") {
            throw new \InvalidArgumentException("Invalid username given.");
        }

        $this->username = $username;

        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
    
    private function bind()
    {
        return $this->imapConnection
            ->bind($this->username, $this->password);
    } 
}
