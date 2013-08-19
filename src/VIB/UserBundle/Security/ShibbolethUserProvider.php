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

namespace VIB\UserBundle\Security;

use FOS\UserBundle\Model\User as BaseUser;
use FOS\UserBundle\Security\UserProvider as BaseUserProvider;
use KULeuven\ShibbolethBundle\Security\ShibbolethUserProviderInterface;
use KULeuven\ShibbolethBundle\Security\ShibbolethUserToken;

use VIB\UserBundle\Entity\User;

/**
 * Shibboleth UserProvider
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ShibbolethUserProvider extends BaseUserProvider implements ShibbolethUserProviderInterface
{
    /**
     * Create a new user using shibboleth heders as data source
     *
     * @param  \KULeuven\ShibbolethBundle\Security\ShibbolethUserToken $token
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function createUser(ShibbolethUserToken $token)
    {
        $user = $this->userManager->createUser();
        $user->setUsername($token->getUsername());
        $this->setUserData($user, $token);

        return $user;
    }

    /**
     * Update user using shibboleth heders as data source
     *
     * @param  \KULeuven\ShibbolethBundle\Security\ShibbolethUserToken $token
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function updateUser(ShibbolethUserToken $token)
    {
        $user = $this->loadUserByUsername($token->getUsername());
        $this->setUserData($user, $token);

        return $user;
    }

    /**
     * Set user data using shibboleth heders as data source
     *
     * @param \FOS\UserBundle\Entity\User                             $user
     * @param \KULeuven\ShibbolethBundle\Security\ShibbolethUserToken $token
     */
    private function setUserData(BaseUser $user, ShibbolethUserToken $token)
    {
        if ($user instanceof User) {
            $user->setGivenName($token->getGivenName());
            $user->setSurname($token->getSurname());
        }
        $user->setPlainPassword('no_passwd');
        if (null != $token->getMail()) {
            $user->setEmail($token->getMail());
        } else {
            $user->setEmail($token->getUsername() . '@kuleuven.be');
        }
        if ($token->isStudent()) {
            $user->addRole('ROLE_STUDENT');
        } elseif ($token->isStaff()) {
            $user->addRole('ROLE_STAFF');
        } else {
            $user->addRole('ROLE_GUEST');
        }
        $user->addRole('ROLE_USER');
        $user->setEnabled(true);

        $this->userManager->updateUser($user);
    }
}
