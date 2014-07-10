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
use JMS\SecurityExtraBundle\Annotation\SatisfiesParentSecurityPolicy;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Doctrine\Common\Collections\ArrayCollection;

use VIB\CoreBundle\Controller\CRUDController;
use VIB\CoreBundle\Filter\RedirectFilterInterface;

use VIB\FliesBundle\Label\PDFLabel;

use VIB\FliesBundle\Form\StockType;
use VIB\FliesBundle\Form\StockNewType;

use VIB\FliesBundle\Entity\Stock;
use VIB\FliesBundle\Entity\StockVial;

use VIB\FliesBundle\Filter\StockFilter;
use VIB\FliesBundle\Filter\VialFilter;

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
     * List stocks
     *
     * @Route("/")
     * @Route("/list/{access}", defaults={"access" = "owned"})
     * @Route("/list/{access}/sort/{sorting}", defaults={"access" = "mtnt", "sorting" = "name"})
     * @Template()
     * @SatisfiesParentSecurityPolicy
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        return parent::listAction();
    }
    
    /**
     * Show stock
     *
     * @Route("/show/{id}")
     * @Template()
     *
     * @param mixed $id
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $stock = $this->getEntity($id);
        $response = parent::showAction($stock);
        $om = $this->getObjectManager();
        
        $filter = new VialFilter(null, $this->getSecurityContext());
        $filter->setAccess('private');
        
        $myVials = $om->getRepository('VIB\FliesBundle\Entity\StockVial')
                      ->findLivingVialsByStock($stock, $filter);

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

        return is_array($response) ? array_merge($response, $vials) : $response;
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
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $stock = $data['stock'];
                $number = $data['number'];
                $size = $data['size'];
                $food = $data['food'];

                for ($i = 0; $i < $number - 1; $i++) {
                    $vial = new StockVial();
                    $stock->addVial($vial);
                }
                
                $vials = $stock->getVials();
                
                foreach ($vials as $vial) {
                    $vial->setSize($size);
                    $vial->setFood($food);
                }
                
                $om->persist($stock);
                $om->flush();
                $om->createACL($stock,$this->getDefaultACL());
                $vm->createACL($vials,$this->getDefaultACL());

                $this->addSessionFlash('success', 'Stock ' . $stock . ' was created.');

                if ($this->getSession()->get('autoprint') == 'enabled') {
                    $labelMode = ($this->getSession()->get('labelmode','std') == 'alt');
                    $pdf = $this->get('vibfolks.pdflabel');
                    $pdf->addLabel($vials, $labelMode);
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
    
    /**
     * {@inheritdoc}
     */
    protected function getFilterRedirect(RedirectFilterInterface $filter)
    {
        $request = $this->getRequest();
        $currentRoute = $request->attributes->get('_route');
        
        if ($currentRoute == '') {
            $route = 'vib_flies_stock_list_1';
        } else {
            $pieces = explode('_',$currentRoute);
            if (! is_numeric($pieces[count($pieces) - 1])) {
                $pieces[] = '2';
            }
            $route = ($currentRoute == 'default') ? 'vib_flies_vial_list_1' : implode('_', $pieces);
        }

        $routeParameters = ($filter instanceof StockFilter) ?
            array(
                'access' => $filter->getAccess(),
                'sorting' => $filter->getSort()) :
            array();
        
        $url = $this->generateUrl($route, $routeParameters);
        
        return $this->redirect($url);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getFilter()
    {
        return new StockFilter($this->getRequest(), $this->getSecurityContext());
    }
}
