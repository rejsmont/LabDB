<?php

/*
 * Copyright 2014 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
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

namespace VIB\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;


/**
 * Description of ManagerCompilerPass
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ManagerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('vib.doctrine.registry')) {
            return;
        }
        
        $definition = $container->getDefinition(
            'vib.doctrine.registry'
        );
        
        $taggedServices = $container->findTaggedServiceIds(
            'vibcore.object_manager'
        );
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addManager',
                array(new Reference($id))
            );
        }
    }
}
