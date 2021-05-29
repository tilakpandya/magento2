<?php

class Ccc_Order_Block_Adminhtml_Cart_Billing_Address
    extends Mage_Core_Block_Template
{
    protected $cart = null;
    public function getBillingAddress()
    { 
        $address = $this->getCart()->getBillingAddress();
        if ($address->getId()) {
            return $address;
        }
        $customerAddress = $this->getCart()->getCustomer()->getDefaultBillingAddress();
       if ($customerAddress == NULL) {
           return $address;
       }
        return $customerAddress;
    }

    public function getCountryName()
    {
        return Mage::getModel('directory/country_api')->items();
    }
    public function setCart(Ccc_Order_Model_Cart $cart) {
		$this->cart = $cart;
		return $this;
	}

	public function getCart() {
		if(!$this->cart) {
			throw new Exception("cart not found..");
            
		}
		return $this->cart;
	}
}
