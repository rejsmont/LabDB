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

    public function testSelect()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/stocks/vials/select');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testCreate()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/stocks/vials/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(12, $crawler->filter('.modal-body label')->count());
    }

    public function testCreateSubmitOne()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/stocks/vials/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['stockvial_new[vial][stock]'] = 'stock 4';
        $form['stockvial_new[number]'] = 1;

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(1, $result->filter('.input-text a:contains("stock 4")')->count());
    }

    public function testCreateSubmitMany()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/stocks/vials/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['stockvial_new[vial][stock]'] = 'stock 1';
        $form['stockvial_new[number]'] = 2;

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(3, $result->filter('td:contains("stock 1")')->count());
    }

    public function testCreateSubmitError()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/stocks/vials/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['stockvial_new[vial][stock]'] = '';
        $form['stockvial_new[number]'] = 0;

        $result = $client->submit($form);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $result->filter('span:contains("Stock must be specified")')->count());
        $this->assertEquals(1, $result->filter('span:contains("This value should be 1 or more.")')->count());
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

    public function testEditSubmit()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/stocks/vials/edit/5');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['stockvial[notes]'] = 'This is a test note.';

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(1, $result->filter('span.input-text:contains("This is a test note.")')->count());
    }

    public function testEditNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/stocks/vials/edit/8');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }

    public static function tearDownAfterClass()
    {
        $client = static::createClient();
        $vm = $client->getContainer()->get('vib.doctrine.vial_manager');
        $repository = $vm->getRepository('VIB\FliesBundle\Entity\StockVial');
        $qb = $repository->createQueryBuilder('v')->where('v.id > 10');
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
