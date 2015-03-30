<?php

/**
 * Our homepage. Show the most recently added quote.
 * 
 * controllers/Welcome.php
 *
 * ------------------------------------------------------------------------
 */
class Welcome extends Application {

    function __construct()
    {
	parent::__construct();
    }

    //-------------------------------------------------------------
    //  Homepage: show a list of the orders on file
    //-------------------------------------------------------------

    function index()
    {        
        $map = directory_map('./data/');
        $my_map = null;
        $i = 0;
        
        // Build a list of orders
        foreach($map as $filename)
        {
            if( (substr_compare($filename, '.xml', strlen($filename)-4, 4) === 0)
                && (substr_compare($filename, 'order', 0, 5) === 0))
            {
                // shave off ".xml"
                $ordername = substr($filename, 0, strlen($filename)-4);
                // shave off "order" and ".xml"
                $ordernum = substr($filename, 5, strlen($filename)-9);
                $my_map[$i] = array( 'num' => $ordernum,
                    'ordername' => $ordername . " (" . $this->order->getOrder(
                                   $ordernum)['customername'] . ")");
                $i++;
            }
        }
        	
        $this->data['linklist'] = $my_map;
        
	// Present the list to choose from
        $this->data['title'] = "Barker Bob's Burger Bar - Orders";
	$this->data['pagebody'] = 'homepage';
	$this->render();
    }
    
    //-------------------------------------------------------------
    //  Show the "receipt" for a specific order
    //-------------------------------------------------------------

    function order($num)
    {
        // Build a receipt for the chosen order
	$order = $this->order->getOrder($num);
        
        $this->data['ordercustomer'] = $order['ordername'] . " for " .
                $order['customername'] . " (" . $order['type'] . ")";
        $this->data['orderdelivery'] = $order['delivery'];
        $this->data['orderspecial'] = $order['special'];
        
        // the list of burgers
        $this->data['burgerlist'] = $order['burgerlist'];
        
        $this->data['ordertotal'] = $order['ordertotal'];
        
	$this->data['pagebody'] = 'justone';
	$this->render();
    }
    

}
