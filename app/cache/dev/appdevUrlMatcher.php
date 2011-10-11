<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * appdevUrlMatcher
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appdevUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
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

        // _wdt
        if (preg_match('#^/_wdt/(?P<token>[^/]+?)$#x', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::toolbarAction',)), array('_route' => '_wdt'));
        }

        if (0 === strpos($pathinfo, '/_profiler')) {
            // _profiler_search
            if ($pathinfo === '/_profiler/search') {
                return array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::searchAction',  '_route' => '_profiler_search',);
            }

            // _profiler_purge
            if ($pathinfo === '/_profiler/purge') {
                return array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::purgeAction',  '_route' => '_profiler_purge',);
            }

            // _profiler_import
            if ($pathinfo === '/_profiler/import') {
                return array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::importAction',  '_route' => '_profiler_import',);
            }

            // _profiler_export
            if (0 === strpos($pathinfo, '/_profiler/export') && preg_match('#^/_profiler/export/(?P<token>[^/\\.]+?)\\.txt$#x', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::exportAction',)), array('_route' => '_profiler_export'));
            }

            // _profiler_search_results
            if (preg_match('#^/_profiler/(?P<token>[^/]+?)/search/results$#x', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::searchResultsAction',)), array('_route' => '_profiler_search_results'));
            }

            // _profiler
            if (preg_match('#^/_profiler/(?P<token>[^/]+?)$#x', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::panelAction',)), array('_route' => '_profiler'));
            }

        }

        if (0 === strpos($pathinfo, '/_configurator')) {
            // _configurator_home
            if (rtrim($pathinfo, '/') === '/_configurator') {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($pathinfo.'/', '_configurator_home');
                }
                return array (  '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::checkAction',  '_route' => '_configurator_home',);
            }

            // _configurator_step
            if (0 === strpos($pathinfo, '/_configurator/step') && preg_match('#^/_configurator/step/(?P<index>[^/]+?)$#x', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::stepAction',)), array('_route' => '_configurator_step'));
            }

            // _configurator_final
            if ($pathinfo === '/_configurator/final') {
                return array (  '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::finalAction',  '_route' => '_configurator_final',);
            }

        }

        // default
        if (rtrim($pathinfo, '/') === '') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'default');
            }
            return array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyStockController::indexAction',  '_route' => 'default',);
        }

        // flystock_list
        if (rtrim($pathinfo, '/') === '/stocks') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'flystock_list');
            }
            return array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyStockController::listAction',  '_route' => 'flystock_list',);
        }

        // flystock_show
        if (0 === strpos($pathinfo, '/stocks/show') && preg_match('#^/stocks/show/(?P<id>[^/]+?)$#x', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyStockController::showAction',)), array('_route' => 'flystock_show'));
        }

        // flystock_create
        if ($pathinfo === '/stocks/new') {
            return array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyStockController::createAction',  '_route' => 'flystock_create',);
        }

        // flystock_edit
        if (0 === strpos($pathinfo, '/stocks/edit') && preg_match('#^/stocks/edit/(?P<id>[^/]+?)$#x', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyStockController::editAction',)), array('_route' => 'flystock_edit'));
        }

        // flystock_delete
        if (0 === strpos($pathinfo, '/stocks/delete') && preg_match('#^/stocks/delete/(?P<id>[^/]+?)$#x', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyStockController::deleteAction',)), array('_route' => 'flystock_delete'));
        }

        // flycross_list
        if (rtrim($pathinfo, '/') === '/crosses') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'flycross_list');
            }
            return array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyCrossController::listAction',  '_route' => 'flycross_list',);
        }

        // flycross_show
        if (0 === strpos($pathinfo, '/crosses/show') && preg_match('#^/crosses/show/(?P<id>[^/]+?)$#x', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyCrossController::showAction',)), array('_route' => 'flycross_show'));
        }

        // flycross_create
        if ($pathinfo === '/crosses/new') {
            return array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyCrossController::createAction',  '_route' => 'flycross_create',);
        }

        // flycross_edit
        if (0 === strpos($pathinfo, '/crosses/edit') && preg_match('#^/crosses/edit/(?P<id>[^/]+?)$#x', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyCrossController::editAction',)), array('_route' => 'flycross_edit'));
        }

        // flycross_delete
        if (0 === strpos($pathinfo, '/crosses/delete') && preg_match('#^/crosses/delete/(?P<id>[^/]+?)$#x', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyCrossController::deleteAction',)), array('_route' => 'flycross_delete'));
        }

        // flyvial_list
        if (0 === strpos($pathinfo, '/vials/list') && preg_match('#^/vials/list/(?P<filter>[^/]+?)$#x', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyVialController::listAction',)), array('_route' => 'flyvial_list'));
        }

        // flyvial_show
        if (0 === strpos($pathinfo, '/vials/show') && preg_match('#^/vials/show/(?P<id>[^/]+?)$#x', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyVialController::showAction',)), array('_route' => 'flyvial_show'));
        }

        // flyvial_create
        if ($pathinfo === '/vials/new') {
            return array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyVialController::createAction',  '_route' => 'flyvial_create',);
        }

        // flyvial_edit
        if (0 === strpos($pathinfo, '/vials/edit') && preg_match('#^/vials/edit/(?P<id>[^/]+?)$#x', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyVialController::editAction',)), array('_route' => 'flyvial_edit'));
        }

        // flyvial_delete
        if (0 === strpos($pathinfo, '/vials/delete') && preg_match('#^/vials/delete/(?P<id>[^/]+?)$#x', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyVialController::deleteAction',)), array('_route' => 'flyvial_delete'));
        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
