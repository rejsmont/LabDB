<?php

namespace VIB\ImapUserBundle\Provider;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException,
    Symfony\Component\Security\Core\Exception\UsernameNotFoundException,
    Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\User\UserProviderInterface;

use VIB\ImapUserBundle\Manager\ImapUserManagerInterface,
    VIB\ImapUserBundle\User\ImapUserInterface;

/**
 * LDAP User Provider
 *
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
     * @var string
     */
    private $bindUsernameBefore;

    /**
     * The class name of the User model
     * @var string
     */
    private $userClass;

    /**
     * Constructor
     *
     * @param \VIB\ImapUserBundle\Manager\ImapUserManagerInterface $imapManager
     * @param bool|string                                       $bindUsernameBefore
     * @param string                                            $userClass
     */
    public function __construct(
            ImapUserManagerInterface $imapManager,
            $bindUsernameBefore = false,
            $userClass = 'VIB\ImapUserBundle\User\ImapUser')
    {
        $this->imapManager = $imapManager;
        $this->bindUsernameBefore = $bindUsernameBefore;
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

        if (true === $this->bindUsernameBefore) {
            $imapUser = $this->simpleUser($username);
        } else {
            $imapUser = $this->anonymousSearch($username);
        }

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

        if (false === $this->bindUsernameBefore) {
            return $this->loadUserByUsername($user->getUsername());
        } else {
            return $this->bindedSearch($user->getUsername());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return is_subclass_of($class, '\VIB\ImapUserBundle\User\ImapUserInterface');
    }

    private function simpleUser($username)
    {
        $imapUser = new $this->userClass;
        $imapUser->setUsername($username);

        return $imapUser;
    }

    private function anonymousSearch($username)
    {
        $this->imapManager->exists($username);

        $im = $this->imapManager
            ->setUsername($username)
            ->doPass();

        $imapUser = new $this->userClass;

        $imapUser
            ->setUsername($im->getUsername())
            ->setEmail($im->getEmail())
            ->setRoles($im->getRoles())
            ->setDn($im->getDn())
            ->setCn($im->getCn())
            ->setAttributes($im->getAttributes())
            ->setGivenName($im->getGivenName())
            ->setSurname($im->getSurname())
            ->setDisplayName($im->getDisplayName())
            ;

        return $imapUser;
    }

    private function bindedSearch($username)
    {
        return $this->anonymousSearch($username);
    }
}
