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
       '_assetic_8c57670' => true,
       '_assetic_8c57670_0' => true,
       '_assetic_8c57670_1' => true,
       '_assetic_8c57670_2' => true,
       '_assetic_8c57670_3' => true,
       '_assetic_8c57670_4' => true,
       '_assetic_8c57670_5' => true,
       '_assetic_8c57670_6' => true,
       '_assetic_8c57670_7' => true,
       '_assetic_8c57670_8' => true,
       '_assetic_8c57670_9' => true,
       '_assetic_8c57670_10' => true,
       '_assetic_8c57670_11' => true,
       '_assetic_8c57670_12' => true,
       '_assetic_8c57670_13' => true,
       '_assetic_8c57670_14' => true,
       '_assetic_8c57670_15' => true,
       '_assetic_8c57670_16' => true,
       '_assetic_8c57670_17' => true,
       '_assetic_8c57670_18' => true,
       '_assetic_8c57670_19' => true,
       '_assetic_8c57670_20' => true,
       '_assetic_8c57670_21' => true,
       '_assetic_8c57670_22' => true,
       '_assetic_8c57670_23' => true,
       '_assetic_8c57670_24' => true,
       '_assetic_8c57670_25' => true,
       '_assetic_8c57670_26' => true,
       '_assetic_8c57670_27' => true,
       '_assetic_8c57670_28' => true,
       '_assetic_8c57670_29' => true,
       '_assetic_8c57670_30' => true,
       '_assetic_8c57670_31' => true,
       '_assetic_8c57670_32' => true,
       '_assetic_8c57670_33' => true,
       '_assetic_8c57670_34' => true,
       '_assetic_8c57670_35' => true,
       '_assetic_8c57670_36' => true,
       '_assetic_8c57670_37' => true,
       '_assetic_8c57670_38' => true,
       '_assetic_8c57670_39' => true,
       '_assetic_8c57670_40' => true,
       '_assetic_399a29c' => true,
       '_assetic_399a29c_0' => true,
       '_assetic_399a29c_1' => true,
       '_assetic_399a29c_2' => true,
       '_assetic_399a29c_3' => true,
       '_assetic_399a29c_4' => true,
       '_assetic_399a29c_5' => true,
       '_assetic_399a29c_6' => true,
       '_assetic_399a29c_7' => true,
       '_assetic_399a29c_8' => true,
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
       'flystock_listpage' => true,
       'flystock_show' => true,
       'flystock_create' => true,
       'flystock_edit' => true,
       'flystock_delete' => true,
       'flycross_list' => true,
       'flycross_listpage' => true,
       'flycross_select' => true,
       'flycross_show' => true,
       'flycross_create' => true,
       'flycross_edit' => true,
       'flycross_delete' => true,
       'flyvial_list' => true,
       'flyvial_listpage' => true,
       'flyvial_select' => true,
       'flyvial_show' => true,
       'flyvial_create' => true,
       'flyvial_edit' => true,
       'flyvial_delete' => true,
       'flyvial_expand' => true,
       'flyvial_expand_id' => true,
       'ajax_vial_format' => true,
       'ajax_vial_filter_format' => true,
       'ajax_vial' => true,
       'ajax_vial_filter' => true,
       'searchResult' => true,
       'searchResultPage' => true,
       'login' => true,
       'login_check' => true,
       'logout' => true,
    );

    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function generate($name, $parameters = array(), $absolute = false)
    {
        if (!isset(self::$declaredRouteNames[$name])) {
            throw new RouteNotFoundException(sprintf('Route "%s" does not exist.', $name));
        }

        $escapedName = str_replace('.', '__', $name);

        list($variables, $defaults, $requirements, $tokens) = $this->{'get'.$escapedName.'RouteInfo'}();

        return $this->doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute);
    }

    private function get_assetic_8c57670RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => NULL,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670.js',  ),));
    }

    private function get_assetic_8c57670_0RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 0,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_jquery-1.7_1.js',  ),));
    }

    private function get_assetic_8c57670_1RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 1,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_jquery.effects.core_2.js',  ),));
    }

    private function get_assetic_8c57670_2RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 2,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_jquery.ui.core_3.js',  ),));
    }

    private function get_assetic_8c57670_3RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 3,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_jquery.ui.widget_4.js',  ),));
    }

    private function get_assetic_8c57670_4RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 4,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_jquery.ui.position_5.js',  ),));
    }

    private function get_assetic_8c57670_5RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 5,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_jquery.ui.mouse_6.js',  ),));
    }

    private function get_assetic_8c57670_6RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 6,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_jquery.ui.menu_7.js',  ),));
    }

    private function get_assetic_8c57670_7RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 7,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_8_jquery.effects.blind_1.js',  ),));
    }

    private function get_assetic_8c57670_8RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 8,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_8_jquery.effects.bounce_2.js',  ),));
    }

    private function get_assetic_8c57670_9RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 9,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_8_jquery.effects.clip_3.js',  ),));
    }

    private function get_assetic_8c57670_10RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 10,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_8_jquery.effects.drop_4.js',  ),));
    }

    private function get_assetic_8c57670_11RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 11,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_8_jquery.effects.explode_5.js',  ),));
    }

    private function get_assetic_8c57670_12RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 12,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_8_jquery.effects.fade_6.js',  ),));
    }

    private function get_assetic_8c57670_13RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 13,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_8_jquery.effects.fold_7.js',  ),));
    }

    private function get_assetic_8c57670_14RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 14,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_8_jquery.effects.highlight_8.js',  ),));
    }

    private function get_assetic_8c57670_15RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 15,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_8_jquery.effects.pulsate_9.js',  ),));
    }

    private function get_assetic_8c57670_16RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 16,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_8_jquery.effects.scale_10.js',  ),));
    }

    private function get_assetic_8c57670_17RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 17,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_8_jquery.effects.shake_11.js',  ),));
    }

    private function get_assetic_8c57670_18RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 18,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_8_jquery.effects.slide_12.js',  ),));
    }

    private function get_assetic_8c57670_19RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 19,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_8_jquery.effects.transfer_13.js',  ),));
    }

    private function get_assetic_8c57670_20RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 20,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.accordion_1.js',  ),));
    }

    private function get_assetic_8c57670_21RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 21,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.autocomplete_2.js',  ),));
    }

    private function get_assetic_8c57670_22RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 22,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.button_3.js',  ),));
    }

    private function get_assetic_8c57670_23RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 23,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.checkbox_4.js',  ),));
    }

    private function get_assetic_8c57670_24RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 24,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.datepicker_5.js',  ),));
    }

    private function get_assetic_8c57670_25RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 25,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.dialog_6.js',  ),));
    }

    private function get_assetic_8c57670_26RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 26,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.draggable_7.js',  ),));
    }

    private function get_assetic_8c57670_27RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 27,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.droppable_8.js',  ),));
    }

    private function get_assetic_8c57670_28RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 28,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.menubar_9.js',  ),));
    }

    private function get_assetic_8c57670_29RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 29,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.popup_10.js',  ),));
    }

    private function get_assetic_8c57670_30RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 30,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.position_11.js',  ),));
    }

    private function get_assetic_8c57670_31RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 31,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.progressbar_12.js',  ),));
    }

    private function get_assetic_8c57670_32RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 32,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.resizable_13.js',  ),));
    }

    private function get_assetic_8c57670_33RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 33,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.selectable_14.js',  ),));
    }

    private function get_assetic_8c57670_34RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 34,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.selectmenu_15.js',  ),));
    }

    private function get_assetic_8c57670_35RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 35,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.slider_16.js',  ),));
    }

    private function get_assetic_8c57670_36RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 36,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.sortable_17.js',  ),));
    }

    private function get_assetic_8c57670_37RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 37,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.spinner_18.js',  ),));
    }

    private function get_assetic_8c57670_38RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 38,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.tabs_19.js',  ),));
    }

    private function get_assetic_8c57670_39RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 39,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_9_jquery.ui.tooltip_20.js',  ),));
    }

    private function get_assetic_8c57670_40RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '8c57670',  'pos' => 40,  '_format' => 'js',), array (), array (  0 =>   array (    0 => 'text',    1 => '/js/8c57670_part_10_forms_1.js',  ),));
    }

    private function get_assetic_399a29cRouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => NULL,  '_format' => 'css',), array (), array (  0 =>   array (    0 => 'text',    1 => '/css/399a29c.css',  ),));
    }

    private function get_assetic_399a29c_0RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 0,  '_format' => 'css',), array (), array (  0 =>   array (    0 => 'text',    1 => '/css/399a29c_jquery.ui.all_1.css',  ),));
    }

    private function get_assetic_399a29c_1RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 1,  '_format' => 'css',), array (), array (  0 =>   array (    0 => 'text',    1 => '/css/399a29c_jquery.ui.selectmenu_2.css',  ),));
    }

    private function get_assetic_399a29c_2RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 2,  '_format' => 'css',), array (), array (  0 =>   array (    0 => 'text',    1 => '/css/399a29c_jquery.ui.checkbox_3.css',  ),));
    }

    private function get_assetic_399a29c_3RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 3,  '_format' => 'css',), array (), array (  0 =>   array (    0 => 'text',    1 => '/css/399a29c_part_4_general_1.css',  ),));
    }

    private function get_assetic_399a29c_4RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 4,  '_format' => 'css',), array (), array (  0 =>   array (    0 => 'text',    1 => '/css/399a29c_part_4_nav_top_2.css',  ),));
    }

    private function get_assetic_399a29c_5RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 5,  '_format' => 'css',), array (), array (  0 =>   array (    0 => 'text',    1 => '/css/399a29c_part_4_pager_3.css',  ),));
    }

    private function get_assetic_399a29c_6RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 6,  '_format' => 'css',), array (), array (  0 =>   array (    0 => 'text',    1 => '/css/399a29c_part_4_search_4.css',  ),));
    }

    private function get_assetic_399a29c_7RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 7,  '_format' => 'css',), array (), array (  0 =>   array (    0 => 'text',    1 => '/css/399a29c_part_4_table_data_5.css',  ),));
    }

    private function get_assetic_399a29c_8RouteInfo()
    {
        return array(array (), array (  '_controller' => 'assetic.controller:render',  'name' => '399a29c',  'pos' => 8,  '_format' => 'css',), array (), array (  0 =>   array (    0 => 'text',    1 => '/css/399a29c_part_4_table_ui_6.css',  ),));
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
        return array(array (), array (  '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::checkAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/_configurator/',  ),));
    }

    private function get_configurator_stepRouteInfo()
    {
        return array(array (  0 => 'index',), array (  '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::stepAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'index',  ),  1 =>   array (    0 => 'text',    1 => '/_configurator/step',  ),));
    }

    private function get_configurator_finalRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::finalAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/_configurator/final',  ),));
    }

    private function getdefaultRouteInfo()
    {
        return array(array (), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::listAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/',  ),));
    }

    private function getflystock_listRouteInfo()
    {
        return array(array (), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyStockController::listAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/stocks/',  ),));
    }

    private function getflystock_listpageRouteInfo()
    {
        return array(array (  0 => 'page',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyStockController::listAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'page',  ),  1 =>   array (    0 => 'text',    1 => '/stocks/page',  ),));
    }

    private function getflystock_showRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyStockController::showAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/stocks/show',  ),));
    }

    private function getflystock_createRouteInfo()
    {
        return array(array (), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyStockController::createAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/stocks/new',  ),));
    }

    private function getflystock_editRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyStockController::editAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/stocks/edit',  ),));
    }

    private function getflystock_deleteRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyStockController::deleteAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/stocks/delete',  ),));
    }

    private function getflycross_listRouteInfo()
    {
        return array(array (), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyCrossController::listAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/crosses/',  ),));
    }

    private function getflycross_listpageRouteInfo()
    {
        return array(array (  0 => 'page',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyCrossController::listAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'page',  ),  1 =>   array (    0 => 'text',    1 => '/crosses/page',  ),));
    }

    private function getflycross_selectRouteInfo()
    {
        return array(array (), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyCrossController::selectAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/crosses/select',  ),));
    }

    private function getflycross_showRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyCrossController::showAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/crosses/show',  ),));
    }

    private function getflycross_createRouteInfo()
    {
        return array(array (), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyCrossController::createAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/crosses/new',  ),));
    }

    private function getflycross_editRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyCrossController::editAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/crosses/edit',  ),));
    }

    private function getflycross_deleteRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyCrossController::deleteAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/crosses/delete',  ),));
    }

    private function getflyvial_listRouteInfo()
    {
        return array(array (), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::listAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/vials',  ),));
    }

    private function getflyvial_listpageRouteInfo()
    {
        return array(array (  0 => 'page',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::listAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'page',  ),  1 =>   array (    0 => 'text',    1 => '/vials/page',  ),));
    }

    private function getflyvial_selectRouteInfo()
    {
        return array(array (), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::selectAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/vials/select',  ),));
    }

    private function getflyvial_showRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::showAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/vials/show',  ),));
    }

    private function getflyvial_createRouteInfo()
    {
        return array(array (), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::createAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/vials/new',  ),));
    }

    private function getflyvial_editRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::editAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/vials/edit',  ),));
    }

    private function getflyvial_deleteRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::deleteAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/vials/delete',  ),));
    }

    private function getflyvial_expandRouteInfo()
    {
        return array(array (), array (  'id' => 0,  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::expandAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/vials/expand/',  ),));
    }

    private function getflyvial_expand_idRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\FlyVialController::expandAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/vials/expand',  ),));
    }

    private function getajax_vial_formatRouteInfo()
    {
        return array(array (  0 => 'id',  1 => 'format',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\AJAXController::vialAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '.',    2 => '[^\\.]+?',    3 => 'format',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/\\.]+?',    3 => 'id',  ),  2 =>   array (    0 => 'text',    1 => '/ajax/vials',  ),));
    }

    private function getajax_vial_filter_formatRouteInfo()
    {
        return array(array (  0 => 'filter',  1 => 'id',  2 => 'format',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\AJAXController::vialAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '.',    2 => '[^\\.]+?',    3 => 'format',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/\\.]+?',    3 => 'id',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'filter',  ),  3 =>   array (    0 => 'text',    1 => '/ajax/vials',  ),));
    }

    private function getajax_vialRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\AJAXController::vialAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  2 =>   array (    0 => 'text',    1 => '/ajax/vials',  ),));
    }

    private function getajax_vial_filterRouteInfo()
    {
        return array(array (  0 => 'filter',  1 => 'id',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\AJAXController::vialAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'filter',  ),  3 =>   array (    0 => 'text',    1 => '/ajax/vials',  ),));
    }

    private function getsearchResultRouteInfo()
    {
        return array(array (), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\SearchController::searchResultAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/search/result/',  ),));
    }

    private function getsearchResultPageRouteInfo()
    {
        return array(array (  0 => 'page',), array (  '_controller' => 'VIB\\FliesBundle\\Controller\\SearchController::searchResultAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'page',  ),  1 =>   array (    0 => 'text',    1 => '/search/result/page',  ),));
    }

    private function getloginRouteInfo()
    {
        return array(array (), array (  '_controller' => 'VIB\\SecurityBundle\\Controller\\SecurityController::loginAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/login',  ),));
    }

    private function getlogin_checkRouteInfo()
    {
        return array(array (), array (), array (), array (  0 =>   array (    0 => 'text',    1 => '/login_check',  ),));
    }

    private function getlogoutRouteInfo()
    {
        return array(array (), array (), array (), array (  0 =>   array (    0 => 'text',    1 => '/logout',  ),));
    }
}
