<?php

namespace VIB\ImapAuthenticationBundle\Provider;

use VIB\ImapAuthenticationBundle\Exception\ConnectionException;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Psr\Log\LoggerInterface;

use JMS\DiExtraBundle\Annotation as DI;

use VIB\ImapAuthenticationBundle\Manager\ImapUserManagerInterface;
use VIB\ImapAuthenticationBundle\Event\ImapUserEvent;
use VIB\ImapAuthenticationBundle\Event\ImapEvents;
use VIB\ImapAuthenticationBundle\User\ImapUserInterface;

/**
 * KU Leuven IMAP UserProvider
 *
 * @DI\Service("vib_imap.security.authentication.provider")
 * 
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ImapAuthenticationProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $imapManager;
    private $dispatcher;
    private $providerKey;
    private $hideUserNotFoundExceptions;
    private $logger;

    /**
     * Constructor
     *
     * Please note that $hideUserNotFoundExceptions is true by default in order
     * to prevent a possible brute-force attack.
     *
     * @DI\InjectParams({
     *      "userProvider" = @DI\Inject("user_provider"),
     *      "userChecker" = @DI\Inject("security.user_checker"),
     *      "imapManager" = @DI\Inject("vib_imap.imap_manager"),
     *      "dispatcher" = @DI\Inject("event_dispatcher", required = false),
     *      "logger" = @DI\Inject("logger", required = false),
     *      "providerKey" = @DI\Inject("%%"),
     *      "hideUserNotFoundExceptions" = @DI\Inject("%security.authentication.hide_user_not_found%")
     * })
     * 
     * @param UserProviderInterface    $userProvider
     * @param UserCheckerInterface     $userChecker
     * @param ImapUserManagerInterface $imapManager
     * @param EventDispatcherInterface $dispatcher
     * @param string                   $providerKey
     * @param Boolean                  $hideUserNotFoundExceptions
     */
    public function __construct(
            UserProviderInterface $userProvider,
            UserCheckerInterface $userChecker,
            ImapUserManagerInterface $imapManager,
            EventDispatcherInterface $dispatcher = null,
            LoggerInterface $logger = null,
            $providerKey = 'vib-imap',
            $hideUserNotFoundExceptions = true )
    {
        $this->userProvider = $userProvider;
        $this->userChecker = $userChecker;
        $this->imapManager = $imapManager;
        $this->dispatcher = $dispatcher;
        $this->providerKey = $providerKey;
        $this->hideUserNotFoundExceptions = $hideUserNotFoundExceptions;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {

        if (! $this->supports($token)) {
            throw new AuthenticationException('Unsupported token');
        }

        try {          
            $user = $this->retrieveUser($token);
            $authenticatedToken = $this->imapAuthenticate($user, $token);
            
            if ($user instanceof UserInterface) {
                $this->userChecker->checkPostAuth($user);
            }
            
            return $authenticatedToken;

        } catch (\Exception $e) {
            if (($e instanceof ConnectionException ||
                    $e instanceof UsernameNotFoundException) &&
                        $this->hideUserNotFoundExceptions) {
                
                throw $e;
                
                //throw new BadCredentialsException('Bad credentials', 0, $e);
            }

            throw $e;
        }
    }

    /**
     * Retrieve user from security token
     * 
     * @param type $token
     * @return type
     * @throws UsernameNotFoundException
     * @throws AuthenticationServiceException
     */
    public function retrieveUser($token)
    {
        try {
            $user = $this->userProvider->loadUserByUsername($token->getUsername());
            
            if (null !== $this->logger) {
                $this->logger->debug(sprintf(
                        'ImapAuthProvider: userProvider returned: %s',
                            $user->getUsername()));
            }

            if (!$user instanceof UserInterface) {
                
                throw new AuthenticationServiceException(
                        'The user provider must return a UserInterface object.');
            }

        } catch (UsernameNotFoundException $notFound) {
            
            if ($this->userProvider instanceof ImapUserProviderInterface) {
                
                $user = $this->userProvider->createUser($token);
                
                $this->logger->debug(sprintf(
                        'Created USER has roles: %s',
                            print_r($user->getRoles(), true)));
                
                if ($user === null) {
                        $user = $token->getUsername();
                }
                
            } else {
                
                throw $notFound;
            }
        }

        return $user;
    }
    
    /**
     * Authentication logic to allow IMAP user
     *
     * @param Symfony\Component\Security\Core\User\UserInterface  $user
     * @param Symfony\Component\Security\Core\Authentication\Token\TokenInterface  $token
     *
     * @return \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken  $token
     */
    private function imapAuthenticate(UserInterface $user, TokenInterface $token)
    {
        $userEvent = new ImapUserEvent($user);
        
        if (null !== $this->dispatcher) {
            try {
                $this->dispatcher->dispatch(ImapEvents::PRE_BIND, $userEvent);
                
            } catch (AuthenticationException $expt) {
                if ($this->hideUserNotFoundExceptions) {
                    
                    throw new BadCredentialsException('Bad credentials', 0, $expt);
                }
                
                throw $expt;
            }
        }
        
        $this->bind($user, $token);
        
        if (null === $user->getUsername()) {
            $user = $this->reloadUser($user);
        }
        
        if (null !== $this->dispatcher) {
            $userEvent = new ImapUserEvent($user);
            
            try {
                $this->dispatcher->dispatch(ImapEvents::POST_BIND, $userEvent);
                
            } catch (AuthenticationException $authenticationException) {
                if ($this->hideUserNotFoundExceptions) {
                    throw new BadCredentialsException('Bad credentials', 0, $authenticationException);
                }
                throw $authenticationException;
            }
        }
        
        $authenticatedToken = new UsernamePasswordToken($userEvent->getUser(), 
                null, $this->providerKey, $userEvent->getUser()->getRoles());
        $authenticatedToken->setAttributes($token->getAttributes());
        
        if (null !== $this->logger) {
            if ($authenticatedToken->isAuthenticated()) {
                $this->logger->debug(sprintf('Token has been authenticated.'));
            } else {
                $this->logger->debug(sprintf('WTF? Token is NOT authenticated.'));
            }
            
        }
        
        return $authenticatedToken;
    }

    /**
     * Authenticate the user with IMAP login.
     *
     * @param Symfony\Component\Security\Core\User\UserInterface  $user
     * @param Symfony\Component\Security\Core\Authentication\Token\TokenInterface  $token
     *
     * @return true
     */
    private function bind(UserInterface $user, TokenInterface $token)
    {
        $this->imapManager
            ->setUsername($user->getUsername())
            ->setPassword($token->getCredentials());

        $this->imapManager->auth();

        return true;
    }

    /**
     * Reload user with the username
     *
     * @param \VIB\ImapAuthenticationBundle\User\ImapUserInterface $user
     * @return \VIB\ImapAuthenticationBundle\User\ImapUserInterface $user
     */
    private function reloadUser(UserInterface $user)
    {
        try {
            $user = $this->userProvider->refreshUser($user);
            
        } catch (UsernameNotFoundException $userNotFoundException) {
            
            if ($this->hideUserNotFoundExceptions) {
                throw new BadCredentialsException('Bad credentials', 0, $userNotFoundException);
            }

            throw $userNotFoundException;
        }

        return $user;
    }

    /**
     * Check whether this provider supports the given token.
     *
     * @param TokenInterface $token
     *
     * @return boolean
     */
    public function supports(TokenInterface $token)
    {
        
        return (($token instanceof UsernamePasswordToken) &&
                ($token->getProviderKey() === $this->providerKey) &&
                ($this->imapManager->supports($token->getUsername())));
    }
}
