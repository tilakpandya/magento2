<?php
class Ccc_Order_Model_Cart extends Mage_Core_Model_Abstract
{

    protected $customer = Null;
    protected $items = Null;
    protected $shippingAddress = null;
    protected $billingAddress = null;
    protected $paymentMethodId = null;
    protected $shippingMethodId = null;
    protected $shippingMethodAmount = null;
    protected $cart = null;


    public function _construct() 
    {
        $this->_init('order/cart');
    }

    public function setCustomer(Mage_Customer_Model_Customer $customer) {
		$this->customer = $customer;
		return $this;
	}

	public function getCustomer() {
		if($this->customer) {
			return $this->customer;
		}
        if (!$this->cartId) {
            return false;
        }
		$customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
		$this->setCustomer($customer);
		return $this->customer;
	}
    

    public function getBillingAddress()
    {
        if ($this->billingAddress) {
            return $this->billingAddress;
        }
        if (!$this->cartId) {
            return false;
        }
        
        $address = Mage::getModel('order/cart_address');
        
        $addressCollection = $address->getCollection()
            ->addFieldToFilter('cart_id',['eq'=>$this->cartId])
            ->addFieldToFilter('address_type',['eq'=>Ccc_Order_Model_Cart_Address::ADDRESS_TYPE_BILLING]);
            
        $address = $addressCollection->getFirstItem();
        
        return $address;    
    }

    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }


    public function getShippingAddress()
    {
        if ($this->shippingAddress) {
            return $this->shippingAddress;
        }
        if (!$this->cartId) {
            return false;
        }
        
        $address = Mage::getModel('order/cart_address');
        
        $addressCollection = $address->getCollection()
            ->addFieldToFilter('cart_id',['eq'=>$this->cartId])
            ->addFieldToFilter('address_type',['eq'=>Ccc_Order_Model_Cart_Address::ADDRESS_TYPE_SHIPPING]);
            
        $address = $addressCollection->getFirstItem();
        
        return $address;    
    }

    public function setShippingAddress($shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
        return $this;
    }

 
    public function getItems()
    {
        if ($this->item) {
            return $this->item;
        }

        $item = Mage::getModel('order/cart_item');
        $itemCollection = $item->getCollection()->addFieldToFilter('cart_id',['eq'=>$this->cartId]);
        $select = $itemCollection->getSelect();
        
        $itemCollection = $itemCollection->getResource()->getReadConnection()->fetchAll($select);
        
        return $itemCollection;
    }

    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }
}