<?php

class Ccc_Order_Block_Adminhtml_Cart_Shipping_Address
    extends Mage_Core_Block_Template
{
    
    public function getShippingAddress()
    {
      
        $customerId = Mage::getSingleton('order/session')->getCustomerId();
        $cartId = Mage::getModel('order/cart')->load($customerId,'customer_id')->getId();
        $cartShippingAddress = Mage::getModel('order/cart_address')->getCollection();

        $cartAddressCollection = Mage::getModel('order/cart_address')->getCollection();
        $select = $cartAddressCollection->getSelect()->where("cart_id = $cartId AND address_type = 'Shipping'");
        $cartAddressData = $cartAddressCollection->getResource()->getReadConnection()->fetchRow($select);
        $cartAddressCollection = Mage::getModel('order/cart_address')->load($cartAddressData['cart_address_id']);
          

        if($cartAddressData)
        {
            return $cartAddressCollection;
        }
        
        $collection = Mage::getModel('customer/customer')->load($customerId)->getDefaultShippingAddress();
        return $collection;
    }


}
