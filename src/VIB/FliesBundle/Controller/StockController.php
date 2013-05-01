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

namespace VIB\FliesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Doctrine\Common\Collections\ArrayCollection;

use VIB\CoreBundle\Controller\CRUDController;

use VIB\FliesBundle\Label\PDFLabel;

use VIB\FliesBundle\Form\StockType;
use VIB\FliesBundle\Form\StockNewType;

use VIB\FliesBundle\Entity\Stock;
use VIB\FliesBundle\Entity\StockVial;

/**
 * StockController class
 *
 * @Route("/stocks")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class StockController extends CRUDController
{
    /**
     * Construct StockController
     *
     */
    public function __construct()
    {
        $this->entityClass = 'VIB\FliesBundle\Entity\Stock';
        $this->entityName  = 'stock|stocks';
    }

    /**
     * {@inheritdoc}
     */
    protected function getCreateForm()
    {
        return new StockNewType();
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditForm()
    {
        return new StockType();
    }

    /**
     * Show stock
     *
     * @Route("/show/{id}")
     * @Template()
     *
     * @param mixed $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $stock = $this->getEntity($id);
        $response = parent::showAction($stock);
        $om = $this->getObjectManager();
        $query =  $om->getRepository('VIB\FliesBundle\Entity\StockVial')->createQueryBuilder('b');
        $date = new \DateTime();
        $date->sub(new \DateInterval('P2M'));
        $query->where('b.stock = :stock')
              ->andWhere('b.setupDate > :date')
              ->andWhere('b.trashed = false')
              ->orderBy('b.setupDate', 'DESC')
              ->addOrderBy('b.id', 'DESC')
              ->setParameter('stock', $stock)
              ->setParameter('date', $date->format('Y-m-d'));

        $myVials = $this->get('vib.security.helper.acl')->apply($query,array('OWNER'))->getResult();

        $small = new ArrayCollection();
        $medium = new ArrayCollection();
        $large = new ArrayCollection();

        foreach ($myVials as $vial) {
            switch ($vial->getSize()) {
                case 'small':
                    $small->add($vial);
                    break;
                case 'medium':
                    $medium->add($vial);
                    break;
                case 'large':
                    $large->add($vial);
                    break;
            }
        }

        $vials = array('small' => $small, 'medium' => $medium, 'large' => $large);

        return is_array($response) ? array_merge($response,$vials) : $response;
    }

    /**
     * Create stock
     *
     * @Route("/new")
     * @Template()
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $om = $this->getObjectManager();
        $vm = $this->get('vib.doctrine.vial_manager');
        $class = $this->getEntityClass();
        $stock = new $class();
        $existingStock = null;
        $data = array('stock' => $stock, 'number' => 1, 'size' => 'medium');
        $form = $this->createForm($this->getCreateForm(), $data);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $stock = $data['stock'];
                $number = $data['number'];
                $size = $data['size'];

                for ($i = 0; $i < $number - 1; $i++) {
                    $vial = new StockVial();
                    $stock->addVial($vial);
                    $vial->setSize($size);
                }
                $om->persist($stock);
                $om->flush();
                $om->createACL($stock,$this->getDefaultACL());

                $vials = $stock->getVials();
                $vm->createACL($vials,$this->getDefaultACL());

                $this->addSessionFlash('success', 'Stock ' . $stock . ' was created.');

                if ($this->getSession()->get('autoprint') == 'enabled') {
                    $pdf = $this->get('vibfolks.pdflabel');
                    $pdf->addLabel($vials);
                    if ($this->submitPrintJob($pdf)) {
                        $vm->markPrinted($vials);
                        $vm->flush();
                    }
                }

                $route = str_replace("_create", "_show", $request->attributes->get('_route'));
                $url = $this->generateUrl($route,array('id' => $stock->getId()));

                return $this->redirect($url);
            } elseif ($stock instanceof Stock) {
                $existingStock = $om->getRepository($this->getEntityClass())
                                    ->findOneBy(array('name' => $stock->getName()));
            }
        }

        return array('form' => $form->createView(), 'existingStock' => $existingStock);
    }

    /**
     * Submit print job
     *
     * @param  VIB\FliesBundle\Utils\PDFLabel $pdf
     * @param  integer                        $count
     * @return boolean
     */
    protected function submitPrintJob(PDFLabel $pdf, $count = 1)
    {
        $jobStatus = $pdf->printPDF();
        if ($jobStatus == 'successfull-ok') {
            if ($count == 1) {
                $this->get('session')->getFlashBag()
                     ->add('success', 'Label for 1 vial was sent to the printer.');
            } else {
                $this->get('session')->getFlashBag()
                     ->add('success', 'Labels for ' . $count . ' vials were sent to the printer. ');
            }

            return true;
        } else {
            $this->get('session')->getFlashBag()
                 ->add('error', 'There was an error printing labels. The print server said: ' . $jobStatus);

            return false;
        }
    }
}
