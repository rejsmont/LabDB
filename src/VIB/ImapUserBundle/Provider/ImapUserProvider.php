<?php

namespace VIB\ImapUserBundle\Provider;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use VIB\ImapUserBundle\Manager\ImapUserManagerInterface;
use VIB\ImapUserBundle\User\ImapUserInterface;

/**
 * LDAP User Provider
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * @author Boris Morel
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class ImapUserProvider implements UserProviderInterface
{
    /**
     * @var \VIB\ImapUserBundle\Manager\ImapUserManagerInterface
     */
    private $imapManager;

    /**
     * The class name of the User model
     * @var string
     */
    private $userClass;

    /**
     * Constructor
     *
     * @param \VIB\ImapUserBundle\Manager\ImapUserManagerInterface $imapManager
     * @param bool|string                                          $bindUsernameBefore
     * @param string                                               $userClass
     */
    public function __construct(
            ImapUserManagerInterface $imapManager,
            $userClass = 'VIB\ImapUserBundle\User\ImapUser')
    {
        $this->imapManager = $imapManager;
        $this->userClass = $userClass;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        // Throw the exception if the username is not provided.
        if (empty($username)) {
            throw new UsernameNotFoundException('The username is not provided.');
        }
        
        $imapUser = new $this->userClass;
        $imapUser->setUsername($username);

        return $imapUser;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof ImapUserInterface) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return is_subclass_of($class, '\VIB\ImapUserBundle\User\ImapUserInterface');
    }
}
