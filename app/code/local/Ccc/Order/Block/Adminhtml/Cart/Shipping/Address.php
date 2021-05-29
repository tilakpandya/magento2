<?php

class Ccc_Order_Block_Adminhtml_Cart_Shipping_Address
    extends Mage_Core_Block_Template
{
    protected $cart = null;

    public function getShippingAddress()
    { 
        $address = $this->getCart()->getShippingAddress();
        if ($address->getId()) {
            return $address;
        }
        $customerAddress = $this->getCart()->getCustomer()->getDefaultShippingAddress();
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


    public function getSameAsBilling()
    {
        if ($this->getCart()->getShippingAddress()->getId()) {
            return null;
        }
       $billing = $this->getCart()->getBillingAddress();
        if ($billing->same_as_billing) {
            return $billing->getSameAsBilling();
        }
    }

}
