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

namespace VIB\CoreBundle\Tests\Controller;

use VIB\CoreBundle\Entity\Entity;

class CRUDControllerTest extends \PHPUnit_Framework_TestCase
{
    private $controller;
    private $objectManager;
    private $entityRepository;
    private $request;
    private $securityContext;
    private $aclFilter;
    private $form;

    public function testListAction()
    {
        $this->controller->expects($this->atLeastOnce())->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $result = $this->controller->listAction();
        $this->assertArrayHasKey('entities', $result);
        $this->assertArrayHasKey('filter', $result);
        $this->assertNull($result['entities']);
        $this->assertNull($result['filter']);
    }

    public function testListActionFilter()
    {
        $this->controller->expects($this->atLeastOnce())->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $result = $this->controller->listAction('test');
        $this->assertArrayHasKey('entities', $result);
        $this->assertArrayHasKey('filter', $result);
        $this->assertNull($result['entities']);
        $this->assertEquals('test', $result['filter']);
    }

    public function testShowAction()
    {
        $this->controller->expects($this->atLeastOnce())->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $result = $this->controller->showAction(1);
        $this->assertArrayHasKey('entity', $result);
        $this->assertArrayHasKey('owner', $result);
        $this->assertEquals(new Entity(), $result['entity']);
        $this->assertNull($result['owner']);
    }

    public function testShowActionNotFound()
    {
        $this->controller->expects($this->atLeastOnce())->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        $this->controller->showAction(0);
    }

    public function testCreateAction()
    {
        $this->request = $this->getFakeRequest();
        $this->request->expects($this->once())->method('getMethod')->will($this->returnValue('GET'));
        $this->controller->expects($this->once())->method('createForm')->will($this->returnValue($this->form));
        $this->controller->expects($this->once())->method('getRequest')->will($this->returnValue($this->request));
        $result = $this->controller->createAction();
        $this->assertArrayHasKey('form', $result);
        $this->assertNull($result['form']);
    }

    public function testCreateActionSubmit()
    {
        $this->request = $this->getFakeRequest();
        $this->request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));
        $this->form = $this->getFakeForm();
        $this->form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $this->objectManager = $this->getFakeObjectManager();
        $this->objectManager->expects($this->once())->method('persist');
        $this->objectManager->expects($this->once())->method('flush');
        $this->objectManager->expects($this->once())->method('createACL');
        $this->controller->expects($this->once())->method('getRequest')->will($this->returnValue($this->request));
        $this->controller->expects($this->once())->method('createForm')->will($this->returnValue($this->form));
        $this->controller->expects($this->once())->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $result = $this->controller->createAction();
        $this->assertNull($result);
    }

    public function testEditAction()
    {
        $this->request = $this->getFakeRequest();
        $this->request->expects($this->once())->method('getMethod')->will($this->returnValue('GET'));
        $this->controller->expects($this->once())->method('createForm')->will($this->returnValue($this->form));
        $this->controller->expects($this->once())->method('getRequest')->will($this->returnValue($this->request));
        $this->controller->expects($this->atLeastOnce())->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $result = $this->controller->editAction(1);
        $this->assertArrayHasKey('form', $result);
        $this->assertNull($result['form']);
    }

    public function testEditActionSubmit()
    {
        $this->request = $this->getFakeRequest();
        $this->request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));
        $this->form = $this->getFakeForm();
        $this->form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $this->objectManager = $this->getFakeObjectManager();
        $this->objectManager->expects($this->once())->method('persist');
        $this->objectManager->expects($this->once())->method('flush');
        $this->controller->expects($this->once())->method('getRequest')->will($this->returnValue($this->request));
        $this->controller->expects($this->once())->method('createForm')->will($this->returnValue($this->form));
        $this->controller->expects($this->atLeastOnce())->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $result = $this->controller->editAction(1);
        $this->assertNull($result);
    }

    public function testEditActionNotFound()
    {
        $this->controller->expects($this->atLeastOnce())->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        $this->controller->editAction(0);
    }

    public function testDeleteAction()
    {
        $this->request = $this->getFakeRequest();
        $this->request->expects($this->once())->method('getMethod')->will($this->returnValue('GET'));
        $this->controller->expects($this->once())->method('getRequest')->will($this->returnValue($this->request));
        $this->controller->expects($this->atLeastOnce())->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $result = $this->controller->deleteAction(1);
        $this->assertArrayHasKey('entity', $result);
        $this->assertInstanceOf('VIB\CoreBundle\Entity\Entity',$result['entity']);
    }

    public function testDeleteActionSubmit()
    {
        $this->request = $this->getFakeRequest();
        $this->request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));
        $this->objectManager = $this->getFakeObjectManager();
        $this->objectManager->expects($this->once())->method('remove');
        $this->objectManager->expects($this->once())->method('flush');
        $this->controller->expects($this->once())->method('getRequest')->will($this->returnValue($this->request));
        $this->controller->expects($this->atLeastOnce())->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $result = $this->controller->deleteAction(1);
        $this->assertNull($result);
    }

    public function testDeleteActionNotFound()
    {
        $this->controller->expects($this->atLeastOnce())->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        $this->controller->deleteAction(0);
    }

    protected function setUp()
    {
        $this->entityRepository = $this->getFakeEntityRepository();
        $this->securityContext = $this->getFakeSecurityContext();
        $this->request = $this->getFakeRequest();
        $this->form = $this->getFakeForm();
        $this->objectManager = $this->getFakeObjectManager();
        $this->controller = $this->getFakeController();
    }

    private function getFakeController()
    {
        $methods = array(
            'getCurrentPage', 'getPaginator', 'getObjectManager',
            'getSecurityContext', 'getUser', 'getAclFilter', 'getEntityClass',
            'createForm','getRequest', 'addSessionFlash', 'generateUrl', 'redirect'
        );

        $controller =
                $this->getMockBuilder('VIB\CoreBundle\Controller\CRUDController')
                     ->setMethods($methods)
                     ->getMockForAbstractClass();
        $controller->expects($this->any())
            ->method('getCurrentPage')
            ->will($this->returnValue(0));
        $controller->expects($this->any())
            ->method('getPaginator')
            ->will($this->returnValue($this->getMock('Knp\Component\Pager\Paginator')));
        $controller->expects($this->any())
            ->method('getSecurityContext')
            ->will($this->returnValue($this->securityContext));
        $controller->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->getMock('Symfony\Component\Security\Core\User\UserInterface')));
        $controller->expects($this->any())
            ->method('getEntityClass')
            ->will($this->returnValue('VIB\CoreBundle\Entity\Entity'));
        $controller->expects($this->any())
            ->method('addSessionFlash')
            ->will($this->returnValue(true));

        return $controller;
    }

    private function getFakeEntityRepository()
    {
        $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()->setMethods(array('getListCount','getListQuery'))
            ->getMock();
        $repository->expects($this->any())
            ->method('getListCount')
            ->will($this->returnValue(25));
        $repository->expects($this->any())
            ->method('getListQuery')
            ->will($this->returnValue($query));

        return $repository;
    }

    private function getFakeObjectManager()
    {
        $map = array(
          array('VIB\CoreBundle\Entity\Entity', 0, array(), null),
          array('VIB\CoreBundle\Entity\Entity', 1, array(), new Entity())
        );

        $om = $this->getMockBuilder('VIB\CoreBundle\Doctrine\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $om->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->entityRepository));
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

    private function getFakeForm()
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        return $form;
    }

    private function getFakeRequest()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $request->attributes = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');

        return $request;
    }
}
