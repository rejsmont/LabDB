<?php
namespace VIB\FliesBundle\Menu;

use Symfony\Component\HttpFoundation\Request;
use Mopa\Bundle\BootstrapBundle\Navbar\AbstractNavbarMenuBuilder;

/**
 * An example howto inject a default KnpMenu to the Navbar
 * see also Resources/config/example_menu.yml
 * and example_navbar.yml
 * @author phiamo
 *
 */
class MenuBuilder extends AbstractNavbarMenuBuilder
{

    public function createMainMenu(Request $request)
    {
        $menu = $this->createNavbarMenuItem();
        $menu->addChild('Stocks', array('route' => 'vib_flies_stock_list'));
        $menu->addChild('Stocks vials', array('route' => 'vib_flies_stockvial_list'));
        $menu->addChild('Crosses', array('route' => 'vib_flies_crossvial_list'));
        //$menu->addChild('Vials', array('route' => 'vib_flies_vial_list'));
        $vialsMenu = $this->createDropdownMenuItem($menu, "Vials", false, array('icon' => 'caret'));
        $vialsMenu->addChild('Stock vials', array('route' => 'default'));
        $vialsMenu->addChild('Cross vials', array('route' => 'default'));
        return $menu;
    }
}
