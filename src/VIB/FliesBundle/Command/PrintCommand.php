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
use Symfony\Component\Console\Output\OutputInterface;

use VIB\FliesBundle\Entity\Stock;

/**
 * PrintCommand
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class PrintCommand extends Command
{
    private $container;

    protected function configure()
    {
        $this
            ->setName('flydb:print')
            ->setDescription('Print labels for stocks read from file')
            ->addArgument(
                'csv',
                InputArgument::REQUIRED,
                'File with list of stocks'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('csv');
        $file = fopen($filename,'r');

        $this->container = $this->getApplication()->getKernel()->getContainer();
        $om = $this->container->get('vib.doctrine.manager');
        $vm = $this->container->get('vib.doctrine.vial_manager');

        $stocks = array();

        if ($file) {
            while ($data = fgetcsv($file, 0, "\t")) {
                $stocks[] = $data[0];
            }
        }

        natsort($stocks);

        $pdf = $this->container->get('vibfolks.pdflabel');

        foreach ($stocks as $stockname) {
            $qb = $om->getRepository('VIB\FliesBundle\Entity\Stock')->createQueryBuilder('b');
            $qb->where('b.name like :term')
               ->setParameter('term', $stockname);
            $stock = $qb->getQuery()->getSingleResult();
            echo $stock->getName() . "\n";

            foreach ($stock->getLivingVials() as $vial) {
                if (! $vial->isLabelPrinted()) {
                    echo "\t" . $vial . "\n";
                    $pdf->addLabel($vial);
                    $vm->markPrinted($vial);
                }
            }
        }

        $vm->flush();

        $jobStatus = $pdf->printPDF();
        echo $jobStatus . "\n";

    }
}
