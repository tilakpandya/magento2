<?php

class Ccc_Order_Block_Adminhtml_Cart_Billing_Method extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('order_cart_billing_method');
    }

    public function getHeaderText()
    {
        return Mage::helper('Order')->__('Payment Method');
    }

    public function getHeaderCssClass()
    {
        return 'head-payment-method';
    }
}
