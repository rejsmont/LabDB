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

class SearchControllerTest extends WebTestCase
{
    public function testResultStocks()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/');
        $form = $crawler->filter('form#search-form')->form();
        $form['search_form[query]'] = 'yw';
        $form['search_form[filter]'] = '';

        $result = $client->submit($form);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(4, $result->filter('tbody tr')->count());
    }

    public function testResultCrosses()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/');
        $form = $crawler->filter('form#search-form')->form();
        $form['search_form[query]'] = 'yw';
        $form['search_form[filter]'] = 'crossvial';

        $result = $client->submit($form);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $result->filter('tbody tr')->count());
    }

    public function testResultVial()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/');
        $form = $crawler->filter('form#search-form')->form();
        $form['search_form[query]'] = '1';
        $form['search_form[filter]'] = '';

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $result->filter('html:contains("Stock vial 000001")')->count());
    }

    public function testResultRack()
    {
        $client = $this->getAuthenticatedClient();

        $crawler = $client->request('GET', '/');
        $form = $crawler->filter('form#search-form')->form();
        $form['search_form[query]'] = 'R1';
        $form['search_form[filter]'] = '';

        $client->submit($form);
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
        $result = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $result->filter('html:contains("Rack R000001")')->count());
    }

    protected function getAuthenticatedClient()
    {
        return static::createClient(array(), array(
            'PHP_AUTH_USER' => 'jdoe',
            'PHP_AUTH_PW'   => 'password',
        ));
    }
}
