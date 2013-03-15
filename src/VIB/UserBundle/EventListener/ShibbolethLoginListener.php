<?php

namespace VIB\UserBundle\EventListener;
 
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

use KULeuven\ShibbolethBundle\Security\ShibbolethUserToken; 
use VIB\UserBundle\Security\ShibbolethUserProvider;


class ShibbolethLoginListener implements EventSubscriberInterface {
    
    /**
     * @var VIB\UserBundle\Security\ShibbolethUserProvider
     */
    private $userProvider;
 
    public function __construct(ShibbolethUserProvider $userProvider) {
        $this->userProvider = $userProvider;
    }
 
    public static function getSubscribedEvents() {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        );
    }
    
    public function onInteractiveLogin(InteractiveLoginEvent $event) {
        
        $token = $event->getAuthenticationToken();
        if ($token instanceof ShibbolethUserToken) {
            $this->userProvider->updateUser($token);
        }
    }
}


?>
