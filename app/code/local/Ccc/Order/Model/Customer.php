<?php

class Ccc_Order_Model_Customer extends Mage_Customer_Model_Customer
{
    protected $billingAddress = null;
    protected $shippingAddress = null;

    public function setBillingAddress(Mage_Customer_Model_Address $billingAddress)
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }

    public function getBillingAddress()
    {
        if ($this->billingAddress) {
            return $this->billingAddress;
        }
        if (!$this->getId()) {
            return false;
        }
        $addressId  = $this->getResource()->getAttribute('default_billing')
            ->getFrontend()->getValue($this);
        
        $address = Mage::getModel('customer/address')->load($addressId);
        return $address;    
    }

    public function setShippingAddress(Mage_Customer_Model_Address $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
        return $this;
    }

    public function getShippingAddress()
    {
        if ($this->shippingAddress) {
            return $this->shippingAddress;
        }
        if (!$this->getId()) {
            return false;
        }
        $addressId  = $this->getResource()->getAttribute('default_shipping')
            ->getFrontend()->getValue($this);
        
        $address = Mage::getModel('customer/address')->load($addressId);
        return $address;    
    }

    
}