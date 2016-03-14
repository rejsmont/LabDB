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
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

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
                'logfile',
                InputArgument::REQUIRED,
                'Log file'
            )
            ->addArgument(
                'listfile',
                InputArgument::OPTIONAL,
                'List of stocks to import'
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

        $listfilename = $input->getArgument('listfile');
        $importlist = array();
        if ($listfilename) {
            $listfile = fopen($listfilename,'r');
            if ($listfile) {
                while ($data = fgetcsv($listfile,0,"\t")) {
                    $importlist[] = $data[0];
                }
            }
        }

        $dialog = $this->getHelperSet()->get('dialog');
        $this->container = $this->getApplication()->getKernel()->getContainer();

        $connection = $this->container->get('database_connection');
        $om = $this->container->get('vib.doctrine.registry')->getManagerForClass('VIB\CoreBundle\Entity\Entity');
        $vm = $this->container->get('vib.doctrine.registry')->getManagerForClass('VIB\FliesBundle\Entity\StockVial');

        $om->disableAutoAcl();
        $vm->disableAutoAcl();
        
        $stocks = array();
        $stock_register = array();
        $vials = array();
        
        if ($file) {
            while ($data = fgetcsv($file,0,"\t")) {
                $trim = " \t\n\r\0\x0B\"";
                $owner_name = trim($data[0],$trim);
                $stock_name = trim($data[1],$trim);
                $stock_genotype = trim($data[2],$trim);
                $creator_name = trim($data[4],$trim);
                $stock_notes = trim($data[5],$trim);
                $stock_vendor = trim($data[6],$trim);
                $stock_vendor_id = trim($data[7],$trim);
                $stock_info_url = str_replace(" ","",trim($data[8],$trim));
                $stock_verified = trim($data[9],$trim) == "yes" ? true : false;
                $stock_vials_size = trim($data[10],$trim);
                $stock_vials_size = $stock_vials_size == "" ? 'medium' : $stock_vials_size;
                $stock_vials_number = (integer) trim($data[11],$trim);
                $stock_vials_number = $stock_vials_number <= 0 ? 1 : $stock_vials_number;
                $stock_vials_food = trim($data[12],$trim);
                $stock_vials_food = $stock_vials_food == "" ? 'standard' : $stock_vials_food;
                
                $test = $om->getRepository('VIB\FliesBundle\Entity\Stock')->findOneByName($stock_name);
                
                if ((!in_array($stock_name, $stock_register))&&($creator_name == "")&&(null === $test)) {
                    
                    if (($stock_vendor != "")&&($stock_vendor_id != "")) {
                        $output->write("Querying FlyBase for " . $stock_name . ": ");
                        $stock_data = $this->getStockData($stock_vendor, $stock_vendor_id);
                        if (count($stock_data) == 1) {
                            $stock_genotype = $stock_data[0]['stock_genotype'];
                            $stock_info_url = $stock_data[0]['stock_link'];
                            $output->writeln("success");
                        } elseif (count($stock_data) != 1) {
                            $output->writeln("failed");
                        }
                    }
                    
                    $stock = new Stock();
                    $stock->setName($stock_name);
                    $stock->setGenotype($stock_genotype);
                    $stock->setNotes($stock_notes);
                    $stock->setVendor($stock_vendor);
                    $stock->setVendorId($stock_vendor_id);
                    $stock->setInfoURL($stock_info_url);
                    $stock->setVerified($stock_verified);
                    
                    for ($i = 0; $i < $stock_vials_number - 1; $i++) {
                        $vial = new StockVial();
                        $stock->addVial($vial);
                    }
                    $stock_vials = $stock->getVials();
                    foreach ($stock_vials as $vial) {
                        $vial->setSize($stock_vials_size);
                        $vial->setFood($stock_vials_food);
                    }
                    
                    $stock_register[] = $stock_name;
                    $stocks[$owner_name][$stock_name] = $stock;
                } else {
                    $vials[$owner_name][$stock_name]['size'] = $stock_vials_size;
                    $vials[$owner_name][$stock_name]['number'] = $stock_vials_number;
                    $vials[$owner_name][$stock_name]['food'] = $stock_vials_food;
                }
            }
        }
        
        $connection->beginTransaction();
        
        foreach ($stocks as $user_name => $user_stocks) {
            
            try {
                $user = $this->container->get('user_provider')->loadUserByUsername($user_name);
            } catch (UsernameNotFoundException $e) {
                $user = null;
            }
            
            if ($user instanceof UserInterface) {
                $output->writeln("Adding stocks for user " . $user_name . ":");
                $userStocks = new ArrayCollection();
                $userVials = new ArrayCollection();
                foreach ($user_stocks as $stock_name => $stock) {
                    $om->persist($stock);
                    $userStocks->add($stock);
                    $userVials->add($stock->getVials());
                    $output->write(".");
                    fprintf($logfile,"%s\n",$stock->getName());
                }
                $om->flush();
                $output->writeln("");
                $output->write("Creating ACLs...");
                $om->createACL($userStocks, $user);
                $vm->createACL($userVials, $user);
                $output->writeln(" done");
            } else {
                $output->writeln("<error>User " . $user_name . " does not exits. Skipping!</error>");
            }
        }
        
        foreach ($vials as $user_name => $user_vials) {
            try {
                $user = $this->container->get('user_provider')->loadUserByUsername($user_name);
            } catch (UsernameNotFoundException $e) {
                $user = null;
            }
            
            if ($user instanceof UserInterface) {
                $output->writeln("Adding vials for user " . $user_name . ":");
                $userVials = new ArrayCollection();
                foreach ($user_vials as $stock_name => $stock_vials) {
                    $stock = $om->getRepository('VIB\FliesBundle\Entity\Stock')->findOneByName($stock_name);
                    if ($stock instanceof Stock) {
                        $stockVials = new ArrayCollection();
                        for ($i = 0; $i < $stock_vials['number']; $i++) {
                            $vial = new StockVial();
                            $stock->addVial($vial);
                            $stockVials->add($vial);
                            $userVials->add($vial);
                        }
                        foreach ($stockVials as $vial) {
                            $vial->setSize($stock_vials['number']);
                            $vial->setFood($stock_vials['number']);
                            $vm->persist($vial);
                        }
                        $output->write(".");
                    } else {
                        $output->write("?");
                    }
                }
                $output->writeln("");
                $vm->flush();
                $vm->createACL($userVials, $user);
            } else {
                $output->writeln("<error>User " . $user_name . " does not exits. Skipping!</error>");
            }
        }
        
        $message = 'Stocks and vials have been created. Commit?';
        if ($dialog->askConfirmation($output, '<question>' . $message . '</question>', true)) {
            $connection->commit();
        } else {
            $connection->rollback();
            $connection->close();
        }
        
        $om->enableAutoAcl();
        $vm->enableAutoAcl();
    }
    
    protected function getStockData($vendor, $stock)
    {
        $sql = <<<FLYBASE_SQL
    SELECT stockcollection.uniquename AS stock_center,
    stock.name AS stock_id,
    'http://flybase.org/reports/' || stock.uniquename || '.html' AS stock_link,
    genotype.uniquename AS stock_genotype
    FROM stock
    JOIN stock_genotype on stock.stock_id = stock_genotype.stock_id
    JOIN genotype on stock_genotype.genotype_id = genotype.genotype_id
    JOIN stockcollection_stock on stock.stock_id = stockcollection_stock.stock_id
    JOIN stockcollection on stockcollection_stock.stockcollection_id = stockcollection.stockcollection_id
    WHERE stock.name = :stock AND stockcollection.uniquename = :vendor
FLYBASE_SQL;
        $conn = $this->container->get('doctrine.dbal.flybase_connection');
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("stock", $stock);
        if ($vendor !== '') {
            $stmt->bindValue("vendor", $vendor);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
