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
 * ImportCommand
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ImportCommand extends Command
{
    private $container;
    
    protected function configure()
    {
        $this
            ->setName('flydb:import')
            ->setDescription('Import stocks from CSV')
            ->addArgument(
                'csv',
                InputArgument::REQUIRED,
                'CSV file to import from'
            )
            ->addArgument(
                'owner',
                InputArgument::REQUIRED,
                'Owner of imported stocks'
            )
            ->addArgument(
                'logfile',
                InputArgument::REQUIRED,
                'Log file'
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
        $filename = $input->getArgument('csv');
        $file = fopen($filename,'r');
        
        $logfilename = $input->getArgument('logfile');
        $logfile = fopen($logfilename,'w');
        
        $dialog = $this->getHelperSet()->get('dialog');
        $this->container = $this->getApplication()->getKernel()->getContainer();
        $user = $this->container->get('user_provider')->loadUserByUsername($input->getArgument('owner'));
        
        $om = $this->container->get('vib.doctrine.manager');
        $vm = $this->container->get('vib.doctrine.vial_manager');
        $em = $this->container->get('doctrine')->getManager();
        
        $em->getConnection()->beginTransaction();
        
        $vials = new ArrayCollection();
        $stocks = new ArrayCollection();
        
        if ($file) {
            $acronyms = array();
            while ($data = fgetcsv($file,0,"\t")) {
                $stockName = str_replace(" ","",trim($data[1]));
                $stockGenotype = trim($data[15]) != '\N' ? trim($data[15]) : trim($data[2]);
                $projectName = trim($data[3]);
                $comment = trim($data[4]);
                $vendor = trim($data[6]);
                $reference = trim($data[7]);
                $chromosome = trim($data[8]);
                $url = trim($data[10]);
                $numberInProject = trim($data[11]);
                $verified = (boolean) trim($data[12]);
                $lab = strtoupper(trim($data[14]));
                if (($stockName != "")&&($stockName != "unknown")&&($lab == "LNG")) {
                    if (! isset($acronyms[$projectName])) {
                        $matches = array();
                        preg_match('/^[[:alpha:]]*/', $stockName, $matches);
                        $acronyms[$projectName] = $matches[0];
                        $projectAcronym = $acronyms[$projectName];
                    } else {
                        preg_match('/^[[:alpha:]]*/', $stockName, $matches);
                        $projectAcronym = $matches[0];
                        if ($projectAcronym != $acronyms[$projectName]) {
                            $stockName = str_replace($projectAcronym,$acronyms[$projectName],$stockName);
                        }
                    }
                    
                    $stock = new Stock();
                    $stock->setName($stockName);
                    if (($stockGenotype == '')||($stockGenotype == '\N')) {
                        $stockGenotype = 'unknown';
                    }
                    $stock->setGenotype($stockGenotype);
                    $stock->setGenotype(str_replace('<sup>','',$stock->getGenotype()));
                    $stock->setGenotype(str_replace('< / sup>','',$stock->getGenotype()));
                    $stock->setVerified($verified);
                    $notes = '';
                    if (($comment != '')&&($comment != '\N')) {
                        $notes .= $comment;
                    }
                    if (($chromosome != '')&&($chromosome != '\N')) {
                        $delim = ($notes != '') ? "\n" : '';
                        $notes .= $delim . 'affected chromosome: ' . trim(str_replace('on','',$chromosome));
                    }
                    if (($reference != '')&&($reference != '\N')) {
                        $delim = ($notes != '') ? "\n" : '';
                        $notes .= $delim . 'reference: ' . $reference;
                    }
                    if ($notes != '') {
                        $stock->setNotes($notes);
                    }
                    if (($vendor != '')&&($vendor != '\N')) {
                        $stock->setVendor($vendor);
                    }
                    if (($url != '')&&($url != '\N')) {
                        $stock->setInfoURL($url);
                    }
                 
                    $qb = $om->getRepository('VIB\FliesBundle\Entity\Stock')->createQueryBuilder('b');
                    $qb->where('b.name like :term_1')
                       ->orWhere('b.name like :term_2')
                       ->orWhere('b.name like :term_3')
                       ->setParameter('term_1', $projectAcronym . $numberInProject)
                       ->setParameter('term_2', $projectAcronym . ' ' . $numberInProject)
                       ->setParameter('term_3', $stock->getName());
                    
                    $result = $qb->getQuery()->getResult();
                    
                    if (count($result)) {
                        if (count($result == 1)) {
                            $existing = $result[0];
                            $owner = $om->getOwner($existing);
                            $message = 'Stock ' . $stock->getName() . ' already exists (as '
                                                . $existing->getName() . ' owned by ' . $owner . '). Merge?';
                            if ($dialog->askConfirmation($output, '<question>' . $message . '</question>', true)) {
                                if ($stock->getGenotype() != $existing->getGenotype()) {
                                    $message = 'Overwrite existing genotype ' . $existing->getGenotype() .
                                           ' with ' . $stock->getGenotype() . '?';
                                    if ($dialog->askConfirmation($output, '<question>' .
                                            $message . '</question>', true)) {
                                        $existing->setGenotype($stock->getGenotype());
                                    }
                                }
                                if ($existing->getNotes() != '') {
                                    $existing->setNotes($existing->getNotes() . "\n" . $stock->getNotes());
                                } else {
                                    $existing->setNotes($stock->getNotes());
                                }
                                $existing->setName($stock->getName());
                                $existing->setVerified($stock->isVerified());
                                $existing->setVendor($stock->getVendor());
                                $existing->setInfoURL($stock->getInfoURL());
                                $om->persist($existing);
                                
                                $vial = new StockVial();
                                $vial->setStock($existing);
                                $vm->persist($vial);
                                $vials->add($vial);
                                fprintf($logfile,"%s\n",$existing->getName());
                            } else {
                                $om->persist($stock);
                                $stocks->add($stock);
                                fprintf($logfile,"%s\n",$stock->getName());
                                foreach ($stock->getVials() as $vial) {
                                    $vials->add($vial);
                                }
                            }
                        }
                    } else {
                        $om->persist($stock);
                        $stocks->add($stock);
                        fprintf($logfile,"%s\n",$stock->getName());
                        foreach ($stock->getVials() as $vial) {
                            $vials->add($vial);
                        }
                    }
                }
            }
        }
        $output->writeln("Objects imported. Flushing DB buffer.");
        $om->flush(); 
        $output->writeln("Creating ACLs.");
        $om->createACL($stocks,$this->getDefaultACL($user));
        $vm->createACL($vials,$this->getDefaultACL($user));
        $message = 'Everything seems to be OK. Commit?';
        if ($dialog->askConfirmation($output, '<question>' . $message . '</question>', true)) {
            $em->getConnection()->commit();
        } else {
            $em->getConnection()->rollback();
            $em->close();
        }
    }
    
    protected function getDefaultACL($user)
    {
        return array(
            array('identity' => $user,
                  'permission' => MaskBuilder::MASK_OWNER),
            array('identity' => 'ROLE_USER',
                  'permission' => MaskBuilder::MASK_VIEW));
    }
}
