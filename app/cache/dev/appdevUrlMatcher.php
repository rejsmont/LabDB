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

        // _assetic_8c57670
        if ($pathinfo === '/js/8c57670.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => NULL,  '_format' => 'js',  '_route' => '_assetic_8c57670',);
        }

        // _assetic_8c57670_0
        if ($pathinfo === '/js/8c57670_jquery-1.7_1.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 0,  '_format' => 'js',  '_route' => '_assetic_8c57670_0',);
        }

        // _assetic_8c57670_1
        if ($pathinfo === '/js/8c57670_jquery.effects.core_2.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 1,  '_format' => 'js',  '_route' => '_assetic_8c57670_1',);
        }

        // _assetic_8c57670_2
        if ($pathinfo === '/js/8c57670_jquery.ui.core_3.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 2,  '_format' => 'js',  '_route' => '_assetic_8c57670_2',);
        }

        // _assetic_8c57670_3
        if ($pathinfo === '/js/8c57670_jquery.ui.widget_4.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 3,  '_format' => 'js',  '_route' => '_assetic_8c57670_3',);
        }

        // _assetic_8c57670_4
        if ($pathinfo === '/js/8c57670_jquery.ui.position_5.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 4,  '_format' => 'js',  '_route' => '_assetic_8c57670_4',);
        }

        // _assetic_8c57670_5
        if ($pathinfo === '/js/8c57670_jquery.ui.mouse_6.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 5,  '_format' => 'js',  '_route' => '_assetic_8c57670_5',);
        }

        // _assetic_8c57670_6
        if ($pathinfo === '/js/8c57670_jquery.ui.menu_7.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 6,  '_format' => 'js',  '_route' => '_assetic_8c57670_6',);
        }

        // _assetic_8c57670_7
        if ($pathinfo === '/js/8c57670_part_8_jquery.effects.blind_1.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 7,  '_format' => 'js',  '_route' => '_assetic_8c57670_7',);
        }

        // _assetic_8c57670_8
        if ($pathinfo === '/js/8c57670_part_8_jquery.effects.bounce_2.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 8,  '_format' => 'js',  '_route' => '_assetic_8c57670_8',);
        }

        // _assetic_8c57670_9
        if ($pathinfo === '/js/8c57670_part_8_jquery.effects.clip_3.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 9,  '_format' => 'js',  '_route' => '_assetic_8c57670_9',);
        }

        // _assetic_8c57670_10
        if ($pathinfo === '/js/8c57670_part_8_jquery.effects.drop_4.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 10,  '_format' => 'js',  '_route' => '_assetic_8c57670_10',);
        }

        // _assetic_8c57670_11
        if ($pathinfo === '/js/8c57670_part_8_jquery.effects.explode_5.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 11,  '_format' => 'js',  '_route' => '_assetic_8c57670_11',);
        }

        // _assetic_8c57670_12
        if ($pathinfo === '/js/8c57670_part_8_jquery.effects.fade_6.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 12,  '_format' => 'js',  '_route' => '_assetic_8c57670_12',);
        }

        // _assetic_8c57670_13
        if ($pathinfo === '/js/8c57670_part_8_jquery.effects.fold_7.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 13,  '_format' => 'js',  '_route' => '_assetic_8c57670_13',);
        }

        // _assetic_8c57670_14
        if ($pathinfo === '/js/8c57670_part_8_jquery.effects.highlight_8.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 14,  '_format' => 'js',  '_route' => '_assetic_8c57670_14',);
        }

        // _assetic_8c57670_15
        if ($pathinfo === '/js/8c57670_part_8_jquery.effects.pulsate_9.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 15,  '_format' => 'js',  '_route' => '_assetic_8c57670_15',);
        }

        // _assetic_8c57670_16
        if ($pathinfo === '/js/8c57670_part_8_jquery.effects.scale_10.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 16,  '_format' => 'js',  '_route' => '_assetic_8c57670_16',);
        }

        // _assetic_8c57670_17
        if ($pathinfo === '/js/8c57670_part_8_jquery.effects.shake_11.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 17,  '_format' => 'js',  '_route' => '_assetic_8c57670_17',);
        }

        // _assetic_8c57670_18
        if ($pathinfo === '/js/8c57670_part_8_jquery.effects.slide_12.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 18,  '_format' => 'js',  '_route' => '_assetic_8c57670_18',);
        }

        // _assetic_8c57670_19
        if ($pathinfo === '/js/8c57670_part_8_jquery.effects.transfer_13.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 19,  '_format' => 'js',  '_route' => '_assetic_8c57670_19',);
        }

        // _assetic_8c57670_20
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.accordion_1.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 20,  '_format' => 'js',  '_route' => '_assetic_8c57670_20',);
        }

        // _assetic_8c57670_21
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.autocomplete_2.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 21,  '_format' => 'js',  '_route' => '_assetic_8c57670_21',);
        }

        // _assetic_8c57670_22
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.button_3.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 22,  '_format' => 'js',  '_route' => '_assetic_8c57670_22',);
        }

        // _assetic_8c57670_23
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.checkbox_4.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 23,  '_format' => 'js',  '_route' => '_assetic_8c57670_23',);
        }

        // _assetic_8c57670_24
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.datepicker_5.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 24,  '_format' => 'js',  '_route' => '_assetic_8c57670_24',);
        }

        // _assetic_8c57670_25
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.dialog_6.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 25,  '_format' => 'js',  '_route' => '_assetic_8c57670_25',);
        }

        // _assetic_8c57670_26
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.draggable_7.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 26,  '_format' => 'js',  '_route' => '_assetic_8c57670_26',);
        }

        // _assetic_8c57670_27
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.droppable_8.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 27,  '_format' => 'js',  '_route' => '_assetic_8c57670_27',);
        }

        // _assetic_8c57670_28
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.menubar_9.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 28,  '_format' => 'js',  '_route' => '_assetic_8c57670_28',);
        }

        // _assetic_8c57670_29
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.popup_10.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 29,  '_format' => 'js',  '_route' => '_assetic_8c57670_29',);
        }

        // _assetic_8c57670_30
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.position_11.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 30,  '_format' => 'js',  '_route' => '_assetic_8c57670_30',);
        }

        // _assetic_8c57670_31
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.progressbar_12.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 31,  '_format' => 'js',  '_route' => '_assetic_8c57670_31',);
        }

        // _assetic_8c57670_32
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.resizable_13.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 32,  '_format' => 'js',  '_route' => '_assetic_8c57670_32',);
        }

        // _assetic_8c57670_33
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.selectable_14.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 33,  '_format' => 'js',  '_route' => '_assetic_8c57670_33',);
        }

        // _assetic_8c57670_34
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.selectmenu_15.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 34,  '_format' => 'js',  '_route' => '_assetic_8c57670_34',);
        }

        // _assetic_8c57670_35
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.slider_16.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 35,  '_format' => 'js',  '_route' => '_assetic_8c57670_35',);
        }

        // _assetic_8c57670_36
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.sortable_17.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 36,  '_format' => 'js',  '_route' => '_assetic_8c57670_36',);
        }

        // _assetic_8c57670_37
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.spinner_18.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 37,  '_format' => 'js',  '_route' => '_assetic_8c57670_37',);
        }

        // _assetic_8c57670_38
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.tabs_19.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 38,  '_format' => 'js',  '_route' => '_assetic_8c57670_38',);
        }

        // _assetic_8c57670_39
        if ($pathinfo === '/js/8c57670_part_9_jquery.ui.tooltip_20.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 39,  '_format' => 'js',  '_route' => '_assetic_8c57670_39',);
        }

        // _assetic_8c57670_40
        if ($pathinfo === '/js/8c57670_part_10_forms_1.js') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 40,  '_format' => 'js',  '_route' => '_assetic_8c57670_40',);
        }

        // _assetic_399a29c
        if ($pathinfo === '/css/399a29c.css') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => NULL,  '_format' => 'css',  '_route' => '_assetic_399a29c',);
        }

        // _assetic_399a29c_0
        if ($pathinfo === '/css/399a29c_jquery.ui.all_1.css') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 0,  '_format' => 'css',  '_route' => '_assetic_399a29c_0',);
        }

        // _assetic_399a29c_1
        if ($pathinfo === '/css/399a29c_jquery.ui.selectmenu_2.css') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 1,  '_format' => 'css',  '_route' => '_assetic_399a29c_1',);
        }

        // _assetic_399a29c_2
        if ($pathinfo === '/css/399a29c_jquery.ui.checkbox_3.css') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 2,  '_format' => 'css',  '_route' => '_assetic_399a29c_2',);
        }

        // _assetic_399a29c_3
        if ($pathinfo === '/css/399a29c_part_4_general_1.css') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 3,  '_format' => 'css',  '_route' => '_assetic_399a29c_3',);
        }

        // _assetic_399a29c_4
        if ($pathinfo === '/css/399a29c_part_4_nav_top_2.css') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 4,  '_format' => 'css',  '_route' => '_assetic_399a29c_4',);
        }

        // _assetic_399a29c_5
        if ($pathinfo === '/css/399a29c_part_4_pager_3.css') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 5,  '_format' => 'css',  '_route' => '_assetic_399a29c_5',);
        }

        // _assetic_399a29c_6
        if ($pathinfo === '/css/399a29c_part_4_search_4.css') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 6,  '_format' => 'css',  '_route' => '_assetic_399a29c_6',);
        }

        // _assetic_399a29c_7
        if ($pathinfo === '/css/399a29c_part_4_table_data_5.css') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 7,  '_format' => 'css',  '_route' => '_assetic_399a29c_7',);
        }

        // _assetic_399a29c_8
        if ($pathinfo === '/css/399a29c_part_4_table_ui_6.css') {
            return array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 8,  '_format' => 'css',  '_route' => '_assetic_399a29c_8',);
        }

        // _wdt
        if (preg_match('#^/_wdt/(?P<token>[^/]+?)$#xs', $pathinfo, $matches)) {
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
            if (0 === strpos($pathinfo, '/_profiler/export') && preg_match('#^/_profiler/export/(?P<token>[^/\\.]+?)\\.txt$#xs', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::exportAction',)), array('_route' => '_profiler_export'));
            }

            // _profiler_search_results
            if (preg_match('#^/_profiler/(?P<token>[^/]+?)/search/results$#xs', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Symfony\\Bundle\\WebProfilerBundle\\Controller\\ProfilerController::searchResultsAction',)), array('_route' => '_profiler_search_results'));
            }

            // _profiler
            if (preg_match('#^/_profiler/(?P<token>[^/]+?)$#xs', $pathinfo, $matches)) {
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
            if (0 === strpos($pathinfo, '/_configurator/step') && preg_match('#^/_configurator/step/(?P<index>[^/]+?)$#xs', $pathinfo, $matches)) {
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
            return array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::listAction',  '_route' => 'default',);
        }

        // flystock_list
        if (rtrim($pathinfo, '/') === '/stocks') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'flystock_list');
            }
            return array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyStockController::listAction',  '_route' => 'flystock_list',);
        }

        // flystock_listpage
        if (0 === strpos($pathinfo, '/stocks/page') && preg_match('#^/stocks/page/(?P<page>[^/]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyStockController::listAction',)), array('_route' => 'flystock_listpage'));
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

        // flycross_listpage
        if (0 === strpos($pathinfo, '/crosses/page') && preg_match('#^/crosses/page/(?P<page>[^/]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyCrossController::listAction',)), array('_route' => 'flycross_listpage'));
        }

        // flycross_select
        if ($pathinfo === '/crosses/select') {
            return array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyCrossController::selectAction',  '_route' => 'flycross_select',);
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

        // flyvial_listpage
        if (0 === strpos($pathinfo, '/vials/page') && preg_match('#^/vials/page/(?P<page>[^/]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::listAction',)), array('_route' => 'flyvial_listpage'));
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

        // flyvial_expand
        if (rtrim($pathinfo, '/') === '/vials/expand') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'flyvial_expand');
            }
            return array (  'id' => 0,  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::expandAction',  '_route' => 'flyvial_expand',);
        }

        // flyvial_expand_id
        if (0 === strpos($pathinfo, '/vials/expand') && preg_match('#^/vials/expand/(?P<id>[^/]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::expandAction',)), array('_route' => 'flyvial_expand_id'));
        }

        // ajax_vial_format
        if (0 === strpos($pathinfo, '/ajax/vials') && preg_match('#^/ajax/vials/(?P<id>[^/\\.]+?)\\.(?P<format>[^\\.]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\AJAXController::vialAction',)), array('_route' => 'ajax_vial_format'));
        }

        // ajax_vial_filter_format
        if (0 === strpos($pathinfo, '/ajax/vials') && preg_match('#^/ajax/vials/(?P<filter>[^/]+?)/(?P<id>[^/\\.]+?)\\.(?P<format>[^\\.]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\AJAXController::vialAction',)), array('_route' => 'ajax_vial_filter_format'));
        }

        // ajax_vial
        if (0 === strpos($pathinfo, '/ajax/vials') && preg_match('#^/ajax/vials/(?P<id>[^/]+?)/?$#xs', $pathinfo, $matches)) {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'ajax_vial');
            }
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\AJAXController::vialAction',)), array('_route' => 'ajax_vial'));
        }

        // ajax_vial_filter
        if (0 === strpos($pathinfo, '/ajax/vials') && preg_match('#^/ajax/vials/(?P<filter>[^/]+?)/(?P<id>[^/]+?)/?$#xs', $pathinfo, $matches)) {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'ajax_vial_filter');
            }
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\AJAXController::vialAction',)), array('_route' => 'ajax_vial_filter'));
        }

        // searchResult
        if (rtrim($pathinfo, '/') === '/search/result') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'searchResult');
            }
            return array (  '_controller' => 'VIB\\FliesBundle\\Controller\\SearchController::searchResultAction',  '_route' => 'searchResult',);
        }

        // searchResultPage
        if (0 === strpos($pathinfo, '/search/result/page') && preg_match('#^/search/result/page/(?P<page>[^/]+?)$#xs', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'VIB\\FliesBundle\\Controller\\SearchController::searchResultAction',)), array('_route' => 'searchResultPage'));
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
