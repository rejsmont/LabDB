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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use PHP_IPP\IPP\CupsPrintIPP;

/**
 * Description of AutoPrintController
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AutoPrintController extends Controller {
    
    /**
     * Autoprint panel
     * 
     * @Template()
     * @Route("/autoprint")
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function autoPrintAction() {
        $ipp = new CupsPrintIPP();
        $ipp->setLog('', 0, 0);
        $ipp->setHost($this->container->getParameter('print_host'));
        $ipp->setPrinterURI($this->container->getParameter('print_queue'));
        $ipp->getPrinterAttributes();
        if (implode('\n',$ipp->status) != 'client-error-not-found') {
            return new Response('Printer is OK ' . implode('\n',$ipp->status), 200);
        } else {
            return new Response('Printer is not OK ' . implode('\n',$ipp->status), 400);
        }
    }
}

?>
