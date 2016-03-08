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

namespace VIB\KULeuvenImapUserBundle\EventListener;


use JMS\DiExtraBundle\Annotation as DI;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Psr\Log\LoggerInterface;

use VIB\KULeuvenImapUserBundle\Security\ImapUserProvider;

/**
 * This listener updates user with shibboleth data upon login
 *
 * @DI\Service
 * @DI\Tag("kernel.event_subscriber")
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ImapLoginListener implements EventSubscriberInterface
{
    /**
     * @var Symfony\Component\Security\Core\User\UserProviderInterface
     */
    private $userProvider;

    /**
     * Construct ShibbolethLoginListener
     *
     * @DI\InjectParams({
     *     "userProvider" = @DI\Inject("user_provider"),
     * })
     * 
     * @param Symfony\Component\Security\Core\User\UserProviderInterface $userProvider
     */
    public function __construct(UserProviderInterface $userProvider)
    {        
        if ($userProvider instanceof ImapUserProvider) {
            $this->userProvider = $userProvider;
        } else {
            $this->userProvider = null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        );
    }

    /**
     * Updates user with IMAP data upon login
     *
     * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();
        if ((null !== $this->userProvider)
                &&($token instanceof UsernamePasswordToken)) {
            $this->userProvider->updateUser($token);
        }
    }
}
