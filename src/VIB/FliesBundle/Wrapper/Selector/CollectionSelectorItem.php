<?php

namespace VIB\FliesBundle\Wrapper\Selector;

/**
 * Description of CollectionSelectorItem
 *
 * @author ejsmont
 */
class CollectionSelectorItem {
    
    /**
     * @var boolean
     */    
    private $selected;
    
    /**
     * @var mixed
     */      
    private $item;
    
    /**
     * @param mixed $item
     * @param boolean $selected
     * @return void 
     */
    function __construct($item, $selected = false) {
        $this->item = $item;
        $this->selected = $selected;
    }

    /**
     * @return boolean $selected
     */
    public function isSelected() {
        return $this->selected;
    }
    
    /**
     * @param boolean $selected
     */
    public function setSelected($selected) {
        $this->selected = $selected;
    }

    /**
     * @return mixed $item
     */
    public function getItem() {
        return $this->item;
    }

}

?>
