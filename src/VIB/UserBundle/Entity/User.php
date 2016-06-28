<?php

/*
 * Copyright 2013 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace VIB\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use VIB\ImapAuthenticationBundle\User\ImapUserInterface;

/**
 * User entity
 *
 * @ORM\Entity(repositoryClass="VIB\UserBundle\Repository\UserRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "user" = "\VIB\UserBundle\Entity\User",
 *     "imapuser" = "\VIB\ImapAuthenticationBundle\Entity\ImapUser"})
 * @ORM\Table(name="user")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
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
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * Get given name
     *
     * @return string
     */
    public function getInitials()
    {
        $names = explode(" ", $this->getGivenName());
        $initials = "";
        foreach ($names as $name) {
            $initials .= substr($name,0,1);
        }

        return $initials;
    }

    /**
     * Set given name
     *
     * @param string $givenName
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName()
    {
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
    public function getShortName()
    {
        if (($surname = $this->getSurname() != "")&&($this->getInitials() != "")) {
            if (strlen($surname) > 12) {
                $surname = substr($surname, 0, 12) . "…";
            }
            return (string) $this->getInitials() . " " . $this->getSurname();
        } else {
            return $this->getUsername();
        }
    }

    /**
     * Set surname
     *
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * Convert user object to string
     *
     * @return string
     */
    public function __toString()
    {
        if (strlen($this->getFullName()) <= 12) {
            return (string) $this->getFullName();
        } else {
            return (string) $this->getShortName();
        }
    }
    
    public function isEqualTo(UserInterface $user)
    {
        /*if (!$user instanceof User
            || $user->getUsername() !== $this->getUsername()
            || count(array_diff($user->getRoles(), $this->getRoles())) > 0) {
            
            return false;
        }*/
        
        echo "<pre>";
        echo "\n\n\n\n";
        print_r($this);
        echo "\n";
        print_r($user);
        echo "\n---\n";
        echo "</pre>";
        
        return true;
    }
}
