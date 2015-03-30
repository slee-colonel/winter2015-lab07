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
            $record = array();
            $record['burgernum'] = $count;
            $record['burgerbase'] = $this->menu->getPatty($burger->patty['type']
                )->name;
            $record['burgercheeses'] = " ";
            // map cheeses from code to full cheese name
            if (isset($burger->cheeses['top']))
                $record['burgercheeses'] .= $this->menu->getCheese($burger->
                    cheeses['top'])->name . " (top) ";
            if (isset($burger->cheeses['bottom']))
                $record['burgercheeses'] .= $this->menu->getCheese($burger->
                    cheeses['bottom'])->name . " (bottom) ";
            $record['burgertoppings'] = $this->getToppings($burger);
            $record['burgersauces'] = $this->getSauces($burger);
            $record['burgerinstructions'] = $burger->instructions;
            $burgers[$count] = $record;
            $count++;
        }
        
        return $burgers;
    }

    // map toppings from code to full topping name
    function getToppings($burger) {
        $first = true;
        $toppings = "";
        foreach ($burger->topping as $topping) {
            if(!$first)
                $toppings .= ", ";        
            $toppings .= $this->menu->getTopping($topping['type'])->name;
            $first = false;
        }        
        
        return $toppings;
    }
    
    // map sauces from code to full sauce name
    function getSauces($burger) {
        $first = true;
        $sauces = "";
        foreach ($burger->sauce as $sauce) {
            if(!$first)
                $sauces .= ", ";
            $sauces .= $this->menu->getSauce($sauce['type'])->name;
            $first = false;
        }        
        
        return $sauces;
    }
}
