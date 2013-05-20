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

class AJAXControllerTest extends WebTestCase
{
    public function testVial()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/ajax/vials?id=1');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('tr')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("000001")')->count());
    }
    
    public function testVialJson()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/ajax/vials?id=1&format=json');
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        
    }
    
    public function testVialNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/ajax/vials?id=0');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }
    
    public function testVialStock()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/ajax/vials?id=1&filter=stock');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('tr')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("000001")')->count());
    }
    
    public function testVialStockNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/ajax/vials?id=8&filter=stock');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }
    
    public function testVialCross()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/ajax/vials?id=8&filter=cross');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('tr')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("000008")')->count());
    }
    
    public function testVialCrossNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/ajax/vials?id=1&filter=cross');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }
    
    public function testRackVial()
    {
        $client = $this->getAuthenticatedClient();
        
        $crawler = $client->request('GET', '/secure/ajax/racks/vials?vialID=1&positionID=2');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('input[type=checkbox]#select_items_1')->count());
    }
    
    public function testRackVialError()
    {
        $client = $this->getAuthenticatedClient();
        
        $client->request('GET', '/secure/ajax/racks/vials?vialID=1&positionID=1');
        $response = $client->getResponse();
        $this->assertEquals(406,$response->getStatusCode());
    }
    
    public function testRackVialRemove()
    {
        $client = $this->getAuthenticatedClient();
        
        $client->request('GET', '/secure/ajax/racks/vials/remove?vialID=1&rackID=1');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
    
    public function testStockSearch()
    {
        $client = $this->getAuthenticatedClient();
        
        $client->request('GET', '/secure/ajax/stocks/search?query=CyO');
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertRegExp('/stock 2/', $response->getContent());
    }
    
    public function testPopoverVial()
    {
        $client = $this->getAuthenticatedClient();
        
        $client->request('GET', '/secure/ajax/popover?type=vial&id=1');
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertRegExp('/Vial 000001/', $response->getContent());
    }
    
    public function testPopoverVialCross()
    {
        $client = $this->getAuthenticatedClient();
        
        $client->request('GET', '/secure/ajax/popover?type=vial&id=8');
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertRegExp('/Cross 000008/', $response->getContent());
    }
    
    public function testPopoverVialNotFound()
    {
        $client = $this->getAuthenticatedClient();
        
        $client->request('GET', '/secure/ajax/popover?type=vial&id=0');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }
    
    public function testPopoverStock()
    {
        $client = $this->getAuthenticatedClient();
        
        $client->request('GET', '/secure/ajax/popover?type=stock&id=1');
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertRegExp('/Stock stock 1/', $response->getContent());
    }
    
    public function testPopoverStockNotFound()
    {
        $client = $this->getAuthenticatedClient();
        
        $client->request('GET', '/secure/ajax/popover?type=stock&id=0');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }
    
    public function testPopoverUnrecognized()
    {
        $client = $this->getAuthenticatedClient();
        
        $client->request('GET', '/secure/ajax/popover?type=test&id=0');
        $response = $client->getResponse();
        $this->assertEquals(406,$response->getStatusCode());
    }
    
    public static function tearDownAfterClass()
    {
        
    }
    
    protected function getAuthenticatedClient()
    {
        return static::createClient(array(), array(
            'PHP_AUTH_USER' => 'jdoe',
            'PHP_AUTH_PW'   => 'password',
        ));
    }
    
    protected function getAdminAuthenticatedClient()
    {
        return static::createClient(array(), array(
            'PHP_AUTH_USER' => 'asmith',
            'PHP_AUTH_PW'   => 'password',
        ));
    }
}
