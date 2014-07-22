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

namespace VIB\FliesBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Doctrine\Common\Collections\ArrayCollection;

use VIB\FliesBundle\Entity\Rack;

/**
 * NewRackCommand
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class NewRackCommand extends Command
{
    private $container;

    protected function configure()
    {
        $this
            ->setName('flydb:racks:create')
            ->setDescription('Create racks from CSV')
            ->addArgument(
                'listfile',
                InputArgument::REQUIRED,
                'List of racks to import'
            )
            ->addArgument(
                'owner',
                InputArgument::REQUIRED,
                'Owner of imported racks'
            )
            ->addOption(
               'print',
               null,
               InputOption::VALUE_NONE,
               'Print labels for new racks'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $listfilename = $input->getArgument('listfile');
        $racklist = array();
        if ($listfilename) {
            $listfile = fopen($listfilename,'r');
            if ($listfile) {
                while ($data = fgetcsv($listfile,0,"\t")) {
                    $racklist[] = array('name' => $data[0], 'contents' => $data[1]);
                }
            }
        }
        
        $dialog = $this->getHelperSet()->get('dialog');
        $this->container = $this->getApplication()->getKernel()->getContainer();
        $user = $this->container->get('user_provider')->loadUserByUsername($input->getArgument('owner'));

        $om = $this->container->get('vib.doctrine.registry')->getManagerForClass('VIB\CoreBundle\Entity\Entity');
        $om->disableAutoAcl();
        $om->getConnection()->beginTransaction();

        $racks = new ArrayCollection();

        foreach ($racklist as $rackitem) {
                echo $rackitem['name'] . "\t" . $rackitem['contents'] . "\n";
                $rack = new Rack(10,10);
                $rack->setDescription($rackitem['name'] . "\n" . $rackitem['contents']);
                $racks[] = $rack;
                $om->persist($rack);            
        }

        $output->writeln("Objects imported. Flushing DB buffer.");
        $om->flush();
        $output->writeln("Creating ACLs.");
        $om->createACL($racks, $user);
        $message = 'Everything seems to be OK. Commit?';
        if ($dialog->askConfirmation($output, '<question>' . $message . '</question>', true)) {
            $em->getConnection()->commit();
            if ($input->getOption('print')) {
                echo "Will print labels now.\n";
                $pdf = $this->container->get('vibfolks.pdflabel');
                foreach($racks as $rack) {
                    $pdf->addLabel($rack);
                }
                $jobStatus = $pdf->printPDF();
                echo $jobStatus . "\n";
            }
        } else {
            $om->getConnection()->rollback();
            $om->close();
        }
        $om->enableAutoAcl();
    }
}
