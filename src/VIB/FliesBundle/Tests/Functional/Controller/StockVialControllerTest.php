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

class StockVialControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/stocks/vials/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(3, $crawler->filter('tbody > tr')->count());
        $this->assertEquals(1, $crawler->filter(
                'tbody > tr:first-child > td:contains("stock 4")')->count());
    }
    
    public function testShow()
    {
        $client = $this->getAuthenticatedClient();
        
        $crawler_5 = $client->request('GET', '/secure/stocks/vials/show/5');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler_5->filter('html:contains("Stock vial 000005")')->count());
    }
    
    public function testShowNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/stocks/vials/show/8');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }
    
    public function testEdit()
    {
        $client = $this->getAuthenticatedClient();
        
        $crawler_5 = $client->request('GET', '/secure/stocks/vials/edit/5');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler_5->filter('html:contains("Edit stock vial 000005")')->count());
    }
    
    public function testEditNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/stocks/vials/edit/8');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }
    
    protected function getAuthenticatedClient()
    {
        return static::createClient(array(), array(
            'PHP_AUTH_USER' => 'jdoe',
            'PHP_AUTH_PW'   => 'password',
        ));
    }
}
