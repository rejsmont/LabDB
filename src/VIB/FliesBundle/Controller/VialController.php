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

use VIB\FliesBundle\Label\PDFLabel;

use VIB\FliesBundle\Form\VialType;
use VIB\FliesBundle\Form\VialExpandType;
use VIB\FliesBundle\Form\SelectType;

use VIB\FliesBundle\Entity\Vial;
use VIB\FliesBundle\Entity\Incubator;


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
        $this->entityName   = 'vial';
    }
    
    /**
     * Get object manager
     * 
     * @return \VIB\FliesBundle\Doctrine\VialManager
     */
    protected function getObjectManager() {
        return $this->get('vib.doctrine.vial_manager');
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getEditForm() {
        return new VialType();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getListQuery() {
        return parent::getListQuery()->addOrderBy('b.setupDate','DESC')
                                     ->addOrderBy('b.id','DESC');
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
    public function listAction($filter = null) {
        $response = parent::listAction($filter);
        $formResponse = $this->handleSelectForm(new SelectType($this->getEntityClass()));
        return is_array($formResponse) ? array_merge($response, $formResponse) : $formResponse;
    }
    
    /**
     * Filter query
     * 
     * @param \Doctrine\ORM\QueryBuilder $query
     * @param string $filter
     * @return \Doctrine\ORM\Query
     */
    public function applyFilter($query, $filter = null) {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P2M'));
        $securityContext = $this->getSecurityContext();
        switch($filter) {
            case 'public':
                $query = $query->where('b.setupDate > :date')
                               ->andWhere('b.trashed = false')
                               ->setParameter('date', $date->format('Y-m-d'));
                if (($this->getUser() !== null)&&(! $securityContext->isGranted('ROLE_ADMIN'))) {
                    return $this->getAclFilter()->apply($query);
                } else {
                    return $query->getQuery();
                }
                break;
            case 'all':
                if (($this->getUser() !== null)&&(! $securityContext->isGranted('ROLE_ADMIN'))) {
                    return $this->getAclFilter()->apply($query);
                } else {
                    return $query->getQuery();
                }
                break;
            case 'trashed':
                $query = $query->where('b.setupDate > :date')
                               ->andWhere('b.trashed = true')
                               ->setParameter('date', $date->format('Y-m-d'));
                if ($this->getUser() !== null) {
                    return $this->getAclFilter()->apply($query,array('OWNER'));
                } else {
                    return $query->getQuery();
                }                
                break;
            case 'dead':
                $query = $query->where('b.setupDate <= :date')
                               ->orWhere('b.trashed = true')
                               ->setParameter('date', $date->format('Y-m-d'));
                if ($this->getUser() !== null) {
                    return $this->getAclFilter()->apply($query,array('OWNER'));
                } else {
                    return $query->getQuery();
                }                
                break;
            default:
                $query = $query->where('b.setupDate > :date')
                               ->andWhere('b.trashed = false')
                               ->setParameter('date', $date->format('Y-m-d'));
                if ($this->getUser() !== null) {
                    return $this->getAclFilter()->apply($query,array('OWNER'));
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
        $om = $this->getObjectManager();
        $class = $this->getEntityClass();
        $vial = new $class();
        $form = $this->createForm($this->getCreateForm(), $vial);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $om->persist($vial);
                $om->flush();
                $om->createACL($vial,$this->getDefaultACL());
                $message = 'Vial ' . $vial . ' was created.';
                $this->addSessionFlash('success', $message);
                $this->autoPrint($vial);
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
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function expandAction($id = null) {
        $om = $this->getObjectManager();
        $source = (null !== $id) ? $this->getEntity($id) : null;
        $data = array('source' => $source, 'number' => 1, 'size' => 'medium');        
        $form = $this->createForm(new VialExpandType(), $data);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $source = $data['source'];
                $number = $data['number'];
                $size = $data['size'];
                
                $vials = $om->expand($source, $number, true, $size);
                $om->flush();
                $om->createACL($vials, $this->getDefaultACL());
                
                if (($count = count($vials)) == 1) {
                    $this->addSessionFlash('success', 'Vial ' . $source . ' was flipped.');
                } else {
                    $this->addSessionFlash('success', 'Vial ' . $source . ' was expanded into ' . $count . ' vials.');
                }
                
                $this->autoPrint($vials);
                
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
    protected function handleBatchAction($data) {
        
        $action = $data['action'];
        $vials = new ArrayCollection($data['items']);
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
                $this->flipVials($vials, true);
                break;
            case 'trash':
                $this->trashVials($vials);
                break;
            case 'untrash':
                $this->untrashVials($vials);
                break;
            case 'incubate':
                $incubator = $data['incubator'];
                $this->incubateVials($vials, $incubator);
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
    protected function handleSelectForm(AbstractType $formType) {
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
     * Prepare vial labels
     * 
     * @param mixed $vials
     * @return \Symfony\Component\HttpFoundation\Response
     */    
    protected function prepareLabels($vials) {
        $pdf = $this->get('vibfolks.pdflabel');
        $pdf->addLabel($vials);
        return $pdf;
    }
    
    /**
     * Submit print job
     * 
     * @param VIB\FliesBundle\Utils\PDFLabel $pdf
     * @param integer $count
     * @return boolean
     */
    protected function submitPrintJob(PDFLabel $pdf, $count = 1) {
        $jobStatus = $pdf->printPDF();
        if ($jobStatus == 'successfull-ok') {
            if ($count == 1) {
                $this->addSessionFlash('success', 'Label for 1 vial was sent to the printer.');
            } else {
                $this->addSessionFlash('success', 'Labels for ' . $count . ' vials were sent to the printer.');
            }
            return true;
        } else {
            $this->addSessionFlash('error', 'There was an error printing labels. The print server said: ' . $jobStatus);
            return false;
        }
    }
    
    /**
     * Generate vial labels
     * 
     * @param mixed $vials
     * @return \Symfony\Component\HttpFoundation\Response
     */    
    protected function downloadLabels($vials) {
        $om = $this->getObjectManager();
        $count = ($vials instanceof Collection) ? count($vials) : ($vials instanceof Vial) ? 1 : 0;
        $pdf = $this->prepareLabels($vials);
        if ($count > 0) {
            $om->markPrinted($vials);
            $om->flush();
        } else {
            return $this->getDefaultBatchResponse();
        }
        return $pdf->outputPDF();
    }
    
    /**
     * Print vial labels
     * 
     * @param mixed $vials
     */    
    protected function printLabels($vials) {
        $om = $this->getObjectManager();
        $count = ($vials instanceof Collection) ? count($vials) : ($vials instanceof Vial) ? 1 : 0;
        $pdf = $this->prepareLabels($vials);
        if (($count > 0)&&($this->submitPrintJob($pdf, $count))) {
            $om->markPrinted($vials);
            $om->flush();
        }
    }
    
    /**
     * Automatically print vial labels if requested 
     * 
     * @param mixed $vials
     */
    protected function autoPrint($vials) {
        if ($this->getSession()->get('autoprint') == 'enabled') {
            $this->printLabels($vials);
        }
    }
    
    /**
     * Flip vials
     * 
     * @param \Doctrine\Common\Collections\Collection $vials
     * @param boolean $trash
     */     
    public function flipVials(Collection $vials, $trash = false) {
        $om = $this->getObjectManager();
        $flippedVials = $om->flip($vials, true, $trash);
        $om->flush();
        $om->createACL($flippedVials,$this->getDefaultACL());
        if (($count = count($flippedVials)) == 1) {
            $this->addSessionFlash('success', '1 vial was flipped.' .
                                   ($trash ? ' Source vial was trashed.' : ''));
        } else {
            $this->addSessionFlash('success', $count . ' vials were flipped.' .
                                   ($trash ? ' Source vials were trashed.' : ''));
        }
        $this->autoPrint($vials);
    }
    
    /**
     * Trash vials
     * 
     * @param \Doctrine\Common\Collections\Collection $vials
     */  
    public function trashVials(Collection $vials) {
        $om = $this->getObjectManager();
        $om->trash($vials);
        $om->flush();
        if (($count = count($vials)) == 1) {
            $this->addSessionFlash('success', '1 vial was trashed.');
        } else {
            $this->addSessionFlash('success', $count . ' vials were trashed.');
        }
    }
    
    /**
     * Untrash vials
     * 
     * @param \Doctrine\Common\Collections\Collection $vials
     */  
    public function untrashVials(Collection $vials) {
        $om = $this->getObjectManager();
        $om->untrash($vials);
        $om->flush();
        if (($count = count($vials)) == 1) {
            $this->addSessionFlash('success', '1 vial was removed from trash.');
        } else {
            $this->addSessionFlash('success', $count . ' vials were removed from trash.');
        }
    }
    
    /**
     * Incubate vials
     * 
     * @param \Doctrine\Common\Collections\Collection $vials
     * @param \VIB\FliesBundle\Entity\Incubator $incubator
     */  
    public function incubateVials(Collection $vials, Incubator $incubator) {
        $om = $this->getObjectManager();
        $om->incubate($vials, $incubator);
        $om->flush();
        if (($count = count($vials)) == 1) {
            $this->addSessionFlash('success', '1 vial was put in ' . $incubator . '.');
        } else {
            $this->addSessionFlash('success', $count . ' vials were put in ' . $incubator . '.');
        }
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
