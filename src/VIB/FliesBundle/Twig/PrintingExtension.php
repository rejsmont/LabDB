<?php

/*
 * Copyright 2013 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
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

use JMS\DiExtraBundle\Annotation as DI;

use PHP_IPP\IPP\CupsPrintIPP;
use PHP_IPP\IPP\IPPException;

/**
 * Printing extension
 *
 * @DI\Service
 * @DI\Tag("twig.extension")
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class PrintingExtension extends \Twig_Extension
{

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
     * @DI\InjectParams({
     *     "printHost" = @DI\Inject("%print_host%"),
     *     "printQueue" = @DI\Inject("%print_queue%")
     * })
     * 
     * @param string $printHost
     * @param string $printQueue
     */
    public function __construct($printHost, $printQueue)
    {
        $this->printHost = $printHost;
        $this->printQueue = $printQueue;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            'can_print' => new \Twig_Function_Method($this, 'canPrintFunction')
        );
    }

    /**
     * Check if printing is enabled
     *
     * @return boolean
     */
    public function canPrintFunction()
    {
        $host = $this->printHost;
        $queue = $this->printQueue;
        if ((null !== $host)&&(null !== $queue)) {
            try {
                $ipp = new CupsPrintIPP();
                $ipp->setLog('', 0, 0);
                $ipp->setHost($host);
                $ipp->setPrinterURI($queue);
                $ipp->getPrinterAttributes();

                return (implode('\n',$ipp->status) == 'successfull-ok');
            } catch (IPPException $e) {}
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'printing_extension';
    }
}
