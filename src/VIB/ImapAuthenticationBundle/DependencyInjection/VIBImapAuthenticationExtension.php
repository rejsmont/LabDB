<?php

namespace VIB\ImapAuthenticationBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

class VIBImapAuthenticationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('security_imap.yml');

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('vib_imap.imap_connection.params', $config);
        $container->setParameter('vib_imap.model.user_class', $config["user_class"]);
    }

    public function getAlias()
    {
        return 'vib_imap_authentication';
    }
}
