<?php

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\RouteNotFoundException;


/**
 * appdevUrlGenerator
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appdevUrlGenerator extends Symfony\Component\Routing\Generator\UrlGenerator
{
    static private $declaredRouteNames = array(
       '_wdt' => true,
       '_profiler_search' => true,
       '_profiler_purge' => true,
       '_profiler_import' => true,
       '_profiler_export' => true,
       '_profiler_search_results' => true,
       '_profiler' => true,
       '_configurator_home' => true,
       '_configurator_step' => true,
       '_configurator_final' => true,
       'default' => true,
       'flystock_list' => true,
       'flystock_show' => true,
       'flystock_create' => true,
       'flystock_edit' => true,
       'flystock_delete' => true,
       'flycross_list' => true,
       'flycross_show' => true,
       'flycross_create' => true,
       'flycross_edit' => true,
       'flycross_delete' => true,
       'culturebottle_list' => true,
       'culturebottle_show' => true,
       'culturebottle_create' => true,
       'culturebottle_edit' => true,
       'culturebottle_delete' => true,
    );

    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function generate($name, array $parameters = array(), $absolute = false)
    {
        if (!isset(self::$declaredRouteNames[$name])) {
            throw new RouteNotFoundException(sprintf('Route "%s" does not exist.', $name));
        }

        $escapedName = str_replace('.', '__', $name);

        list($variables, $defaults, $requirements, $tokens) = $this->{'get'.$escapedName.'RouteInfo'}();

        return $this->doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute);
    }

    private function get_wdtRouteInfo()
    {
        return array(array (  0 => 'token',), array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::toolbarAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'token',  ),  1 =>   array (    0 => 'text',    1 => '/_wdt',  ),));
    }

    private function get_profiler_searchRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::searchAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/_profiler/search',  ),));
    }

    private function get_profiler_purgeRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::purgeAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/_profiler/purge',  ),));
    }

    private function get_profiler_importRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::importAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/_profiler/import',  ),));
    }

    private function get_profiler_exportRouteInfo()
    {
        return array(array (  0 => 'token',), array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::exportAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '.txt',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/\\.]+?',    3 => 'token',  ),  2 =>   array (    0 => 'text',    1 => '/_profiler/export',  ),));
    }

    private function get_profiler_search_resultsRouteInfo()
    {
        return array(array (  0 => 'token',), array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::searchResultsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/search/results',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'token',  ),  2 =>   array (    0 => 'text',    1 => '/_profiler',  ),));
    }

    private function get_profilerRouteInfo()
    {
        return array(array (  0 => 'token',), array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::panelAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'token',  ),  1 =>   array (    0 => 'text',    1 => '/_profiler',  ),));
    }

    private function get_configurator_homeRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Symfony\\Bundle\\WebConfiguratorBundle\\Controller\\ConfiguratorController::checkAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/_configurator/',  ),));
    }

    private function get_configurator_stepRouteInfo()
    {
        return array(array (  0 => 'index',), array (  '_controller' => 'Symfony\\Bundle\\WebConfiguratorBundle\\Controller\\ConfiguratorController::stepAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'index',  ),  1 =>   array (    0 => 'text',    1 => '/_configurator/step',  ),));
    }

    private function get_configurator_finalRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Symfony\\Bundle\\WebConfiguratorBundle\\Controller\\ConfiguratorController::finalAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/_configurator/final',  ),));
    }

    private function getdefaultRouteInfo()
    {
        return array(array (), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyStockController::indexAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/',  ),));
    }

    private function getflystock_listRouteInfo()
    {
        return array(array (), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyStockController::listAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/stocks/',  ),));
    }

    private function getflystock_showRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyStockController::showAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/stocks/show',  ),));
    }

    private function getflystock_createRouteInfo()
    {
        return array(array (), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyStockController::createAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/stocks/new',  ),));
    }

    private function getflystock_editRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyStockController::editAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/stocks/edit',  ),));
    }

    private function getflystock_deleteRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyStockController::deleteAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/stocks/delete',  ),));
    }

    private function getflycross_listRouteInfo()
    {
        return array(array (), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyCrossController::listAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/crosses/',  ),));
    }

    private function getflycross_showRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyCrossController::showAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/crosses/show',  ),));
    }

    private function getflycross_createRouteInfo()
    {
        return array(array (), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyCrossController::createAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/crosses/new',  ),));
    }

    private function getflycross_editRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyCrossController::editAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/crosses/edit',  ),));
    }

    private function getflycross_deleteRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\FlyCrossController::deleteAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/crosses/delete',  ),));
    }

    private function getculturebottle_listRouteInfo()
    {
        return array(array (  0 => 'filter',), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\CultureBottleController::listAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'filter',  ),  1 =>   array (    0 => 'text',    1 => '/bottles',  ),));
    }

    private function getculturebottle_showRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\CultureBottleController::showAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/bottles/show',  ),));
    }

    private function getculturebottle_createRouteInfo()
    {
        return array(array (), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\CultureBottleController::createAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/bottles/new',  ),));
    }

    private function getculturebottle_editRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\CultureBottleController::editAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/bottles/edit',  ),));
    }

    private function getculturebottle_deleteRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'MpiCbg\\FliesBundle\\Controller\\CultureBottleController::deleteAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/bottles/delete',  ),));
    }
}
