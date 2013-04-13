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

namespace VIB\SecurityBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * VIBSecurityExtension
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class VIBSecurityExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (isset($config['acl_walker'])) {
            $container->setParameter('vib.security.acl_walker', $config['acl_walker']);
        } else {
            $container->setParameter('vib.security.acl_walker', 'VIB\SecurityBundle\Bridge\Doctrine\AclWalker');
        }

        if (isset($config['acl_class_mapping'])) {
            $container->setParameter('vib.security.acl_class_mapping', $config['acl_class_mapping']);
        } else {
            $container->setParameter('vib.security.acl_class_mapping', array());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'vib_security';
    }
}
