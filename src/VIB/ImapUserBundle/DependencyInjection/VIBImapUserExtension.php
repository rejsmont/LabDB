<?php

namespace VIB\ImapUserBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

class VIBImapUserExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('security_imap.yml');

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('vib_imap.imap_connection.params', $config);
        $container->setParameter('vib_imap.authentication.bind_username_before', $config['client']['bind_username_before']);
        $container->setParameter('vib_imap.model.user_class', $config["user_class"]);
    }

    public function getAlias()
    {
        return "vib_imap";
    }
}
