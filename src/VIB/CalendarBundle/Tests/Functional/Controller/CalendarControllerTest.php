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

namespace VIB\CalendarBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CalendarControllerTest extends WebTestCase
{
    public function testCalendar()
    {
        $client = static::createClient();

        $client->request('GET', '/calendar/jdoe.ics');
        $response = $client->getResponse();
        $this->assertEquals('TEST', $response->getContent(), $response->getContent());
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->headers->contains('Content-Type', 'text/calendar; charset=utf-8'));
    }

    public function testCalendarNoSuchUser()
    {
        $client = static::createClient();

        $client->request('GET', '/calendar/nobody.ics');
        $this->assertEquals(401,$client->getResponse()->getStatusCode());
    }
}
