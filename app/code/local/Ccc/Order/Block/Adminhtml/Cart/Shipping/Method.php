<?php

class Ccc_Order_Block_Adminhtml_Cart_Shipping_Method extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('order_create_shipping_method');
    }

    public function getHeaderText()
    {
        return Mage::helper('order')->__('Shipping Method');
    }

    public function getHeaderCssClass()
    {
        return 'head-shipping-method';
    }
}
