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

class RackControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/racks/');
        $this->assertEquals(404,$client->getResponse()->getStatusCode());
    }

    public function testCreate()
    {
        $client = $this->getAdminAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/racks/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(3, $crawler->filter('.modal-body label')->count());
    }

    public function testCreateSubmit()
    {
        $client = $this->getAdminAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/racks/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['rack[rack][name]'] = 'Test flies';
        $form['rack[rows]'] = 5;
        $form['rack[columns]'] = 5;

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(1, $result->filter('html:contains("Test flies")')->count());
    }

    public function testCreateSubmitError()
    {
        $client = $this->getAdminAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/racks/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['rack[rows]'] = 0;
        $form['rack[columns]'] = 0;

        $result = $client->submit($form);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(2, $result->filter('span:contains("This value should be 1 or more.")')->count());
    }

    public function testShow()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/racks/show/1');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('html:contains("Rack R000001")')->count());
    }

    public function testShowNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/flies/racks/show/0');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }

    public function testEdit()
    {
        $client = $this->getAdminAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/racks/edit/2');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('html:contains("Edit rack R000002")')->count());
    }

    public function testEditSubmit()
    {
        $client = $this->getAdminAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/racks/edit/2');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['rack[rack][name]'] = 'Test flies modified';

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(1, $result->filter('html:contains("Test flies modified")')->count());
    }

    public function testEditNotFound()
    {
        $client = $this->getAdminAuthenticatedClient();

        $client->request('GET', '/flies/racks/edit/0');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }

    public function testDowaloadLabel()
    {
        $client = $this->getAdminAuthenticatedClient();

        $client->request('GET', '/flies/racks/label/1/download');
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/pdf'));
    }

    public function testIncubate()
    {
        $client = $this->getAuthenticatedClient();

        $vial_crawler = $client->request('GET', '/flies/stocks/vials/show/5');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(0, $vial_crawler->filter('span:contains("25℃")')->count());

        $crawler = $client->request('GET', '/flies/racks/show/1');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Flip')->form();
        $values = $form->getPhpValues();
        $values['select']['action'] = 'incubate';
        $values['select']['incubator'] = 'Test incubator';

        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());

        $vial_result = $client->request('GET', '/flies/stocks/vials/show/5');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $vial_result->filter('span:contains("25℃")')->count());
    }

    public static function tearDownAfterClass()
    {
        $client = static::createClient();
        $om = $client->getContainer()->get('vib.doctrine.manager');
        $repository = $om->getRepository('VIB\FliesBundle\Entity\Rack');
        $qb = $repository->createQueryBuilder('r')->where('r.id > 1');
        $racks = $qb->getQuery()->getResult();
        foreach ($racks as $rack) {
            $om->removeACL($rack);
            $om->remove($rack);
        }
        $rack = $repository->find(1);
        $rack->setStorageUnit(null);
        $om->persist($rack);
        $om->flush();
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
