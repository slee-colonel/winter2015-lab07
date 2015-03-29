<?php

/**
 * 
 * 
 * @author Sanders Lee
 */
class Order extends CI_Model {

    protected $loadedorder = null;
    protected $burgers = array();

    // Constructor
    public function __construct() {
        parent::__construct();      
    }

    function getOrder($num){
        $loadedorder = simplexml_load_file('./data/order' . $num . ".xml");
        
        $order['ordernum'] = $num;
        $order['ordername'] = "Order" . $order['ordernum'];
               
        $order['customername'] = $loadedorder->customer;
        $order['type'] = $loadedorder['type'];
        $order['delivery'] = $loadedorder->delivery;
        $order['special'] = $loadedorder->special;
        
        $order['burgerlist'] = $this->getBurgers($loadedorder);
        return $order;
    }
    
    // retrieve list of burgers from order
    function getBurgers($loadedorder) {
        $count = 1;
        foreach ($loadedorder->burger as $burger) {
            $record['burgernum'] = $count;
            $record['burgerbase'] = $burger->patty['type'];
            if ($burger->cheeses['top'] != NULL)
                $record['burgercheeses'] = $burger->cheeses['top'] . " (top) ";
            if ($burger->cheeses['bottom'] != NULL)
                $record['burgercheeses'] = $burger->cheeses['bottom'] . " (bottom) ";
            $record['burgertoppings'] = $this->getToppings($burger);
            $record['burgersauces'] = $this->getSauces($burger);
            $record['burgerinstructions'] = $burger->instructions;
            $burgers[$count] = $record;
            $count++;
        }
        
        return $burgers;
    }

    function getToppings($burger) {
        $first = true;
        $toppings = "";
        foreach ($burger->topping as $topping) {
            if(!$first)
                $toppings .= ", ";
            $toppings .= $topping['type'];
            $first = false;
        }        
        
        return $toppings;
    }
    
    function getSauces($burger) {
        $first = true;
        $sauces = "";
        foreach ($burger->sauce as $sauce) {
            if(!$first)
                $sauces .= ", ";
            $sauces .= $sauce['type'];
            $first = false;
        }        
        
        return $sauces;
    }
}
