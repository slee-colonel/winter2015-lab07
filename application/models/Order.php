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
        
        $order['ordertotal'] = 0;
        foreach($order['burgerlist'] as $burger)
            $order['ordertotal'] += $burger['burgertotal'];
        
        return $order;
    }
    
    // retrieve list of burgers from order
    function getBurgers($loadedorder) {
        $count = 1;
        foreach ($loadedorder->burger as $burger) {
            $record = array();
            $record['burgertotal'] = 0;
            $record['burgernum'] = $count;
            
            // map burger patty from code to full patty name &
            // add price of patty to burger price
            $record['burgerbase'] = $this->menu->getPatty($burger->patty['type']
                )->name;
            $record['burgertotal'] +=  $this->menu->getPatty($burger->patty
                ['type'])->price;
            
            // map cheeses from code to full cheese name &
            // add price of cheeses to burger price
            $record['burgercheeses'] = "";
            if (isset($burger->cheeses['top']))
            {
                $record['burgercheeses'] .= $this->menu->getCheese($burger->
                    cheeses['top'])->name . " (top) ";
                $record['burgertotal'] += $this->menu->getCheese($burger->
                    cheeses['top'])->price;
            }
            if (isset($burger->cheeses['bottom']))
            {
                $record['burgercheeses'] .= $this->menu->getCheese($burger->
                    cheeses['bottom'])->name . " (bottom) ";
                $record['burgertotal'] += $this->menu->getCheese($burger->
                    cheeses['bottom'])->price;
            }
            
            // get toppings & add price of toppings to burger price
            $record['burgertoppings'] = $this->getToppings($burger)['list'];
            $record['burgertotal'] += $this->getToppings($burger)['price'];
            
            $record['burgersauces'] = $this->getSauces($burger);            
            $record['burgerinstructions'] = $burger->instructions;
            
            $burgers[$count] = $record;
            $count++;
        }
        
        return $burgers;
    }

    // map toppings from code to full topping name,
    // toppings are priced
    function getToppings($burger) {
        $first = true;
        $toppings = array();
        $toppings['list'] = "";
        $toppings['price'] = 0;
        foreach ($burger->topping as $topping) {
            if(!$first)
                $toppings['list'] .= ", ";        
            $toppings['list'] .= $this->menu->getTopping(
                $topping['type'])->name;
            $toppings['price'] .= $this->menu->getTopping(
                $topping['type'])->price;
            $first = false;
        }
        
        return $toppings;
    }
    
    // map sauces from code to full sauce name,
    // sauces are free
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
