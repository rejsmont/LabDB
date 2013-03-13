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

namespace VIB\FliesBundle\Twig;

use PHP_IPP\IPP\CupsPrintIPP;

/**
 * Description of PrintingExtension
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class PrintingExtension extends \Twig_Extension {
    
    /**
     * @var string $printHost
     */
    private $printHost;
    
    /**
     * @var string $printQueue
     */
    private $printQueue;
    
    
    /**
     * Construct PrintingExtension
     * 
     * @param string $printHost
     * @param string $printQueue
     */ 
    public function __construct($printHost,$printQueue) {
        $this->printHost = $printHost;
        $this->printQueue = $printQueue;
    }
    
    public function getFunctions() {
        return array(
            'can_print' => new \Twig_Function_Method($this, 'canPrintFunction')
        );
    }

    public function canPrintFunction() {
        $ipp = new CupsPrintIPP();
        $ipp->setLog('', 0, 0);
        $ipp->setHost($this->printHost);
        $ipp->setPrinterURI($this->printQueue);
        $ipp->getPrinterAttributes();
        if (implode('\n',$ipp->status) == 'successfull-ok') {
            return true;
        } else {
            return false;
        }
    }

    public function getName()
    {
        return 'printing_extension';
    }    
}

?>