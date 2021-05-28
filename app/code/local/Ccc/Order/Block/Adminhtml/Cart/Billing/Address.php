<?php

class Ccc_Order_Block_Adminhtml_Cart_Billing_Address
    extends Mage_Core_Block_Template
{
    public function getBillingAddress()
    {
        
        $customerId = Mage::getSingleton('order/session')->getCustomerId();
        $cartId = Mage::getModel('order/cart')->load($customerId,'customer_id')->getId();
        $cartBillingAddress = Mage::getModel('order/cart_address')->getCollection();

        $cartAddressCollection = Mage::getModel('order/cart_address')->getCollection();
        $select = $cartAddressCollection->getSelect()->where("cart_id = $cartId AND address_type = 'Billing'");
        $cartAddressData = $cartAddressCollection->getResource()->getReadConnection()->fetchRow($select);
        $cartAddressCollection = Mage::getModel('order/cart_address')->load($cartAddressData['cart_address_id']);
        
        if($cartAddressData)
        {
            return $cartAddressCollection;
        }
        
        $collection = Mage::getModel('customer/customer')->load($customerId)->getDefaultBillingAddress();
        return $collection;
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
