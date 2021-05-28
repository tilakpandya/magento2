<?php
class Ccc_Order_Block_Adminhtml_Cart_Shipping_Method_Form
    extends Mage_Core_Block_Template
{
    protected $_rates;

    public function __construct()
    {
        parent::__construct();
        $this->setId('Ccc_Order_Block_Adminhtml_Cart_shipping_method_form');
    }

    public function getShippingMethodTitle()
    {
        return  Mage::getModel('shipping/config')->getActiveCarriers();
    }
}
