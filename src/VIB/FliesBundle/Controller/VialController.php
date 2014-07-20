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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use VIB\CoreBundle\Controller\CRUDController;
use VIB\CoreBundle\Form\AclType;
use VIB\CoreBundle\Filter\RedirectFilterInterface;

use VIB\FliesBundle\Filter\VialFilter;
use VIB\FliesBundle\Label\PDFLabel;

use VIB\FliesBundle\Form\VialType;
use VIB\FliesBundle\Form\VialNewType;
use VIB\FliesBundle\Form\VialExpandType;
use VIB\FliesBundle\Form\SelectType;
use VIB\FliesBundle\Form\VialGiveType;
use VIB\FliesBundle\Form\BatchVialType;
use VIB\FliesBundle\Form\BatchVialAclType;

use VIB\FliesBundle\Entity\Vial;
use VIB\FliesBundle\Entity\Incubator;

/**
 * VialController class
 *
 * @Route("/vials")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class VialController extends CRUDController
{
    /**
     * Construct StockVialController
     *
     */
    public function __construct()
    {
        $this->entityClass  = 'VIB\FliesBundle\Entity\Vial';
        $this->entityName   = 'vial|vials';
    }

    /**
     * Get object manager
     *
     * @return \VIB\FliesBundle\Doctrine\VialManager
     */
    protected function getObjectManager()
    {
        return $this->get('vib.doctrine.vial_manager');
    }

    /**
     * {@inheritdoc}
     */
    protected function getCreateForm()
    {
        return new VialNewType();
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditForm()
    {
        return new VialType();
    }

    /**
     * List vials
     *
     * @Route("/")
     * @Route("/list/{access}/{filter}", defaults={"access" = "private", "filter" = "living"})
     * @Route("/list/{access}/{filter}/sort/{sorting}", defaults={"access" = "private", "filter" = "living", "sorting" = "setup"})
     * @Template()
     * @SatisfiesParentSecurityPolicy
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $response = parent::listAction();
        $formResponse = $this->handleSelectForm(new SelectType($this->getEntityClass()));

        if ($response instanceof Response) {
            
            return $response;
        } 
        
        if ($formResponse instanceof Response) {
            
            return $formResponse;
        } 
        
        return ((is_array($response))&&(is_array($formResponse))) ?
            array_merge($response, $formResponse) : $response;
    }

    /**
     * Show vial
     *
     * @Route("/show/{id}")
     * @Template()
     *
     * @param  mixed                                      $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
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
     * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $om = $this->getObjectManager();
        $class = $this->getEntityClass();

        if ($class == 'VIB\FliesBundle\Entity\Vial') {
            throw $this->createNotFoundException();
        }

        $vial = new $class();
        $data = array('vial' => $vial, 'number' => 1);
        $form = $this->createForm($this->getCreateForm(), $data);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $vial = $data['vial'];
                $number = $data['number'];

                $vials = $om->expand($vial, $number, false);
                $om->flush();
                $om->createACL($vials,$this->getDefaultACL());

                $message = (($count = count($vials)) == 1) ?
                    ucfirst($this->getEntityName()) . ' ' . $vials[0] . ' was created.' :
                    ucfirst($count . ' ' . $this->getEntityPluralName()) . ' were created.';
                $this->addSessionFlash('success', $message);

                $this->autoPrint($vials);

                if ($count == 1) {
                    $route = str_replace("_create", "_show", $request->attributes->get('_route'));
                    $url = $this->generateUrl($route,array('id' => $vials[0]->getId()));
                } else {
                    $route = str_replace("_create", "_list", $request->attributes->get('_route'));
                    $url = $this->generateUrl($route);
                }

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
     * @param  mixed                                      $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
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
     * @param  mixed                                            $id
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function expandAction($id = null)
    {
        $om = $this->getObjectManager();
        $source = (null !== $id) ? $this->getEntity($id) : null;
        $data = array(
            'source' => $source,
            'number' => 1,
            'size' => null !== $source ? $source->getSize() : 'medium',
            'food' => null !== $source ? $source->getFood() : 'Normal'
        );
        $form = $this->createForm(new VialExpandType(), $data);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $source = $data['source'];
                $number = $data['number'];
                $size = $data['size'];
                $food = $data['food'];

                $vials = $om->expand($source, $number, true, $size, $food);
                $om->flush();
                
                foreach ($vials as $vial) {
                    $sourceVial = $vial->getParent();
                    $securityContext = $this->getSecurityContext();
                    $acl = ($securityContext->isGranted('OPERATOR', $sourceVial)) ?
                        $om->getACL($sourceVial) : $this->getDefaultACL();
                    $om->createACL($vial, $acl);
                }

                $message = (($count = count($vials)) == 1) ?
                    ucfirst($this->getEntityName()) . ' ' . $source . ' was flipped.' :
                    ucfirst($this->getEntityName()) . ' ' . $source . ' was expanded into ' . $count . ' vials.';
                $this->addSessionFlash('success', $message);

                $this->autoPrint($vials);

                $route = str_replace("_expand", "_list", $request->attributes->get('_route'));
                $url = $this->generateUrl($route);

                return $this->redirect($url);
            }
        }

        return array('form' => $form->createView(), 'cancel' => 'vib_flies_vial_list');
    }
    
    /**
     * Expand vial
     *
     * @Route("/give/{id}", defaults={"id" = null})
     * @Template()
     *
     * @param  mixed                                            $id
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function giveAction($id = null)
    {
        $om = $this->getObjectManager();
        $securityContext = $this->getSecurityContext();
        $source = (null !== $id) ? $this->getEntity($id) : null;
        $data = array(
            'source' => $source,
            'user' => null,
            'type' => 'give',
            'size' => null !== $source ? $source->getSize() : 'medium',
            'food' => null !== $source ? $source->getFood() : 'Normal'
        );
        $form = $this->createForm(new VialGiveType(), $data);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $source = $data['source'];
                $user = $data['user'];
                $type = $data['type'];
                $size = $data['size'];
                $food = $data['food'];
                
                if (!($securityContext->isGranted('OWNER', $source)||$securityContext->isGranted('ROLE_ADMIN'))) {
                    throw new AccessDeniedException();
                }
                
                switch($type) {
                    case 'flip':
                        $vial = $om->flip($source);
                        $vial->setPosition($source->getPosition());
                        $vial->setSize($size);
                        $vial->setFood($food);
                        $om->persist($vial);
                        $om->persist($source);
                        break;
                    case 'flipped':
                        $vial = $om->flip($source);
                        $vial->setSize($size);
                        $vial->setFood($food);
                        $om->persist($vial);
                        break;
                }
                
                $om->flush();
                $vials = new ArrayCollection();
                
                switch($type) {
                    case 'flip':
                        $om->createACL($vial, $this->getDefaultACL());
                        $vials->add($vial);
                    case 'give':
                        $om->setOwner($source, $user);
                        $vials->add($source);
                        $given = $source;
                        break;
                    case 'flipped':
                        $om->createACL($vial, $this->getDefaultACL($user));
                        $vials->add($vial);
                        $given = $vial;
                        break;
                }
                
                $message = ($type != 'give') ?
                    ucfirst($this->getEntityName()) . ' ' . $source . ' was flipped into '
                        . $this->getEntityName() . ' ' . $vial . ". " : '';
                $message .= 
                    ucfirst($this->getEntityName()) . ' ' . $given . ' was given to ' . $user->getFullName() . '.';
                $this->addSessionFlash('success', $message);

                $this->autoPrint($vials);
                
                $route = str_replace("_give", "_list", $request->attributes->get('_route'));
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
    public function selectAction()
    {
        $response = array();
        $formResponse = $this->handleSelectForm(new SelectType('VIB\FliesBundle\Entity\Vial'));

        return is_array($formResponse) ? array_merge($response, $formResponse) : $formResponse;
    }

    /**
     * Handle batch action
     *
     * @param  array                                      $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleBatchAction($data)
    {
        $action = $data['action'];
        $vials = new ArrayCollection($data['items']);
        $response = $this->getDefaultBatchResponse();

        switch ($action) {
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
                $response = $this->getBackBatchResponse();
                break;
            case 'untrash':
                $this->untrashVials($vials);
                break;
            case 'incubate':
                $incubator = $data['incubator'];
                $this->incubateVials($vials, $incubator);
                break;
            case 'edit':
                $response = $this->editVials($vials);
                break;
            case 'permissions':
                $response = $this->permissionsVials($vials);
                break;
        }

        return $response;
    }

    /**
     * Handle selection form
     *
     * @param  \Symfony\Component\Form\AbstractType             $formType
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    protected function handleSelectForm(AbstractType $formType)
    {
        $form = $this->createForm($formType);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                
                return $this->handleBatchAction($form->getData());
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * Prepare vial labels
     *
     * @param  mixed                                      $vials
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function prepareLabels($vials)
    {
        $labelMode = ($this->getSession()->get('labelmode','std') == 'alt');
        $pdf = $this->get('vibfolks.pdflabel');
        $pdf->addLabel($vials, $labelMode);

        return $pdf;
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
                $this->addSessionFlash('success', 'Label for 1 vial was sent to the printer.');
            } else {
                $this->addSessionFlash('success', 'Labels for ' . $count . ' vials were sent to the printer.');
            }

            return true;
        } else {
            $this->addSessionFlash('danger', 'There was an error printing labels. The print server said: ' . $jobStatus);

            return false;
        }
    }

    /**
     * Generate vial labels
     *
     * @param  mixed                                      $vials
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function downloadLabels($vials)
    {
        $om = $this->getObjectManager();
        $count = ($vials instanceof Collection) ? count($vials) : (($vials instanceof Vial) ? 1 : 0);
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
    protected function printLabels($vials)
    {
        $om = $this->getObjectManager();
        $count = ($vials instanceof Collection) ? count($vials) : (($vials instanceof Vial) ? 1 : 0);
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
    protected function autoPrint($vials)
    {
        if ($this->getSession()->get('autoprint') == 'enabled') {
            $this->printLabels($vials);
        }
    }

    /**
     * Flip vials
     *
     * @param \Doctrine\Common\Collections\Collection $vials
     * @param boolean                                 $trash
     */
    public function flipVials(Collection $vials, $trash = false)
    {
        $om = $this->getObjectManager();
        $flippedVials = $om->flip($vials, true, $trash);
        $om->flush();
        
        foreach ($flippedVials as $flippedVial) {
            $sourceVial = $flippedVial->getParent();
            $securityContext = $this->getSecurityContext();
            $acl = ($securityContext->isGranted('OPERATOR', $sourceVial)) ?
                $om->getACL($sourceVial) : $this->getDefaultACL();
            $om->createACL($flippedVial, $acl);
        }
        
        if (($count = count($flippedVials)) == 1) {
            $this->addSessionFlash('success', '1 vial was flipped.' .
                                   ($trash ? ' Source vial was trashed.' : ''));
        } else {
            $this->addSessionFlash('success', $count . ' vials were flipped.' .
                                   ($trash ? ' Source vials were trashed.' : ''));
        }
        $this->autoPrint($flippedVials);
    }

    /**
     * Trash vials
     *
     * @param \Doctrine\Common\Collections\Collection $vials
     */
    public function trashVials(Collection $vials)
    {
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
    public function untrashVials(Collection $vials)
    {
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
     * @param \VIB\FliesBundle\Entity\Incubator       $incubator
     */
    public function incubateVials(Collection $vials, Incubator $incubator)
    {
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
     * Batch edit vials
     *
     * @Route("/batch/edit")
     * 
     * @param \Doctrine\Common\Collections\Collection $vials
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editVials(Collection $vials = null)
    {
        $om = $this->getObjectManager();
        $securityContext = $this->getSecurityContext();
        $template = array(
            'setupDate' => null,
            'flipDate' => null,
            'size' => null,
            'food' => null
        );
        $removed = 0;
        if (null !== $vials) {
            foreach ($vials as $vial) {
                if (!($securityContext->isGranted('ROLE_ADMIN')||$securityContext->isGranted('EDIT', $vial))) {
                    $vials->removeElement($vial);
                    $removed++;
                }
            }
        } else {
            $vials = new ArrayCollection();
        }
                
        if ($removed > 0) {
            if ($removed == 1) {
                $this->addSessionFlash('danger', 'You do not have sufficient permissions to edit 1 vial.'
                        . ' Changes will not apply to this vial.');
            } else {
                $this->addSessionFlash('danger', 'You do not have sufficient permissions to edit ' . $removed . ' vials.'
                        . ' Changes will not apply to these vials.');
            }
        }
                
        $data = array(
            'template' => $template,
            'vials' => $vials
        );
        
        $form = $this->createForm(new BatchVialType(), $data);
        $request = $this->getRequest();
        $action = 'editvials';
        
        if (($request->getMethod() == 'POST')&&(substr($request->get('_route'), -strlen($action)) === $action)) {
            $form->bind($request);
            if ($form->isValid()) {
                
                $data = $form->getData();
                $template = $data['template'];
                $vials = $data['vials'];
                
                foreach ($vials as $vial) {
                    if (null !== ($setupDate = $template['setupDate'])) {
                        $vial->setSetupDate($setupDate);
                    }
                    if (null !== ($flipDate = $template['flipDate'])) {
                        $vial->setStoredFlipDate($flipDate);
                    }
                    if (null !== ($size = $template['size'])) {
                        $vial->setSize($size);
                    }
                    if (null !== ($food = $template['food'])) {
                        $vial->setFood($food);
                    }
                    
                    $om->persist($vial);
                }
                
                $om->flush();
                
                if (($count = count($vials)) == 1) {
                    $this->addSessionFlash('success', 'Changes to 1 vial were saved.');
                } else {
                    $this->addSessionFlash('success', 'Changes to ' . $count . ' vials were saved.');
                }

                return $this->getDefaultBatchResponse();;
            }
        } else {
            if (count($vials) == 0) {
                $this->addSessionFlash('danger', 'There was nothing to edit.');

                return $this->getDefaultBatchResponse();;
            }
        }
        
        $pattern = "/Controller\\\([a-zA-Z]*)Controller/";
        $matches = array();
        preg_match($pattern, $request->get('_controller'), $matches);
        
        return $this->render('VIBFliesBundle:' . $matches[1] . ':batch_edit.html.twig', array('form' => $form->createView()));
    }
    
    /**
     * Batch change permissions for vials
     *
     * @Route("/batch/permissions")
     * 
     * @param \Doctrine\Common\Collections\Collection $vials
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function permissionsVials(Collection $vials = null)
    {
        $om = $this->getObjectManager();
        $acl_array = $this->getDefaultACL();
        $securityContext = $this->getSecurityContext();        
        $removed = 0;
        if (null !== $vials) {
            foreach ($vials as $vial) {
                if (!($securityContext->isGranted('ROLE_ADMIN')||$securityContext->isGranted('MASTER', $vial))) {
                    $vials->removeElement($vial);
                    $removed++;
                }
            }
        } else {
            $vials = new ArrayCollection();
        }
                
        if ($removed > 0) {
            if ($removed == 1) {
                $this->addSessionFlash('danger', 'You do not have sufficient permissions to change permissions'
                        . ' for 1 vial. Changes will not apply to this vial.');
            } else {
                $this->addSessionFlash('danger', 'You do not have sufficient permissions to change permissions'
                        . ' for ' . $removed . ' vials. Changes will not apply to these vials.');
            }
        }

        $acl = array(
            'user_acl' => array(),
            'role_acl' => array()
        );
                
        foreach($acl_array as $acl_entry) {
            $identity = $acl_entry['identity'];
            if ($identity instanceof UserInterface) {
                $acl['user_acl'][] = $acl_entry;
            } else if (is_string($identity)) {
                $acl['role_acl'][] = $acl_entry;
            }
        }
        
        $data = array(
            'acl' => $acl,
            'vials' => $vials
        );
        
        $form = $this->createForm(new \VIB\FliesBundle\Form\BatchVialAclType(), $data);
        
        $request = $this->getRequest();
        $action = 'permissionsvials';
        
        if (($request->getMethod() == 'POST')&&(substr($request->get('_route'), -strlen($action)) === $action)) {
            $form->bind($request);
            if ($form->isValid()) {
                
                $data = $form->getData();
                $acl = $data['acl'];
                $acl_array = array_merge($acl['user_acl'], $acl['role_acl']);
                $vials = $data['vials'];
                
                foreach ($vials as $vial) {
                    $om->updateACL($vial, $acl_array);
                }
                                
                if (($count = count($vials)) == 1) {
                    $this->addSessionFlash('success', 'Changes to 1 vial permissions were saved.');
                } else {
                    $this->addSessionFlash('success', 'Changes to ' . $count . ' vials permissions were saved.');
                }
                
                return $this->getDefaultBatchResponse();
            }
        } else {
            if (count($vials) == 0) {
                $this->addSessionFlash('danger', 'There was nothing to edit.');

                return $this->getDefaultBatchResponse();
            }
        }
        
        $pattern = "/Controller\\\([a-zA-Z]*)Controller/";
        $matches = array();
        preg_match($pattern, $request->get('_controller'), $matches);
        
        return $this->render('VIBFliesBundle:' . $matches[1] . ':batch_permissions.html.twig', array('form' => $form->createView()));        
    }
    
    /**
     * Get default batch action response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getDefaultBatchResponse()
    {
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
     * Get back to where batch job has started
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getBackBatchResponse()
    {
        $request = $this->getRequest();
        $currentRoute = $request->attributes->get('_route');
        $routeArguments = $request->attributes->get('_route_params', null);

        if ($currentRoute == '') {
            $url = $this->generateUrl('vib_flies_vial_list');

            return $this->redirect($url);
        }

        $pieces = explode('_',$currentRoute);
        
        if (in_array('select', $pieces)) {
            $pieces[count($pieces) - 1] = 'list';
        }
        
        $route = ($currentRoute == 'default') ? 'default' : implode('_', $pieces);
        $url = $this->generateUrl($route, $routeArguments);

        return $this->redirect($url);
    }

    /**
     *
     * @param  \VIB\FliesBundle\Entity\Vial               $vial
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function getVialRedirect(Vial $vial)
    {
        $request = $this->getRequest();
        $route = str_replace("_vial_", "_" . $vial->getType() . "vial_", $request->attributes->get('_route'));
        $url = $this->generateUrl($route, array('id' => $vial->getId()));

        return $this->redirect($url);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getFilterRedirect(RedirectFilterInterface $filter)
    {
        $request = $this->getRequest();
        $currentRoute = $request->attributes->get('_route');
        
        if ($currentRoute == '') {
            $route = 'vib_flies_vial_list_2';
        } else {
            $pieces = explode('_',$currentRoute);
            if (! is_numeric($pieces[count($pieces) - 1])) {
                $pieces[] = '2';
            }
            $route = ($currentRoute == 'default') ? 'vib_flies_vial_list_2' : implode('_', $pieces);
        }

        $routeParameters = ($filter instanceof VialFilter) ?
            array(
                'access' => $filter->getAccess(),
                'filter' => $filter->getFilter(),
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
        return new VialFilter($this->getRequest(), $this->getSecurityContext());
    }
}
