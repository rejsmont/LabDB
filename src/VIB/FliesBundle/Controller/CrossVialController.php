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

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use VIB\FliesBundle\Form\CrossVialType;
use VIB\FliesBundle\Form\CrossVialNewType;

use VIB\FliesBundle\Entity\CrossVial;


/**
 * StockVialController class
 * 
 * @Route("/crosses")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class CrossVialController extends VialController
{
    
    /**
     * Construct CrossVialController
     */ 
    public function __construct() {
        $this->entityClass = 'VIB\FliesBundle\Entity\CrossVial';
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getCreateForm() {
        return new CrossVialNewType();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getEditForm() {
        return new CrossVialType();
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityName() {
        return "cross";
    }
    
    /**
     * Create cross
     * 
     * @Route("/new")
     * @Template()
     * 
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction() {
        
        $cross = new CrossVial();
        $data = array('cross' => $cross, 'number' => 1);
        
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm($this->getCreateForm(), $data);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $data = $form->getData();
                $cross = $data['cross'];
                $number = $data['number'];
                
                $crosses = new ArrayCollection();
                
                for ($i = 0; $i < $number; $i++) {
                    $newcross = new CrossVial($cross);
                    $em->persist($newcross);
                    $crosses->add($newcross);
                }
                $em->flush();
                
                foreach($crosses as $newcross) {
                    $this->setACL($newcross);
                }
                
                $count = count($crosses);
        
                if ($count == 1) {
                    $this->get('session')->getFlashBag()
                         ->add('success', 'Cross ' . $cross->getName() . ' was created.');
                } else {
                    $this->get('session')->getFlashBag()
                         ->add('success', $count . ' crosses ' . $cross->getName() . ' were created.');
                }
                
                $shouldPrint = $this->get('request')->getSession()->get('autoprint') == 'enabled';
                
                if ($shouldPrint) {
                    $pdf = $this->get('vibfolks.pdflabel');
                    
                    foreach($crosses as $cross) {
                        $pdf->addFlyLabel($cross->getId(), $cross->getSetupDate(),
                                          $cross->getLabelText(), $this->getOwner($cross));
                    }
                    if ($this->submitPrintJob($pdf, count($crosses))) {
                        foreach($crosses as $cross) {
                            $cross->setLabelPrinted(true);
                            $em->persist($cross);
                        }
                        $em->flush();
                    }
                }
                
                $url = $number == 1 ? 
                    $this->generateUrl('vib_flies_crossvial_show',array('id' => $cross->getId())) : 
                    $this->generateUrl('vib_flies_crossvial_list');

                return $this->redirect($url);
            }
        }
        
        return array('form' => $form->createView());
    }
    
    /**
     * Statistics for cross
     * 
     * @Route("/stats/{id}")
     * @Template()
     * 
     * @param mixed $id
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function statsAction($id) {
        $cross = $this->getEntity($id);
        $owner = $this->getOwner($cross);
        
        $startDate = clone $cross->getSetupDate();
        $stopDate = clone $cross->getSetupDate();
        $startDate->sub(new \DateInterval('P2W'));
        $stopDate->add(new \DateInterval('P2W'));
        
        $query = $this->getListQuery();
        $query->andWhere('b.maleName = :male_name')
              ->andWhere('b.virginName = :virgin_name')
              ->andWhere('b.setupDate > :start_date')
              ->andWhere('b.setupDate <= :stop_date')
              ->orderBy('b.setupDate', 'ASC')
              ->addOrderBy('b.id', 'ASC')
              ->setParameter('male_name', $cross->getUnformattedMaleName())
              ->setParameter('virgin_name', $cross->getUnformattedVirginName())
              ->setParameter('start_date', $startDate->format('Y-m-d'))
              ->setParameter('stop_date', $stopDate->format('Y-m-d'));
        
        $total = $this->get('vib.security.helper.acl')->apply($query,array('OWNER'),$owner)->getResult();
        $sterile = new ArrayCollection();
        $success = new ArrayCollection();
        $fail = new ArrayCollection();
        $ongoing =  new ArrayCollection();
        $stocks = new ArrayCollection();
        $crosses = new ArrayCollection();
        $temps = new ArrayCollection();
        
        if (count($total) == 0) {
            throw $this->createNotFoundException();
        }
        
        foreach ($total as $vial) {
            $temp = $vial->getTemperature();
            if (! $temps->contains($temp)) {
                $temps->add($temp);
            }
            if ($vial->isSterile()) {
                $sterile->add($vial);
            } elseif ($vial->isSuccessful() === true) {
                $success->add($vial);
                foreach ($vial->getStocks() as $childStock) {
                    if (! $stocks->contains($childStock)) {
                        $stocks->add($childStock);
                    }
                }
                foreach ($vial->getCrosses() as $childCross) {
                    if (! $crosses->contains($childCross)) {
                        $crosses->add($childCross);
                    }
                }
            } elseif ($vial->isSuccessful() === false) {
                $fail->add($vial);
            } else {
                $ongoing->add($vial);
            }
            
        }
        
        return array('cross' => $cross,
                     'total' => $total,
                     'sterile' => $sterile,
                     'fail' => $fail,
                     'success' => $success,
                     'ongoing' => $ongoing,
                     'stocks' => $stocks,
                     'crosses' => $crosses,
                     'temps' => $temps);
    }
    
    /**
     * {@inheritdoc}
     */
    public function handleBatchAction($data) {
        
        $action = $data['action'];
        $vials = new ArrayCollection($data['items']);
        $response = $this->getDefaultBatchResponse();
        
        switch($action) {
            case 'marksterile':
                $this->markSterile($vials);
                break;
            case 'marksuccessful':
                $this->markSuccessful($vials);
                break;
            case 'markfailed':
                $this->markFailed($vials);
                break;
            default:
                return parent::handleBatchAction($data);
        }
        
        return $response;
    }
    
    /**
     * Mark crosses as sterile and trash them
     * 
     * @param \Doctrine\Common\Collections\Collection $vials
     */  
    public function markSterile(Collection $vials) {
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ($vials as $vial) {
            if ($vial instanceof CrossVial) {
                $vial->setSterile(true);
                $em->persist($vial);
            }
        }
        
        $em->flush();
        
        $count = count($vials);
        
        if ($count == 1) {
            $this->get('session')->getFlashBag()
                 ->add('success', '1 cross was marked as sterile and trashed.');
        } else {
            $this->get('session')->getFlashBag()
                 ->add('success', $count . ' crosses were marked as sterile and trashed.');
        }
    }
    
    /**
     * Mark crosses as successful
     * 
     * @param \Doctrine\Common\Collections\Collection $vials
     */  
    public function markSuccessful(Collection $vials) {
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ($vials as $vial) {
            if ($vial instanceof CrossVial) {
                $vial->setSuccessful(true);
                $em->persist($vial);
            }
        }

        $em->flush();
        
        $count = count($vials);
        
        if ($count == 1) {
            $this->get('session')->getFlashBag()
                 ->add('success', '1 cross was marked as successful.');
        } else {
            $this->get('session')->getFlashBag()
                 ->add('success', $count . ' crosses were marked as successful.');
        }
    }
    
    /**
     * Mark crosses as successful
     * 
     * @param \Doctrine\Common\Collections\Collection $vials
     */  
    public function markFailed(Collection $vials) {
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ($vials as $vial) {
            if ($vial instanceof CrossVial) {
                $vial->setSuccessful(false);
                $em->persist($vial);
            }
        }

        $em->flush();
        
        $count = count($vials);
        
        if ($count == 1) {
            $this->get('session')->getFlashBag()
                 ->add('success', '1 cross was marked as failed.');
        } else {
            $this->get('session')->getFlashBag()
                 ->add('success', $count . ' crosses were marked as failed.');
        }
    }
}
