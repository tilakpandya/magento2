<?php
class Ccc_Order_Block_Adminhtml_Cart_Shipping_Method_Form
    extends Mage_Core_Block_Template
{
    protected $_rates;
    protected $shippingMethod;
    public function __construct()
    {
        parent::__construct();
        $this->setId('Ccc_Order_Block_Adminhtml_Cart_shipping_method_form');
    }

    public function getShippingMethodTitle()
    {
        return  Mage::getModel('shipping/config')->getActiveCarriers();
    }


    public function getShippingMethod()
    {
        if ($this->shippingMethod) {
            return $this->shippingMethod;
        }
        $this->shippingMethod = Mage::getModel('shipping/config')->getActiveCarriers();
        return $this->shippingMethod;
    }

    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;
        return $this;
    }
}
