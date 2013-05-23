<?php

/*
 * Copyright 2011 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
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

namespace VIB\TestBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Doctrine\Common\Collections\ArrayCollection;

use VIB\FliesBundle\Entity\Stock;
use VIB\FliesBundle\Entity\StockVial;

/**
 * BootstrapCommand
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class BootstrapCommand extends Command
{
    private $container;

    protected function configure()
    {
        $this
            ->setName('test:bootstrap')
            ->setDescription('Bootstrap test environment')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->getApplication()->getKernel()->getContainer();

        $connection = $this->container->get('doctrine')->getManager()->getConnection();
        $params = $connection->getParams();
        
        if ((isset($params['driver']))&&($params['driver'] == 'pdo_sqlite')) {
            $file = $params['path'];
            $parts = explode(DIRECTORY_SEPARATOR, $file);
            array_pop($parts);
            $dir = implode(DIRECTORY_SEPARATOR, $parts);
            if (is_dir($dir)) {
                chmod($dir, 0777);
            } else {
                mkdir($dir, 0777, true);
            }
            touch($file);
            chmod($file, 0666);
        }
        
    }
}
