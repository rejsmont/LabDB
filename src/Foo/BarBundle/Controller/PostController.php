<?php

namespace Foo\BarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PostController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $q = $this->get('doctrine')->getEntityManager()->createQuery('SELECT o FROM FooBarBundle:Post o');

        return array('objects' => $q->getResult());
    }
}
