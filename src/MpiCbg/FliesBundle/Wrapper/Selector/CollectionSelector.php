<?php

namespace MpiCbg\FliesBundle\Wrapper\Selector;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of CollectionSelector
 *
 * @author ejsmont
 */
class CollectionSelector {

    /**
     * @var mixed
     */  
    private $data;    

    /**
     * @var Doctrine\Common\Collections\Collection
     */  
    private $items;
    
    /**
     * $var string
     */
    private $action;

    /**
     * @param Doctrine\Common\Collections\Collection $data
     * @return void 
     */
    function __construct($data) {
        $this->data = $data;
        $this->items = new ArrayCollection();
        
        foreach ($this->data as $key => $value) {
            $item = new CollectionSelectorItem($value);
            $this->items->set($key, $item);
            if (method_exists($value, 'getHasLabel')) {
                if (! $value->isLabelPrinted()) {
                    $item->setSelected(true);
                }
            }
        }
    }

    /**
     * @return mixed $data
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @return Doctrine\Common\Collections\Collection $items 
     */
    public function getItems() {
        return $this->items;
    }

    public function getAction() {
        return $this->action;
    }

    public function setAction($action) {
        $this->action = $action;
    }
}

?>
