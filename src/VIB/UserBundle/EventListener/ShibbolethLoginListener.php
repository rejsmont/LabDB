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

namespace VIB\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

use KULeuven\ShibbolethBundle\Security\ShibbolethUserToken;
use VIB\UserBundle\Security\ShibbolethUserProvider;

/**
 * This listener updates user with shibboleth data upon login
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ShibbolethLoginListener implements EventSubscriberInterface
{
    /**
     * @var VIB\UserBundle\Security\ShibbolethUserProvider
     */
    private $userProvider;

    /**
     * Construct ShibbolethLoginListener
     *
     * @param \VIB\UserBundle\Security\ShibbolethUserProvider $userProvider
     */
    public function __construct(ShibbolethUserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
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
     * Updates user with shibboleth data upon login
     *
     * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {

        $token = $event->getAuthenticationToken();
        if ($token instanceof ShibbolethUserToken) {
            $this->userProvider->updateUser($token);
        }
    }
}
