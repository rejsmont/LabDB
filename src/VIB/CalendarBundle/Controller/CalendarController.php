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

namespace VIB\CalendarBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sabre\VObject;

/**
 * Controller for online ics calendars
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class CalendarController extends Controller
{
    /**
     * Create online calendar for user
     *
     * @Route("/calendar/{username}.ics")
     *
     * @param  string                                     $username User to create the calendar for
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function calendarAction($username)
    {
        $user = $this->get('user_provider')->loadUserByUsername($username);
        $em = $this->getDoctrine()->getManager();
        $calendar = VObject\Component::create('VCALENDAR');
        $calendar->VERSION = '2.0';
        $field = 'X-WR-CALNAME';
        $calendar->$field = $user->getShortName() . '\'s flywork';

        $date = new \DateTime();
        $date->sub(new \DateInterval('P2M'));

        $stockVialQuery =  $em->getRepository('VIB\FliesBundle\Entity\StockVial')->createQueryBuilder('b');
        $stockVialQuery->where('b.setupDate > :date')
                       ->andWhere('b.trashed = false')
                       ->groupBy('b.setupDate')
                       ->addGroupBy('b.incubator')
                       ->addGroupBy('b.flipDate')
                       ->orderBy('b.setupDate', 'DESC')
                       ->setParameter('date', $date->format('Y-m-d'));

        $stockVials = $this->get('vib.security.helper.acl')->apply($stockVialQuery,array('OWNER'),$user)->getResult();
        $stockDates = array();

        foreach ($stockVials as $stockVial) {
            $stockDate = $stockVial->getRealFlipDate();
            if (! in_array($stockDate,$stockDates)) {
                $stockDates[] = $stockDate;
                $event = VObject\Component::create('VEVENT');
                $calendar->add($event);
                $event->SUMMARY = 'Transfer stocks';
                $dtstart = VObject\Property::create('DTSTART');
                $dtstart->setDateTime($stockDate, VObject\Property\DateTime::DATE);
                $event->DTSTART = $dtstart;
                $alarm = VObject\Component::create('VALARM');
                $event->add($alarm);
                $alarm->TRIGGER = 'PT8H';
                $alarm->ACTION = 'DISPLAY';
            }
        }

        $crossVialQuery =  $em->getRepository('VIB\FliesBundle\Entity\CrossVial')->createQueryBuilder('b');
        $crossVialQuery->where('b.setupDate > :date')
                       ->andWhere('b.trashed = false')
                       ->groupBy('b.setupDate')
                       ->addGroupBy('b.incubator')
                       ->addGroupBy('b.flipDate')
                       ->orderBy('b.setupDate', 'DESC')
                       ->setParameter('date', $date->format('Y-m-d'));

        $crossVials = $this->get('vib.security.helper.acl')->apply($crossVialQuery,array('OWNER'),$user)->getResult();
        $crossDates = array();

        foreach ($crossVials as $crossVial) {
            $crossDate = $crossVial->getRealFlipDate();
            if (! in_array($crossDate,$crossDates)) {
                $crossDates[] = $crossDate;
                $event = VObject\Component::create('VEVENT');
                $calendar->add($event);
                $event->SUMMARY = 'Check crosses';
                $dtstart = VObject\Property::create('DTSTART');
                $dtstart->setDateTime($crossDate, VObject\Property\DateTime::DATE);
                $event->DTSTART = $dtstart;
                $alarm = VObject\Component::create('VALARM');
                $event->add($alarm);
                $alarm->TRIGGER = 'PT8H';
                $alarm->ACTION = 'DISPLAY';
            }
        }

        return new Response($calendar->serialize(),200,
                array(
                    'Content-Type' => 'text/calendar; charset=utf-8',
                    'Content-Disposition' => 'inline; filename="calendar.ics"'));
    }
}
