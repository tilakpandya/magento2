<?php

class Ccc_Order_Block_Adminhtml_Cart extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_cart';
        $this->_blockGroup = 'order';
        $this->_headerText = $this->__('Cart');
        //$this->setTemplate('order/adminhtml/cart.phtml');

    }    

}
