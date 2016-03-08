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

namespace VIB\KULeuvenImapUserBundle\Security;

use JMS\DiExtraBundle\Annotation as DI;

use FOS\UserBundle\Model\User as BaseUser;
use FOS\UserBundle\Security\UserProvider as BaseUserProvider;
use FOS\UserBundle\Model\UserManagerInterface;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use VIB\ImapAuthenticationBundle\Provider\ImapUserProviderInterface;

/**
 * KU Leuven IMAP UserProvider
 *
 * @DI\Service("vib.user_provider.kuleuven_imap")
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ImapUserProvider extends BaseUserProvider implements ImapUserProviderInterface
{
    /**
     * @DI\InjectParams({
     *     "userManager" = @DI\Inject("fos_user.user_manager")
     * })
     * 
     * {@inheritDoc}
     */
    public function __construct(UserManagerInterface $userManager)
    {
        parent::__construct($userManager);
    }
    
    public function loadUserByUsername($username)
    {
        $user = parent::loadUserByUsername($username);
        
        return $user;
    }
    
    /**
     * Create a new user using imap data source
     *
     * @param  \KULeuven\ShibbolethBundle\Security\ShibbolethUserToken $token
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function createUser(UsernamePasswordToken $token)
    {
        $user = $this->userManager->createUser();
        $user->setUsername($token->getUsername());
        $this->setUserData($user, $token);

        return $user;
    }

    /**
     * Update user using imap data source
     *
     * @param  \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken $token
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function updateUser(UsernamePasswordToken $token)
    {
        $user = $this->loadUserByUsername($token->getUsername());
        $this->setUserData($user, $token);

        return $user;
    }

    /**
     * Set user data using imap data source
     *
     * @param \FOS\UserBundle\Entity\User                             $user
     * @param \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken $token
     */
    private function setUserData(BaseUser $user, UsernamePasswordToken $token)
    {
        $userName = $user->getUsername();
        $userNameArray = explode('@', $userName);
        if (count($userNameArray) > 1) {
            $userName = $userNameArray[0];
        }
        
        $unumber = str_replace('u', '', $userName);
        
        $url = "http://www.kuleuven.be/wieiswie/en/person/" . $unumber;
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $html = curl_exec($ch);
        curl_close($ch);

        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        
        $uname = "";
        foreach($dom->getElementsByTagName('h1') as $h1) {
            $uname = $h1->nodeValue;
        }
        
        $uemail = "";
        foreach($dom->getElementsByTagName('script') as $script) {
            $emailPre = trim($script->nodeValue);
            if (!empty($emailPre)) {
                $emailPre = str_replace('document.write(String.fromCharCode(', '', $emailPre);
                $emailPre = str_replace('))', '', $emailPre);
                $emailArray = explode(',', $emailPre);

                $emailLink = "";
                    foreach ($emailArray as $element) {
                    $emailLink .= chr(eval('return '.$element.';'));
                }

                $domInner = new \DOMDocument();
                @$domInner->loadHTML($emailLink);

                foreach($domInner->getElementsByTagName('a') as $a) {
                    $uemail = $a->nodeValue;
                }
            }
        }

        $names = explode(" ", $uname);
        $mailparts = explode("@", $uemail);
        $mailuname = $mailparts[0];
        $mailnames = explode(".", $mailuname);
        
        $surnameIndex = 0;
        foreach ($names as $index => $name) {
            if (strtolower(substr($mailnames[1], 0, strlen($name))) === strtolower($name)) {
                $surnameIndex = $index;
            }
        }
        
        if ($user instanceof User) {
            $givenName = implode(" ", array_slice($names, 0, $surnameIndex));
            $user->setGivenName($givenName);
            $lastName = implode(" ", array_slice($names, $surnameIndex));
            $user->setSurname($lastName);
        }
        
        $user->setEmail($uemail);
        $user->setPlainPassword($this->generateRandomString());
        $user->addRole('ROLE_USER');
        $user->addRole('ROLE_KULEUVEN');
        $user->setEnabled(true);
        
        $this->userManager->updateUser($user);
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }   
}
