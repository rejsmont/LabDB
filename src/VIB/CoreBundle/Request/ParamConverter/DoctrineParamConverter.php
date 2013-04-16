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

namespace VIB\CoreBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter as SensioDoctrineParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

/**
 * DoctrineParamConverter
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class DoctrineParamConverter extends SensioDoctrineParamConverter
{
    /**
     * Apply the converter
     *
     * @param  \Symfony\Component\HttpFoundation\Request                                $request
     * @param  \Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface $configuration
     * @return boolean
     * @throws NotFoundHttpException
     */
    public function apply(Request $request, ConfigurationInterface $configuration)
    {
        $name    = $configuration->getName();
        $options = $this->getOptions($configuration);
        $id      = $this->getIdentifier($request, $options, $name);

        if (is_array($id)) {
            $id = implode (":",$id);
        }

        try {
            return parent::apply($request, $configuration);
        } catch (NotFoundHttpException $exeption) {
            throw new NotFoundHttpException(sprintf($options['error_message'],$id));
        }

        return true;
    }

    /**
     * Get options
     *
     * @param  \Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface $configuration
     * @return array
     */
    protected function getOptions(ConfigurationInterface $configuration)
    {
        return array_replace(array(
            'error_message'  => 'Not Found'
        ), parent::getOptions($configuration));
    }
}
