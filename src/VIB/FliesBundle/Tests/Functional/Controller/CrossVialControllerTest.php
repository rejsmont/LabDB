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

class CrossVialControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/crosses/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('tbody > tr')->count());
        $this->assertEquals(2, $crawler->filter(
                'tbody > tr:first-child > td:contains("yw")')->count());
        $this->assertEquals(1, $crawler->filter(
                'tbody > tr:first-child > td:contains("yw; Sp / CyO")')->count());
    }

    public function testExpand()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/crosses/expand');
        $this->assertEquals(404,$client->getResponse()->getStatusCode());
    }

    public function testSelect()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/crosses/select');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testCreate()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/crosses/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(13, $crawler->filter('.modal-body label')->count());
    }

    public function testCreateSubmitOne()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/crosses/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['crossvial_new[vial][virgin]'] = '1';
        $form['crossvial_new[vial][male]'] = '5';
        $form['crossvial_new[number]'] = 1;

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(1, $result->filter('span.muted:contains("yw ☿ ✕ yw / Fm7 ♂")')->count());
    }

    public function testCreateSubmitMany()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/crosses/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['crossvial_new[vial][virgin]'] = '1';
        $form['crossvial_new[vial][male]'] = '2';
        $form['crossvial_new[number]'] = 5;

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(6, $result->filter('td:contains("yw; Sp / CyO")')->count());
    }

    public function testCreateSubmitError()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/crosses/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['crossvial_new[number]'] = 0;

        $result = $client->submit($form);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $result->filter('span:contains("Virgin must be specified")')->count());
        $this->assertEquals(1, $result->filter('span:contains("Male must be specified")')->count());
        $this->assertEquals(1, $result->filter('span:contains("This value should be 1 or more.")')->count());
    }

    public function testCreateSubmitErrorGenotype()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/crosses/new');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['crossvial_new[vial][virgin]'] = '12';
        $form['crossvial_new[vial][male]'] = '13';

        $result = $client->submit($form);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $result->filter('span:contains("Virgin genotype must be specified")')->count());
        $this->assertEquals(1, $result->filter('span:contains("Male genotype must be specified")')->count());
    }

    public function testSelectMarkSterile()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/crosses/select');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Flip')->form();
        $values = $form->getPhpValues();
        $values['select']['action'] = 'marksterile';
        $values['select']['items'][0] = 12;

        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(0, $result->filter('td:contains("000012")')->count());

        $crawler_trashed = $client->request('GET', '/secure/crosses/list/trashed');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler_trashed->filter('td:contains("000012")')->count());
        $this->assertEquals(1, $crawler_trashed->filter('td i.fa-times-circle')->count());
    }

    public function testSelectMarkSuccessful()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/crosses/select');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Flip')->form();
        $values = $form->getPhpValues();
        $values['select']['action'] = 'marksuccessful';
        $values['select']['items'][0] = 13;
        $values['select']['items'][1] = 14;

        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(2, $result->filter('td i.fa-check')->count());
    }

    public function testSelectMarkFailed()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/crosses/select');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Flip')->form();
        $values = $form->getPhpValues();
        $values['select']['action'] = 'markfailed';
        $values['select']['items'][0] = 15;

        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $result->filter('td i.fa-times')->count());

    }

    public function testStats()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/crosses/stats/12');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler
                ->filter('a.control-label:contains("Total crosses") + div > span > strong:contains("6")')
                ->count());
        $this->assertEquals(1, $crawler->filter('span.input-text.text-info:contains("(33.33%)")')->count());
        $this->assertEquals(1, $crawler->filter('span.input-text.text-success:contains("(33.33%)")')->count());
        $this->assertEquals(1, $crawler->filter('span.input-text.text-warning:contains("(16.67%)")')->count());
        $this->assertEquals(1, $crawler->filter('span.input-text.text-danger:contains("(16.67%)")')->count());

    }

    public function testShow()
    {
        $client = $this->getAuthenticatedClient();

        $crawler_8 = $client->request('GET', '/secure/crosses/show/8');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler_8->filter('html:contains("Cross 000008")')->count());
    }

    public function testShowNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/crosses/show/5');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }

    public function testEdit()
    {
        $client = $this->getAuthenticatedClient();

        $crawler_8 = $client->request('GET', '/secure/crosses/edit/8');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler_8->filter(
                'html:contains("Edit cross yw ☿ ✕ yw; Sp / CyO ♂ (000008)")')->count());
    }

    public function testEditSubmit()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/secure/crosses/edit/8');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Save')->form();
        $form['crossvial[notes]'] = 'This is a test note.';

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertEquals(1, $result->filter('span.input-text:contains("This is a test note.")')->count());
    }

    public function testEditNotFound()
    {
        $client = $this->getAuthenticatedClient();

        $client->request('GET', '/secure/crosses/edit/5');
        $response = $client->getResponse();
        $this->assertEquals(404,$response->getStatusCode());
    }

    public static function tearDownAfterClass()
    {
        $client = static::createClient();
        $vm = $client->getContainer()->get('vib.doctrine.vial_manager');
        $repository = $vm->getRepository('VIB\FliesBundle\Entity\CrossVial');
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
