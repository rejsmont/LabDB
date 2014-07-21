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

class IncubatorControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/incubators/');
        $this->assertEquals(404,$client->getResponse()->getStatusCode());
    }

    public function testCreate()
    {
        $client = $this->getAdminAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/incubators/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(2, $crawler->filter('.modal-body label')->count());
    }

    public function testCreateSubmit()
    {
        $client = $this->getAdminAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/incubators/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['incubator[name]'] = 'Hot incubator';
        $form['incubator[temperature]'] = '37.25';

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(1, $result->filter('html:contains("Incubator Hot incubator")')->count());
    }

    public function testCreateSubmitError()
    {
        $client = $this->getAdminAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/incubators/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['incubator[name]'] = '';
        $form['incubator[temperature]'] = '0.00';

        $result = $client->submit($form);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $result->filter('span:contains("Name must be specified")')->count());
        $this->assertEquals(1, $result->filter('span:contains("Temperature cannot be lower than 4℃")')->count());
    }

    public function testShow()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/incubators/show/1');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('html:contains("Incubator Test incubator")')->count());
    }

    public function testShowNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/flies/incubators/show/0');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }

    public function testEdit()
    {
        $client = $this->getAdminAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/incubators/edit/2');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('html:contains("Edit incubator Hot incubator")')->count());
    }

    public function testEditSubmit()
    {
        $client = $this->getAdminAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/incubators/edit/2');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['incubator[temperature]'] = 16;

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(1, $result->filter('span.input-text:contains("16")')->count());
    }

    public function testEditNotFound()
    {
        $client = $this->getAdminAuthenticatedClient();

        $client->request('GET', '/flies/incubators/edit/0');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }

    public function testEditForbidden()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/flies/incubators/edit/2');
        $response = $client->getResponse();
        $this->assertEquals(403,$response->getStatusCode());
    }

    public static function tearDownAfterClass()
    {
        $client = static::createClient();
        $om = $client->getContainer()->get('vib.doctrine.registry')->getManagerForClass('VIB\CoreBundle\Entity\Entity');
        $repository = $om->getRepository('VIB\FliesBundle\Entity\Incubator');
        $qb = $repository->createQueryBuilder('s')->where('s.id > 1');
        $incubators = $qb->getQuery()->getResult();
        foreach ($incubators as $incubator) {
            $om->remove($incubator);
        }
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
