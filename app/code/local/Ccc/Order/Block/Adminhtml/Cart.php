<?php

class Ccc_Order_Block_Adminhtml_Cart extends Mage_Core_Block_Template
{
    protected $billingAddress = null;
    protected $shippingAddress = null;

    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_cart';
        $this->_blockGroup = 'order';
        $this->_headerText = $this->__('Cart');
        //$this->setTemplate('order/adminhtml/cart.phtml');
    }    

    public function getBillingAddres()
    {
        if ($this->billingAddress) {
            return $this->billingAddress;
        }
        if (!$this->cartId) {
            return false;
        }

        $address = Mage::getModel('order/cart_address');
        $addressCollection = $address->getCollection()
            ->addFieldFilter('cart_id',['eq'=>$this->cartId])
            ->addFieldFilter('address_type',['eq'=>Ccc_Order_Model_Cart_Address::ADDRESS_TYPE_BILLING]);

        $address = $addressCollection->getFirstItem();
        return $address;    
    }

    public function getShippingAddres()
    {
        if ($this->shippingAddress) {
            return $this->shippingAddress;
        }
        if (!$this->cartId) {
            return false;
        }

        $address = Mage::getModel('order/cart_address');
        $addressCollection = $address->getCollection()
            ->addFieldFilter('cart_id',['eq'=>$this->cartId])
            ->addFieldFilter('address_type',['eq'=>Ccc_Order_Model_Cart_Address::ADDRESS_TYPE_SHIPPING]);

        $address = $addressCollection->getFirstItem();
        return $address;    
    }
}
