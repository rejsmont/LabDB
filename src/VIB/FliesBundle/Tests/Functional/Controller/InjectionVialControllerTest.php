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

class InjectionVialControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/injections/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(2, $crawler->filter('tbody > tr')->count());
        $this->assertEquals(1, $crawler->filter(
                'tbody > tr:first-child > td:contains("test")')->count());
        $this->assertEquals(1, $crawler->filter(
                'tbody > tr:first-child > td:contains("stock 4")')->count());
    }

    public function testExpand()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/flies/injections/expand');
        $this->assertEquals(404,$client->getResponse()->getStatusCode());
    }

    public function testSelect()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/flies/injections/select');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testCreate()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/injections/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(18, $crawler->filter('.modal-body label')->count());
    }

    public function testCreateSubmitOne()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/injections/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['injectionvial_new[vial][constructName]'] = 'test construct';
        $form['injectionvial_new[vial][targetStock]'] = 'stock 1';
        $form['injectionvial_new[vial][embryoCount]'] = 50;
        $form['injectionvial_new[number]'] = 1;

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(1, $result->filter('span.muted:contains("test construct ➔ stock 1")')->count());
    }

    public function testCreateSubmitMany()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/injections/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['injectionvial_new[vial][constructName]'] = 'test construct';
        $form['injectionvial_new[vial][targetStock]'] = 'stock 1';
        $form['injectionvial_new[vial][embryoCount]'] = 50;
        $form['injectionvial_new[number]'] = 4;

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(5, $result->filter('td:contains("test construct")')->count());
    }

    public function testCreateSubmitError()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/injections/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['injectionvial_new[number]'] = 0;

        $result = $client->submit($form);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $result->filter('span:contains("Construct name must be specified")')->count());
        $this->assertEquals(1, $result->filter('span:contains("Target stock must be specified")')->count());
        $this->assertEquals(1, $result->filter('span:contains("Embryo count must be greater than 0")')->count());
        $this->assertEquals(1, $result->filter('span:contains("This value should be 1 or more.")')->count());
    }

    public function testStats()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/injections/stats/10');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler
                ->filter('a.control-label:contains("Total vials") + div > span > strong:contains("2")')
                ->count());
        $this->assertEquals(1, $crawler->filter('span.input-text.text-info:contains("(100%)")')->count());
        $this->assertEquals(1, $crawler->filter('span.input-text.text-success:contains("(0%)")')->count());
        $this->assertEquals(1, $crawler->filter('span.input-text.text-warning:contains("(0%)")')->count());
        $this->assertEquals(1, $crawler->filter('span.input-text.text-danger:contains("(0%)")')->count());

    }

    public function testShow()
    {
        $client = $this->getAuthenticatedClient();

        $crawler_8 = $client->request('GET', '/flies/injections/show/12');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler_8->filter('html:contains("Injection vial 000012")')->count());
    }

    public function testShowNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/flies/injections/show/5');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }

    public function testEdit()
    {
        $client = $this->getAuthenticatedClient();

        $crawler_8 = $client->request('GET', '/flies/injections/edit/12');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler_8->filter(
                'html:contains("Edit injection vial test construct ➔ stock 1 (000012)")')->count());
    }

    public function testEditSubmit()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/flies/injections/edit/12');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['injectionvial[notes]'] = 'This is a test note.';

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(1, $result->filter('span.input-text:contains("This is a test note.")')->count());
    }

    public function testEditNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/flies/injections/edit/5');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }

    public static function tearDownAfterClass()
    {
        $client = static::createClient();
        $vm = $client->getContainer()->get('vib.doctrine.vial_manager');
        $repository = $vm->getRepository('VIB\FliesBundle\Entity\InjectionVial');
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
