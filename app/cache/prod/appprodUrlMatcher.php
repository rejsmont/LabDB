<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * appprodUrlMatcher
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appprodUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($pathinfo)
    {
        $allow = array();
        $pathinfo = urldecode($pathinfo);

        // default
        if (rtrim($pathinfo, '/') === '') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'default');
            }
            return array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::listAction',  '_route' => 'default',);
        }

        // flystock_list
        if (rtrim($pathinfo, '/') === '/stocks') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'flystock_list');
            }
            return array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyStockController::listAction',  '_route' => 'flystock_list',);
        }

        // flystock_show
        if (0 === strpos($pathinfo, '/stocks/show') && preg_match('#^/stocks/show/(?P<id>[^/]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyStockController::showAction',)), array('_route' => 'flystock_show'));
        }

        // flystock_create
        if ($pathinfo === '/stocks/new') {
            return array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyStockController::createAction',  '_route' => 'flystock_create',);
        }

        // flystock_edit
        if (0 === strpos($pathinfo, '/stocks/edit') && preg_match('#^/stocks/edit/(?P<id>[^/]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyStockController::editAction',)), array('_route' => 'flystock_edit'));
        }

        // flystock_delete
        if (0 === strpos($pathinfo, '/stocks/delete') && preg_match('#^/stocks/delete/(?P<id>[^/]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyStockController::deleteAction',)), array('_route' => 'flystock_delete'));
        }

        // flycross_list
        if (rtrim($pathinfo, '/') === '/crosses') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'flycross_list');
            }
            return array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyCrossController::listAction',  '_route' => 'flycross_list',);
        }

        // flycross_show
        if (0 === strpos($pathinfo, '/crosses/show') && preg_match('#^/crosses/show/(?P<id>[^/]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyCrossController::showAction',)), array('_route' => 'flycross_show'));
        }

        // flycross_create
        if ($pathinfo === '/crosses/new') {
            return array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyCrossController::createAction',  '_route' => 'flycross_create',);
        }

        // flycross_edit
        if (0 === strpos($pathinfo, '/crosses/edit') && preg_match('#^/crosses/edit/(?P<id>[^/]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyCrossController::editAction',)), array('_route' => 'flycross_edit'));
        }

        // flycross_delete
        if (0 === strpos($pathinfo, '/crosses/delete') && preg_match('#^/crosses/delete/(?P<id>[^/]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyCrossController::deleteAction',)), array('_route' => 'flycross_delete'));
        }

        // flyvial_list
        if ($pathinfo === '/vials') {
            return array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::listAction',  '_route' => 'flyvial_list',);
        }

        // flyvial_listfilter
        if (0 === strpos($pathinfo, '/vials/list') && preg_match('#^/vials/list(?:/(?P<filter>[^/]+?))?$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  'filter' => 'living',  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::listAction',)), array('_route' => 'flyvial_listfilter'));
        }

        // flyvial_select
        if ($pathinfo === '/vials/select') {
            return array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::selectAction',  '_route' => 'flyvial_select',);
        }

        // flyvial_show
        if (0 === strpos($pathinfo, '/vials/show') && preg_match('#^/vials/show/(?P<id>[^/]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::showAction',)), array('_route' => 'flyvial_show'));
        }

        // flyvial_create
        if ($pathinfo === '/vials/new') {
            return array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::createAction',  '_route' => 'flyvial_create',);
        }

        // flyvial_edit
        if (0 === strpos($pathinfo, '/vials/edit') && preg_match('#^/vials/edit/(?P<id>[^/]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::editAction',)), array('_route' => 'flyvial_edit'));
        }

        // flyvial_delete
        if (0 === strpos($pathinfo, '/vials/delete') && preg_match('#^/vials/delete/(?P<id>[^/]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::deleteAction',)), array('_route' => 'flyvial_delete'));
        }

        // ajax_vial_json
        if (0 === strpos($pathinfo, '/ajax/vials') && preg_match('#^/ajax/vials/(?P<id>[^/\\.]+?)\\.(?P<format>[^\\.]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\AJAXController::vialAction',)), array('_route' => 'ajax_vial_json'));
        }

        // ajax_vial
        if (0 === strpos($pathinfo, '/ajax/vials') && preg_match('#^/ajax/vials/(?P<id>[^/]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\AJAXController::vialAction',)), array('_route' => 'ajax_vial'));
        }

        // login
        if ($pathinfo === '/login') {
            return array (  '_controller' => 'VIB\\SecurityBundle\\Controller\\SecurityController::loginAction',  '_route' => 'login',);
        }

        // login_check
        if ($pathinfo === '/login_check') {
            return array('_route' => 'login_check');
        }

        // logout
        if ($pathinfo === '/logout') {
            return array('_route' => 'logout');
        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
