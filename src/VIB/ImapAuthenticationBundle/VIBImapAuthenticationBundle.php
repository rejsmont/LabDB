<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace VIB\ImapAuthenticationBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use VIB\ImapAuthenticationBundle\Factory\ImapFactory;

/**
 * ImapUser Bundle
 *
 * @author Radoslaw Ejsmont <radoslaw@ejsmont.net>
 * @author Boris Morel
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class VIBImapAuthenticationBundle extends Bundle
{
    public function boot()
    {
        if (!function_exists('imap_open')) {
            throw new \Exception("Required module php-imap is not installed");
        }
    }

    /**
     * Build the bundle.
     *
     * This is used to register the security listener to support Symfony 2.1.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new ImapFactory);
    }
}
