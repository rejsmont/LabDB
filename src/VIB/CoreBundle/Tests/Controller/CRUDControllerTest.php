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

namespace VIB\CoreBundle\Test\Controller;

use VIB\CoreBundle\Entity\Entity;


class CRUDControllerTest extends \PHPUnit_Framework_TestCase
{
    private $controller;
    
    
    public function testListAction()
    {
        $result = $this->controller->listAction();
        $this->assertArrayHasKey('entities', $result);
        $this->assertArrayHasKey('filter', $result);
        $this->assertNull($result['entities']);
        $this->assertNull($result['filter']);
    }
    
    public function testListActionFilter()
    {
        $result = $this->controller->listAction('test');
        $this->assertArrayHasKey('entities', $result);
        $this->assertArrayHasKey('filter', $result);
        $this->assertNull($result['entities']);
        $this->assertEquals('test', $result['filter']);
    }
    
    public function testShowAction()
    {
        $result = $this->controller->showAction(1);
    }
    
    public function testShowActionNotFound()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        $this->controller->showAction(0);
    }
    
    protected function setUp()
    {
        $methods = array(
            'getCurrentPage', 'getPaginator', 'getObjectManager',
            'getSecurityContext', 'getUser', 'getAclFilter', 'getEntityClass'
        );
        
        $this->controller =
                $this->getMockBuilder('VIB\CoreBundle\Controller\CRUDController')
                     ->setMethods($methods)
                     ->getMockForAbstractClass();
        $this->controller->expects($this->any())
            ->method('getCurrentPage')
            ->will($this->returnValue(0));
        $this->controller->expects($this->any())
            ->method('getPaginator')
            ->will($this->returnValue($this->getMock('Knp\Component\Pager\Paginator')));
        $this->controller->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($this->getFakeObjectManager()));
        $this->controller->expects($this->any())
            ->method('getSecurityContext')
            ->will($this->returnValue($this->getFakeSecurityContext()));
        $this->controller->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->getMock('Symfony\Component\Security\Core\User\UserInterface')));
        $this->controller->expects($this->any())
            ->method('getAclFilter')
            ->will($this->returnValue($this->getFakeAclFilter()));
        $this->controller->expects($this->any())
            ->method('getEntityClass')
            ->will($this->returnValue('VIB\CoreBundle\Entity\Entity'));
    }
    
    private function getFakeEntityRepository()
    {
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        
        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->any())
            ->method('createQueryBuilder')
            ->will($this->returnValue($qb));
        
        return $repository;
    }
    
    private function getFakeObjectManager()
    {
        $map = array(
          array('VIB\CoreBundle\Entity\Entity', 0, null),
          array('VIB\CoreBundle\Entity\Entity', 1, new Entity())
        );
        
        $om = $this->getMockBuilder('VIB\CoreBundle\Doctrine\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $om->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->getFakeEntityRepository()));
        $om->expects($this->any())
            ->method('find')
            ->will($this->returnValueMap($map));
        
        return $om;
    }
    
    private function getFakeSecurityContext()
    {
        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $context->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue(true));
        
        return $context;
    }
    
    private function getFakeAclFilter()
    {
        $aclFilter = $this->getMockBuilder('VIB\SecurityBundle\Bridge\Doctrine\AclHelper')
            ->disableOriginalConstructor()
            ->getMock();
        
        return $aclFilter;
    }
}

