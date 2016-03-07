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
     * @var VIB\UserBundle\Security\ShibbolethUserProvider
     */
    private $userProvider;
    private $logger;

    /**
     * Construct ShibbolethLoginListener
     *
     * @DI\InjectParams({
     *     "userProvider" = @DI\Inject("user_provider"),
     *     "logger" = @DI\Inject("logger", required = false)
     * })
     * 
     * @param Symfony\Component\Security\Core\User\UserProviderInterface $userProvider
     */
    public function __construct(
            UserProviderInterface $userProvider,
            LoggerInterface $logger = null)
    {        
        if ($userProvider instanceof ImapUserProvider) {
            $this->userProvider = $userProvider;
        } else {
            $this->userProvider = null;
        }
        
        $this->logger = $logger;
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
        if (null !== $this->logger) {
            $this->logger->debug(sprintf('InteractiveLoginEvent triggered'));
        }
        $token = $event->getAuthenticationToken();
        
        if (null !== $this->logger) {
            $this->logger->debug(sprintf('Token is: %s', get_class($token)));
        }
        
        if ((null !== $this->userProvider)&&($token instanceof UsernamePasswordToken)) {
            
            $this->userProvider->updateUser($token);
            
            if (null !== $this->logger) {
                $this->logger->debug(sprintf('User created!'));
            }
        }
    }
}
