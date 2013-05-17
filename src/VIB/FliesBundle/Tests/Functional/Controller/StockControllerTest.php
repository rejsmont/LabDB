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

class StockControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/stocks/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(3, $crawler->filter('tbody > tr')->count());
        $this->assertEquals(1, $crawler->filter(
                'tbody > tr:first-child > td:contains("stock 1")')->count());
    }
    
    public function testListCreated()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/stocks/list/created');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(2, $crawler->filter('tbody > tr')->count());
        $this->assertEquals(1, $crawler->filter(
                'tbody > tr:first-child > td:contains("stock 1")')->count());
    }
    
    public function testListPublic()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/stocks/list/public');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(4, $crawler->filter('tbody > tr')->count());
        $this->assertEquals(1, $crawler->filter(
                'tbody > tr:first-child > td:contains("stock 1")')->count());
    }
    
    public function testCreate()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/stocks/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(13, $crawler->filter('.modal-body label')->count());
    }
    
    public function testCreateSubmit()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/stocks/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['stock_new[stock][name]'] = 'stock 5';
        $form['stock_new[stock][genotype]'] = 'Tm2/Tm6';

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(1, $result->filter('html:contains("Stock stock 5")')->count());
    }
    
    public function testCreateSubmitError()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/stocks/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['stock_new[stock][name]'] = '';
        $form['stock_new[stock][genotype]'] = '';
        $form['stock_new[number]'] = 0;
        
        $result = $client->submit($form);        
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $result->filter('span:contains("Name must be specified")')->count());
        $this->assertEquals(1, $result->filter('span:contains("Genotype must be specified")')->count());
        $this->assertEquals(1, $result->filter('span:contains("This value should be 1 or more.")')->count());
    }
    
    public function testCreateSubmitDuplicate()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/stocks/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['stock_new[stock][name]'] = 'stock 1';
        $form['stock_new[stock][genotype]'] = 'yw';
        
        $result = $client->submit($form);        
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $result->filter('span:contains("This value is already used.")')->count());
        $button = $result->filter('a:contains("Yes, please")');
        $this->assertEquals(1, $button->count());

        $newVialForm = $client->click($button->eq(0)->link());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $newVialForm->filter('input[value="stock 1"]')->count());
        
    }
    
    public function testShow()
    {
        $client = $this->getAuthenticatedClient();
        
        $crawler_5 = $client->request('GET', '/secure/stocks/show/1');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler_5->filter('html:contains("Stock stock 1")')->count());
    }
    
    public function testShowNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/stocks/show/0');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }
    
    public function testEdit()
    {
        $client = $this->getAuthenticatedClient();
        
        $crawler_5 = $client->request('GET', '/secure/stocks/edit/1');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler_5->filter('input[value="stock 1"]')->count());
    }
    
    public function testEditSubmit()
    {
        $client = $this->getAuthenticatedClient();
        
        $crawler = $client->request('GET', '/secure/stocks/edit/1');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['stock[notes]'] = 'This is a test note.';
        
        $client->submit($form);        
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(1, $result->filter('span.input-text:contains("This is a test note.")')->count());
    }
    
    public function testEditNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/stocks/edit/0');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }

    public static function tearDownAfterClass()
    {
        $client = static::createClient();
        $om = $client->getContainer()->get('vib.doctrine.manager');
        $repository = $om->getRepository('VIB\FliesBundle\Entity\Stock');
        $qb = $repository->createQueryBuilder('s')->where('s.id > 4');
        $stocks = $qb->getQuery()->getResult();
        foreach ($stocks as $stock) {
            $om->removeACL($stock->getVials());
            $om->removeACL($stock);
            $om->remove($stock);
        }
        $om->flush();
        $vm = $client->getContainer()->get('vib.doctrine.vial_manager');
        $repository = $vm->getRepository('VIB\FliesBundle\Entity\Vial');
        $qb = $repository->createQueryBuilder('v')->where('v.id > 8');
        $vials = $qb->getQuery()->getResult();
        foreach ($vials as $vial) {
            $vm->removeACL($vial);
            $vm->remove($vial);
        }
        $vm->flush();
    }
    
    protected function getAuthenticatedClient()
    {
        return static::createClient(array(), array(
            'PHP_AUTH_USER' => 'jdoe',
            'PHP_AUTH_PW'   => 'password',
        ));
    }
}
