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
use Doctrine\Common\Collections\ArrayCollection;

class VialControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/vials/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(4, $crawler->filter('tbody > tr')->count());
        $this->assertEquals(1, $crawler->filter(
                'tbody > tr:first-child > td:contains("yw ☿ ✕ yw; Sp / CyO ♂")')->count());
    }
    
    public function testListTrashed()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/vials/list/trashed');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('tbody > tr')->count());
        $this->assertEquals(1, $crawler->filter(
                'tbody > tr:first-child > td:contains("stock 1")')->count());
    }
    
    public function testListDead()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/vials/list/dead');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(2, $crawler->filter('tbody > tr')->count());
        $this->assertEquals(2, $crawler->filter(
                'tbody > tr > td:contains("stock 1")')->count());
    }
    
    public function testListPublic()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/vials/list/public');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(6, $crawler->filter('tbody > tr')->count());
        $this->assertEquals(1, $crawler->filter(
                'tbody > tr:first-child > td:contains("yw ☿ ✕ yw; Sp / CyO ♂")')->count());
    }
    
    public function testListAll()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/vials/list/all');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(8, $crawler->filter('tbody > tr')->count());
        $this->assertEquals(1, $crawler->filter(
                'tbody > tr:first-child > td:contains("yw ☿ ✕ yw; Sp / CyO ♂")')->count());
    }
    
    public function testExpand()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/vials/expand');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(6, $crawler->filter('.modal-body label')->count());
    }
    
    public function testExpandSubmit()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/vials/expand');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['vial_expand[source]'] = 2;
        $form['vial_expand[size]'] = 'medium';
        $form['vial_expand[number]'] = 2;

        $client->submit($form);        
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(3, $result->filter('td:contains("stock 2")')->count());
    }
    
    public function testExpandSubmitError()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/vials/expand');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['vial_expand[source]'] = 0;
        $form['vial_expand[size]'] = 'medium';
        $form['vial_expand[number]'] = 0;

        $result = $client->submit($form);        
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $result->filter('span:contains("This value is not valid.")')->count());
        $this->assertEquals(1, $result->filter('span:contains("This value should be 1 or more.")')->count());
    }
    
    public function testSelect()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/vials/select');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
    
    public function testSelectLabel()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/vials/select');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Flip')->form();
        $values = $form->getPhpValues();
        $values['select']['action'] = 'label';
        $values['select']['items'][0] = 2;
        $values['select']['items'][1] = 5;
        $values['select']['items'][2] = 8;
        
        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/pdf'));
        
        $result = $client->request('GET', '/secure/vials/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(3, $result->filter('tbody input[type="checkbox"]:not(:checked)')->count());
    }
    
    public function testSelectFlip()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/vials/select');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Flip')->form();
        $values = $form->getPhpValues();
        $values['select']['action'] = 'flip';
        $values['select']['items'][0] = 1;
        $values['select']['items'][1] = 8;
        
        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(2, $result->filter('td:contains("stock 1")')->count());
        $this->assertEquals(2, $result->filter('td:contains("yw ☿ ✕ yw; Sp / CyO ♂")')->count());
    }
    
    public function testSelectFlipTrash()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/vials/select');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Flip')->form();
        $values = $form->getPhpValues();
        $values['select']['action'] = 'fliptrash';
        $values['select']['items'][0] = 5;
        
        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $result->filter('td:contains("stock 4")')->count());
    }
    
    public function testSelectTrash()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/vials/select');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Flip')->form();
        $values = $form->getPhpValues();
        $values['select']['action'] = 'trash';
        $values['select']['items'][0] = 8;
        
        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $result->filter('td:contains("yw ☿ ✕ yw; Sp / CyO ♂")')->count());
    }
    
    public function testSelectUntrash()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/vials/select');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Flip')->form();
        $values = $form->getPhpValues();
        $values['select']['action'] = 'untrash';
        $values['select']['items'][0] = 7;
        
        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(3, $result->filter('td:contains("stock 1")')->count());
    }
    
    public function testSelectIncubate()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/vials/select');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Flip')->form();
        $values = $form->getPhpValues();
        $values['select']['action'] = 'incubate';
        $values['select']['items'][0] = 1;
        $values['select']['incubator'] = 'Test incubator';
        
        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
       $this->assertEquals(1, $result->filter('span:contains("25℃")')->count());
    }
    
    public function testCreate()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/vials/new');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }
    
    public function testShowNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/vials/show/0');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }
    
    public function testShowCross()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/vials/show/8');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $crawler_8 = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler_8->filter('html:contains("Cross 000008")')->count());
    }
    
    public function testShowStock()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/vials/show/5');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $crawler_5 = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler_5->filter('html:contains("Stock vial 000005")')->count());
    }
    
    public function testEditNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/vials/edit/0');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }
    
    public function testEditCross()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/vials/edit/8');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $crawler_8 = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler_8->filter(
                'html:contains("Edit cross yw ☿ ✕ yw; Sp / CyO ♂ (000008)")')->count());
    }
    
    public function testEditStock()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/vials/edit/5');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $crawler_5 = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler_5->filter('html:contains("Edit stock vial 000005")')->count());
    }
    
    public static function tearDownAfterClass()
    {
        $client = static::createClient();
        $vm = $client->getContainer()->get('vib.doctrine.vial_manager');
        $repository = $vm->getRepository('VIB\FliesBundle\Entity\Vial');
        $qb = $repository->createQueryBuilder('v')->where('v.id > 8');
        $vials = $qb->getQuery()->getResult();
        foreach ($vials as $vial) {
            $vm->removeACL($vial);
            $vm->remove($vial);
        }
        $vm->untrash($repository->find(5));
        $vm->untrash($repository->find(8));
        $vm->trash($repository->find(7));
        $vial = $repository->find(1);
        $vial->setIncubator(null);
        $vm->persist($vial);
        foreach (array(2, 5, 8) as $id) {
            $vial = $repository->find($id);
            $vial->setLabelPrinted(false);
            $vm->persist($vial);
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
