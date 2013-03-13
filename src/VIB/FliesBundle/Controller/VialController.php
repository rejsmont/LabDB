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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\SatisfiesParentSecurityPolicy;

use Symfony\Component\Form\AbstractType;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use VIB\BaseBundle\Controller\CRUDController;

use VIB\FliesBundle\Utils\PDFLabel;

use VIB\FliesBundle\Form\VialType;
use VIB\FliesBundle\Form\VialExpandType;
use VIB\FliesBundle\Form\SelectType;

use VIB\FliesBundle\Entity\Vial;


/**
 * VialController class
 *
 * @Route("/vials")
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class VialController extends CRUDController {
    
    /**
     * Construct StockVialController
     * 
     */
    public function __construct()
    {
        $this->entityClass  = 'VIB\FliesBundle\Entity\Vial';
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getEditForm() {
        return new VialType();
    }
    
        
    /**
     * List vials
     * 
     * @Route("/")
     * @Route("/list/{filter}")
     * @Template()
     * @SatisfiesParentSecurityPolicy
     * 
     * @param integer $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction($filter = null)
    {
        $response = parent::listAction($filter);
        $formResponse = $this->handleSelectForm(new SelectType('VIB\FliesBundle\Entity\Vial'));
        
        return is_array($formResponse) ? array_merge($response, $formResponse) : $formResponse;
    }
    
    /**
     * Filter query
     * 
     * @param \Doctrine\ORM\QueryBuilder $query
     * @param string $filter
     * @return \Doctrine\ORM\Query
     */
    public function applyFilter($query, $filter = null)
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P2M'));
        
        switch($filter) {
            case 'public':
            case 'all':
                $query = $query->where('b.setupDate > :date')
                               ->andWhere('b.trashed = false')
                               ->orderBy('b.setupDate', 'DESC')
                               ->addOrderBy('b.id', 'DESC')
                               ->setParameter('date', $date->format('Y-m-d'));
                if ($this->getUser() !== null) {
                    return $this->get('vib.security.helper.acl')->apply($query);
                } else {
                    return $query->getQuery();
                }
                break;
            case 'trashed':
                $query = $query->where('b.trashed = true')
                               ->orderBy('b.setupDate', 'DESC')
                               ->addOrderBy('b.id', 'DESC');
                if ($this->getUser() !== null) {
                    return $this->get('vib.security.helper.acl')->apply($query);
                } else {
                    return $query->getQuery();
                }                
                break;
            case 'dead':
                $query = $query->where('b.setupDate < :date')
                               ->orderBy('b.setupDate', 'DESC')
                               ->addOrderBy('b.id', 'DESC')
                               ->setParameter('date', $date->format('Y-m-d'));
                if ($this->getUser() !== null) {
                    return $this->get('vib.security.helper.acl')->apply($query);
                } else {
                    return $query->getQuery();
                }                
                break;
            default:
                $query = $query->where('b.setupDate > :date')
                               ->andWhere('b.trashed = false')
                               ->orderBy('b.setupDate', 'DESC')
                               ->addOrderBy('b.id', 'DESC')
                               ->setParameter('date', $date->format('Y-m-d'));
                if ($this->getUser() !== null) {
                    return $this->get('vib.security.helper.acl')->apply($query,array('OWNER'));
                } else {
                    return $query->getQuery();
                }                
                break;
        }
    }
    
    /**
     * Show vial
     * 
     * @Route("/show/{id}")
     * @Template()
     * 
     * @param mixed $id
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id) {
        $vial = $this->getEntity($id);
        if ($this->controls($vial)) {
            return parent::showAction($vial);
        } else {
            return $this->getVialRedirect($vial);
        }
    }

    /**
     * Create vial
     * 
     * @Route("/new")
     * @Template()
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction() {
        $em = $this->getDoctrine()->getManager();
        $class = $this->getEntityClass();
        $vial = new $class();
        $form = $this->createForm($this->getCreateForm(), $vial);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $shouldPrint = $this->get('request')->getSession()->get('autoprint') == 'enabled';
                
                if ($shouldPrint) {
                    $pdf = $this->get('vibfolks.pdflabel');
                    $pdf->addFlyLabel($vial->getId(), $vial->getSetupDate(), $vial->getLabelText());
                    if ($this->submitPrintJob($pdf)) {
                        $vial->setLabelPrinted(true);
                    }
                }
                
                $em->persist($vial);
                $em->flush();

                $this->setACL($vial);
                
                $route = str_replace("_create", "_show", $request->attributes->get('_route'));
                $url = $this->generateUrl($route,array('id' => $vial->getId()));
                return $this->redirect($url);
            }
        }
        return array('form' => $form->createView());
    }
    
    /**
     * Edit vial
     * 
     * @Route("/edit/{id}")
     * @Template()
     * 
     * @param mixed $id
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id) {
        $vial = $this->getEntity($id);
        if ($this->controls($vial)) {
            return parent::editAction($vial);
        } else {
            return $this->getVialRedirect($vial);
        }
    }
    
    /**
     * Expand vial
     * 
     * @Route("/expand/{id}", defaults={"id" = null})
     * @Template()
     * 
     * @param mixed $id
     * 
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function expandAction($id = null) {

        $em = $this->getDoctrine()->getManager();
        $source = (null !== $id) ? $this->getEntity($id) : null;
        $data = array('source' => $source, 'number' => 1);        
        $form = $this->createForm(new VialExpandType(), $data);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $data = $form->getData();
                $source = $data['source'];
                $number = $data['number'];
                $shouldPrint = $this->get('request')->getSession()->get('autoprint') == 'enabled';
                
                $vials = new ArrayCollection();
                
                if ($shouldPrint) {
                    $pdf = $this->get('vibfolks.pdflabel');
                }
                
                for ($i = 0; $i < $number; $i++) {
                    $vial = $source->flip();
                    if ($shouldPrint) {
                        $pdf->addFlyLabel($vial->getId(), $vial->getSetupDate(), $vial->getLabelText());
                    }
                    $vials->add($vial);
                }
                
                if ($shouldPrint) {
                    $printResult = $this->submitPrintJob($pdf, count($vials));
                } else {
                    $printResult = false;
                }
                
                foreach($vials as $vial) {
                    if ($printResult) {
                        $vial->setLabelPrinted(true);
                    }
                    $em->persist($vial);
                }
                $em->flush();
                
                foreach($vials as $vial) {
                    $this->setACL($vial);
                }
                
                $route = str_replace("_expand", "_list", $request->attributes->get('_route'));
                $url = $this->generateUrl($route);
                return $this->redirect($url);
            }
        }
        
        return array('form' => $form->createView(), 'cancel' => 'vib_flies_vial_list');
    }
    
    /**
     * Select vials
     * 
     * @Route("/select")
     * @Template()
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function selectAction() {
        $response = array();
        $formResponse = $this->handleSelectForm(new SelectType('VIB\FliesBundle\Entity\Vial'));
        
        return is_array($formResponse) ? array_merge($response, $formResponse) : $formResponse;
    }
    
    /**
     * Handle batch action
     * 
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleBatchAction($data) {
        
        $action = $data['action'];
        $vials = $data['items'];
        
        $response = $this->getDefaultBatchResponse();
        
        switch($action) {
            case 'label':
                $response = $this->downloadLabels($vials);
                break;
            case 'print':
                $this->printLabels($vials);
                break;
            case 'flip':
                $this->flipVials($vials);
                break;
            case 'fliptrash':
                $this->flipVials($vials,true);
                break;
            case 'trash':
                $this->trashVials($vials);
                break;
        }
        
        return $response;
    }
    
    /**
     * Handle selection form
     * 
     * @param \Symfony\Component\Form\AbstractType $formType
     * @return array|\Symfony\Component\HttpFoundation\Response
     */   
    public function handleSelectForm(AbstractType $formType) {
        
        $form = $this->createForm($formType);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                return $this->handleBatchAction($form->getData());
            }
        }
        
        return array('form' => $form->createView());
    }
    
    /**
     * Generate vial labels
     * 
     * @param mixed $vials
     * @return \Symfony\Component\HttpFoundation\Response
     */    
    public function downloadLabels($vials) {
        $em = $this->getDoctrine()->getManager();
        $pdf = $this->prepareLabels($vials);
        $isVial = ($vials instanceof Vial);
        if ((($vials instanceof Collection)&&(count($vials) > 0))||($isVial)) { 
            $pdf = $this->prepareLabels($vials);
            if ($isVial) {
                $vial = $vials;
                $vial->setLabelPrinted(true);
                $em->persist($vial);
            } else {
                foreach ($vials as $vial) {
                    $vial->setLabelPrinted(true);
                    $em->persist($vial);
                }
            }
            $em->flush();
        }
        return $pdf->output();
    }
    
    /**
     * Prepare vial labels
     * 
     * @param mixed $vials
     * @return \Symfony\Component\HttpFoundation\Response
     */    
    public function prepareLabels($vials) {
        $pdf = $this->get('vibfolks.pdflabel');
        if ($vials instanceof Collection) {
            foreach ($vials as $vial) {
                $pdf->addFlyLabel($vial->getId(), $vial->getSetupDate(), $vial->getLabelText());
            }
        } elseif ($vials instanceof Vial) {
            $vial = $vials;
            $pdf->addFlyLabel($vial->getId(), $vial->getSetupDate(), $vial->getLabelText());
        }
        return $pdf;
    }
    
    /**
     * Print vial labels
     * 
     * @param $mixed $vials
     * @return boolean
     */    
    public function printLabels($vials) {
        $em = $this->getDoctrine()->getManager();
        $isVial = ($vials instanceof Vial);
        if ((($vials instanceof Collection)&&(($count = count($vials)) > 0))||($isVial)) { 
            $pdf = $this->prepareLabels($vials);
            if ($this->submitPrintJob($pdf, $isVial ? 1 : $count)) {
                if ($isVial) {
                    $vial = $vials;
                    $vial->setLabelPrinted(true);
                    $em->persist($vial);
                } else {
                    foreach ($vials as $vial) {
                        $vial->setLabelPrinted(true);
                        $em->persist($vial);
                    }
                }
                $em->flush();
            }
        }
    }
    
    /**
     * Submit print job
     * 
     * @param VIB\FliesBundle\Utils\PDFLabel $pdf
     * @param integer $count
     * @return boolean
     */
    public function submitPrintJob(PDFLabel $pdf, $count = 1) {
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
     * Flip vials
     * 
     * @param \Doctrine\Common\Collections\Collection $vials
     * @param boolean $trash
     */     
    public function flipVials(Collection $vials, $trash = false) {
        
        $em = $this->getDoctrine()->getManager();
        $shouldPrint = $this->get('request')->getSession()->get('autoprint') == 'enabled';
        
        $flippedVials = new ArrayCollection();
        
        if ($shouldPrint) {
            $pdf = $this->get('vibfolks.pdflabel');
        }
        
        foreach ($vials as $source) {
            $vial = $source->flip();
            if ($trash) {
                $vial->setPosition($source->getPosition());
                $source->setTrashed(true);
                $em->persist($source);
            }
            if ($shouldPrint) {
                $pdf->addFlyLabel($vial->getId(), $vial->getSetupDate(), $vial->getLabelText());
            }
            $flippedVials->add($vial);
        }
        
        if ($shouldPrint) {
            $printResult = $this->submitPrintJob($pdf, count($vials));
        } else {
            $printResult = false;
        }

        foreach($flippedVials as $vial) {
            if ($printResult) {
                $vial->setLabelPrinted(true);
            }
            $em->persist($vial);
        }
        $em->flush();
        
        foreach ($flippedVials as $vial) {
            parent::setACL($vial);
        }
    }
    
    /**
     * Trash vials
     * 
     * @param \Doctrine\Common\Collections\Collection $vials
     */  
    public function trashVials(Collection $vials) {
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ($vials as $vial) {
            $vial->setTrashed(true);
            $em->persist($vial);
        }
        
        $em->flush();
    }
    
    /**
     * Get default batch action response
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getDefaultBatchResponse() {
        $request = $this->getRequest();
        $currentRoute = $request->attributes->get('_route');
        
        if ($currentRoute == '') {
            $url = $this->generateUrl('vib_flies_vial_list');
            return $this->redirect($url);
        }
        
        $pieces = explode('_',$currentRoute);
        
        if (is_numeric($pieces[count($pieces) - 1])) {
            array_pop($pieces);
        }
        $pieces[count($pieces) - 1] = 'list';
        $route = ($currentRoute == 'default') ? 'default' : implode('_', $pieces);
        $url = $this->generateUrl($route);
        return $this->redirect($url);
    }
    
    /**
     * 
     * @param \VIB\FliesBundle\Entity\Vial $vial
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function getVialRedirect(Vial $vial) {
        $request = $this->getRequest();
        $route = str_replace("_vial_", "_" . $vial->getType() . "vial_", $request->attributes->get('_route'));
        $url = $this->generateUrl($route, array('id' => $vial->getId()));
        return $this->redirect($url);
    }
}
