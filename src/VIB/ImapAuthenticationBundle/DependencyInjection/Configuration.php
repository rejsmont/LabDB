<?php

namespace VIB\ImapAuthenticationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface,
  Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
  public function getConfigTreeBuilder()
  {
    $treeBuilder = new TreeBuilder();
    $rootNode = $treeBuilder->root('vib_imap_authentication');
    $rootNode
        ->children()
            ->append($this->addConnectionNode())
            ->scalarNode('user_class')
              ->defaultValue("VIB\ImapAuthenticationBundle\User\ImapUser")
            ->end()
        ->end()
        ;

    return $treeBuilder;
  }

  private function addConnectionNode()
  {
      $treeBuilder = new TreeBuilder();
      $node = $treeBuilder->root('connection');

      $node
          ->isRequired()
          ->children()
              ->scalarNode('host')->isRequired()->cannotBeEmpty()->end()
              ->scalarNode('port')->defaultValue(143)->end()
              ->booleanNode('secure')->defaultTrue()->end()
              ->enumNode('encryption')->values(array('none', 'ssl', 'tls'))->end()
              ->booleanNode('validate_cert')->defaultTrue()->end()
              ->integerNode('n_retries')->defaultValue(0)->end()
           ->end()
          ;

      return $node;
  }
}
