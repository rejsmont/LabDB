<?php

namespace VIB\ImapAuthenticationBundle\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\Config\Definition\Builder\NodeDefinition,
    Symfony\Component\DependencyInjection\DefinitionDecorator,
    Symfony\Component\DependencyInjection\Reference;

class ImapFactory extends AbstractFactory
{
    public function __construct()
    {
        $this->addOption('username_parameter', '_username');
        $this->addOption('password_parameter', '_password');
        $this->addOption('csrf_parameter', '_csrf_token');
        $this->addOption('intention', 'imap_authenticate');
        $this->addOption('post_only', true);
    }

    public function getPosition()
    {
        return 'form';
    }

    public function getKey()
    {
        return 'vib-imap';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);
    
        $node
            ->children()
                ->scalarNode('csrf_provider')->cannotBeEmpty()->end()
            ->end()
            ;
    }

    protected function getListenerId()
    {
        return 'vib_imap.security.authentication.listener';
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $dao = 'security.authentication.provider.dao.'.$id;
        $container
            ->setDefinition($dao, new DefinitionDecorator('security.authentication.provider.dao'))
            ->replaceArgument(0, new Reference($userProviderId))
            ->replaceArgument(2, $id)
        ;

        $provider = 'vib_imap.security.authentication.provider.'.$id;
        $container
            ->setDefinition($provider, new DefinitionDecorator('vib_imap.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProviderId))
            ->replaceArgument(4, $id)
            ;

        return $provider;
    }

    protected function createlistener($container, $id, $config, $userProvider)
    {
        $listenerId = parent::createListener($container, $id, $config, $userProvider);

        if (isset($config['csrf_provider'])) {
            $container
                ->getDefinition($listenerId)
                ->addArgument(new Reference($config['csrf_provider']))
                ;
        }

        return $listenerId;
    }

    protected function createEntryPoint($container, $id, $config, $defaultEntryPoint)
    {
        $entryPointId = 'vib_imap.security.authentication.form_entry_point.'.$id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('vib_imap.security.authentication.form_entry_point'))
            ->addArgument(new Reference('security.http_utils'))
            ->addArgument($config['login_path'])
            ->addArgument($config['use_forward'])
            ;

        return $entryPointId;
    }
}
