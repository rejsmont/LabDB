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

use VIB\FliesBundle\Entity\Stock;
use VIB\FliesBundle\Entity\StockVial;

/**
 * NewVialCommand
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class NewVialCommand extends Command
{
    private $container;

    protected function configure()
    {
        $this
            ->setName('flydb:stockvials:create')
            ->setDescription('Create vials for stocks from CSV')
            ->addArgument(
                'listfile',
                InputArgument::REQUIRED,
                'List of stocks to import'
            )
            ->addArgument(
                'owner',
                InputArgument::REQUIRED,
                'Owner of imported stocks'
            )
            ->addOption(
               'print',
               null,
               InputOption::VALUE_NONE,
               'Print labels for new vials'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $listfilename = $input->getArgument('listfile');
        $stocks = array();
        if ($listfilename) {
            $listfile = fopen($listfilename,'r');
            if ($listfile) {
                while ($data = fgetcsv($listfile,0,"\t")) {
                    $stocks[] = $data[0];
                }
            }
        }

        natsort($stocks);
        
        $dialog = $this->getHelperSet()->get('dialog');
        $this->container = $this->getApplication()->getKernel()->getContainer();
        $user = $this->container->get('user_provider')->loadUserByUsername($input->getArgument('owner'));

        $om = $this->container->get('vib.doctrine.registry')->getManagerForClass('VIB\CoreBundle\Entity\Entity');
        $vm = $this->container->get('vib.doctrine.registry')->getManagerForClass('VIB\FliesBundle\Entity\Vial');
        $om->disableAutoAcl();
        $vm->disableAutoAcl();
        
        $om->getConnection()->beginTransaction();

        $vials = new ArrayCollection();

        foreach ($stocks as $stockname) {
            try {
                $stock = $om->getRepository('VIB\FliesBundle\Entity\Stock')
                        ->createQueryBuilder('b')
                        ->where('b.name = :name')
                        ->setParameter('name', $stockname)
                        ->getQuery()
                        ->getSingleResult();
            } catch (\Exception $e) {
                echo "$stockname not found!\n";
                $stock = null;
            }
            if (null !== $stock) {
                echo $stock->getName() . "\n";
                $vial = new StockVial();
                $vial->setStock($stock);
                $vials[] = $vial;
                $vm->persist($vial);
            }
            
        }

        $output->writeln("Objects imported. Flushing DB buffer.");
        $om->flush();
        $output->writeln("Creating ACLs.");
        $vm->createACL($vials, $user);
        $message = 'Everything seems to be OK. Commit?';
        if ($dialog->askConfirmation($output, '<question>' . $message . '</question>', true)) {
            $om->getConnection()->commit();
            if ($input->getOption('print')) {
                echo "Will print labels now.\n";
                $pdf = $this->container->get('vibfolks.pdflabel');
                foreach($vials as $vial) {
                    $pdf->addLabel($vial);
                    $vm->markPrinted($vial);
                }
                $vm->flush();
                $jobStatus = $pdf->printPDF();
                echo $jobStatus . "\n";
            }
        } else {
            $om->getConnection()->rollback();
            $om->close();
        }
        $om->enableAutoAcl();
        $vm->enableAutoAcl();
    }
}
