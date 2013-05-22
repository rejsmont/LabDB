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

namespace VIB\FliesBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PrintControllerTest extends WebTestCase
{
    public function testSetAutoprint()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('POST', '/secure/ajax/autoprint/', array('setting' => 'enabled'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
    
    public function testSetLabelMode()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('POST', '/secure/ajax/labelmode/', array('labelmode' => 'std'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
    
    protected function getAuthenticatedClient()
    {
        return static::createClient(array(), array(
            'PHP_AUTH_USER' => 'jdoe',
            'PHP_AUTH_PW'   => 'password',
        ));
    }
}
