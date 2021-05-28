<?php
class Ccc_Order_Model_Cart_Address extends Mage_Core_Model_Abstract
{
    CONST ADDRESS_TYPE_SHIPPING = 'shipping';
    CONST ADDRESS_TYPE_BILLING = 'billing';
    
    public function _construct() 
    {
        $this->_init('order/cart_address');
    }
}