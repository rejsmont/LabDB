<?php

namespace VIB\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuleuven_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @var string
     */
    protected $givenName;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @var string
     */
    protected $surname;
    
    
    /**
     * Get given name
     * 
     * @return string
     */
    public function getGivenName() {
        return $this->givenName;
    }
    
    /**
     * Get given name
     * 
     * @return string
     */
    public function getInitials() {
        $names = explode(" ", $this->getGivenName());
        $initials = "";
        foreach($names as $name) {
            $initials .= substr($name,0,1);
        }
        return $initials;
    }

    /**
     * Set given name
     * 
     * @param string $givenName
     */
    public function setGivenName($givenName) {
        $this->givenName = $givenName;
    }

    /**
     * Get surname
     * 
     * @return string
     */
    public function getSurname() {
        return $this->surname;
    }
    
    /**
     * Get full name
     * 
     * @return string
     */
    public function getFullName() {
        if (($this->getSurname() != "")&&($this->getGivenName() != "")) {
            return (string) $this->getGivenName() . " " . $this->getSurname();
        } else {
            return $this->getUsername();;
        }
    }
    
    /**
     * Get full name
     * 
     * @return string
     */
    public function getShortName() {
        if (($this->getSurname() != "")&&($this->getInitials() != "")) {
            return (string) $this->getInitials() . " " . $this->getSurname();
        } else {
            return $this->getUsername();;
        }
    }

    /**
     * Set surname
     * 
     * @param string $surname
     */
    public function setSurname($surname) {
        $this->surname = $surname;
    }
    
    /**
     * 
     * @return string
     */
    public function __toString() {
        if (strlen($this->getFullName()) <= 12) {
            return (string) $this->getFullName();
        } else {
            return (string) $this->getShortName();
        }
    }
}
