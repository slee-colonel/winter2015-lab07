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

    function getOrder($filename){
        $loadedorder = simplexml_load_file('./data/' . $filename);
        
        $order['ordernum'] = substr($filename, 5, strlen($filename)-4);
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
            $burgers[$count] = $record;
            $count++;
        }
        
        return $burgers;
    }

    // retrieve a patty record, perhaps for pricing
    function getPatty($code) {
        if (isset($this->patties[$code]))
            return $this->patties[$code];
        else
            return null;
    }

}
