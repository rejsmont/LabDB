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

namespace VIB\CoreBundle\Tests\Request\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use VIB\CoreBundle\Request\ParamConverter\DoctrineParamConverter;


class DoctrineParamConverterTest extends \PHPUnit_Framework_TestCase
{
    private $converter;
    private $registry;

    
    public function testApply()
    {
        $request = new Request();
        $request->attributes->set('id', 1);

        $config = $this->createConfiguration('stdClass', array('id' => 'id'), 'arg');

        $manager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->registry->expects($this->once())
              ->method('getManagerForClass')
              ->with('stdClass')
              ->will($this->returnValue($manager));

        $manager->expects($this->once())
            ->method('getRepository')
            ->with('stdClass')
            ->will($this->returnValue($objectRepository));

        $objectRepository->expects($this->once())
                      ->method('find')
                      ->with($this->equalTo(1))
                      ->will($this->returnValue($object = new \stdClass));

        $ret = $this->converter->apply($request, $config);

        $this->assertTrue($ret);
        $this->assertSame($object, $request->attributes->get('arg'));
    }
    
    public function testApplyNotFound()
    {
        $request = new Request();
        $request->attributes->set('id', 1);

        $config = $this->createConfiguration('stdClass', array('id' => 'id'), 'arg');

        $manager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->registry->expects($this->once())
              ->method('getManagerForClass')
              ->with('stdClass')
              ->will($this->returnValue($manager));

        $manager->expects($this->once())
            ->method('getRepository')
            ->with('stdClass')
            ->will($this->returnValue($objectRepository));

        $objectRepository->expects($this->once())
                      ->method('find')
                      ->with($this->equalTo(1))
                      ->will($this->returnValue(null));

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        $this->converter->apply($request, $config);
    }
    
    protected function setUp()
    {        
        $this->registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $this->converter = new DoctrineParamConverter($this->registry);
    }
    
    private function createConfiguration($class = null, array $options = null, $name = 'arg', $isOptional = false)
    {
        $methods = array('getClass', 'getAliasName', 'getOptions', 'getName', 'allowArray');
        if (null !== $isOptional) {
            $methods[] = 'isOptional';
        }
        $config = $this
            ->getMockBuilder('Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter')
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
        if ($options !== null) {
            $config->expects($this->exactly(2))
                   ->method('getOptions')
                   ->will($this->returnValue($options));
        }
        if ($class !== null) {
            $config->expects($this->any())
                   ->method('getClass')
                   ->will($this->returnValue($class));
        }
        $config->expects($this->any())
               ->method('getName')
               ->will($this->returnValue($name));
        if (null !== $isOptional) {
            $config->expects($this->any())
                   ->method('isOptional')
                   ->will($this->returnValue($isOptional));
        }

        return $config;
    }
}
