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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use PHP_IPP\IPP\CupsPrintIPP;

/**
 * Description of AutoPrintController
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class PrintController extends Controller
{
    /**
     * Print panel
     *
     * @Template()
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function printPanelAction()
    {
        $ipp = new CupsPrintIPP();
        $ipp->setLog('', 0, 0);
        $ipp->setHost($this->container->getParameter('print_host'));
        $ipp->setPrinterURI($this->container->getParameter('print_queue'));
        $ipp->getPrinterAttributes();
        $canPrint = (implode('\n',$ipp->status) == 'successfull-ok');
        $setting = $this->get('request')->getSession()->get('autoprint') == 'enabled';
        $autoprint = ($canPrint) ? $setting : $canPrint;
        $labelmode = $this->get('request')->getSession()->get('labelmode');
        return array('autoprint' => $autoprint, 'labelmode' => $labelmode);
    }

    /**
     * Autoprint panel
     *
     * @Route("/ajax/autoprint/")
     * @Method("POST")
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function setAutoPrintAction(Request $request)
    {
        $ipp = new CupsPrintIPP();
        $ipp->setLog('', 0, 0);
        $ipp->setHost($this->container->getParameter('print_host'));
        $ipp->setPrinterURI($this->container->getParameter('print_queue'));
        $ipp->getPrinterAttributes();
        $canPrint = (implode('\n',$ipp->status) == 'successfull-ok');
        $setting = ($request->request->get('setting') == 'enabled');
        $session = $request->getSession();
        $autoprint = ($canPrint) ? $setting : $canPrint;
        $session->set('autoprint', $autoprint ? 'enabled' : 'disabled');

        return new Response();
    }
    
    /**
     * Autoprint panel
     *
     * @Route("/ajax/labelmode/")
     * @Method("POST")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function setLabelModeAction(Request $request)
    {
        $mode = $request->request->get('labelmode');
        $session = $request->getSession();
        $session->set('labelmode', $mode);

        return new Response();
    }
}
