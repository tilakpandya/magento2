<?php
class Ccc_Order_Model_Cart extends Mage_Core_Model_Abstract
{
    public function _construct() 
    {
        $this->_init('order/cart');
    }
}