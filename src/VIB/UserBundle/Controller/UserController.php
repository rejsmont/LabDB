<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VIB\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * User controller
 *
 * @Route("/users")
 */
class UserController extends ContainerAware
{
    /**
     * @Route("/")
     */
    public function listAction()
    {
        $groups = $this->container->get('fos_user.group_manager')->findGroups();

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:list.html.'.$this->getEngine(), array('groups' => $groups));
    }

    /**
     * @Route("/show/{username}")
     */
    public function showAction($username)
    {
        $group = $this->findUserBy('name', $username);

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:show.html.'.$this->getEngine(), array('group' => $group));
    }

    /**
     * @Route("/edit/{username}")
     */
    public function editAction($username)
    {
        $group = $this->findUserBy('name', $username);
        $form = $this->container->get('fos_user.group.form');
        $formHandler = $this->container->get('fos_user.group.form.handler');

        $process = $formHandler->process($group);
        if ($process) {
            $this->setFlash('fos_user_success', 'group.flash.updated');
            $groupUrl =  $this->container->get('router')->generate('fos_user_group_show', array('groupname' => $group->getName()));

            return new RedirectResponse($groupUrl);
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:edit.html.'.$this->getEngine(), array(
            'form'      => $form->createview(),
            'groupname'  => $group->getName(),
        ));
    }

    /**
     * @Route("/new")
     */
    public function newAction()
    {
        $form = $this->container->get('fos_user.group.form');
        $formHandler = $this->container->get('fos_user.group.form.handler');

        $process = $formHandler->process();
        if ($process) {
            $this->setFlash('fos_user_success', 'group.flash.created');
            $parameters = array('groupname' => $form->getData('group')->getName());
            $url = $this->container->get('router')->generate('fos_user_group_show', $parameters);

            return new RedirectResponse($url);
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:new.html.'.$this->getEngine(), array(
            'form' => $form->createview(),
        ));
    }

    /**
     * @Route("/delete/{id}")
     */
    public function deleteAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);
        $this->container->get('fos_user.group_manager')->deleteGroup($group);
        $this->setFlash('fos_user_success', 'group.flash.deleted');

        return new RedirectResponse($this->container->get('router')->generate('fos_user_group_list'));
    }

    /**
     * Find a group by a specific property
     *
     * @param string $key   property name
     * @param mixed  $value property value
     *
     * @throws NotFoundException                    if user does not exist
     * @return \FOS\UserBundle\Model\GroupInterface
     */
    protected function findUserBy($key, $value)
    {
        if (!empty($value)) {
            $group = $this->container->get('fos_user.group_manager')->{'findGroupBy'.ucfirst($key)}($value);
        }

        if (empty($group)) {
            throw new NotFoundHttpException(sprintf('The group with "%s" does not exist for value "%s"', $key, $value));
        }

        return $group;
    }
    
    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value)
    {
        $this->container->get('session')->getFlashBag()->set($action, $value);
    }
}
