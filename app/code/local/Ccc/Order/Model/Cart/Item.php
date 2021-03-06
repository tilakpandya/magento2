<?php
class Ccc_Order_Model_Cart_Item extends Mage_Core_Model_Abstract
{
    public function _construct() 
    {
        $this->_init('order/cart_item');
    }

    public function setCart(Ccc_Order_Model_Cart $cart) {
		$this->cart = $cart;
		return $this;
	}

	public function getCart() {
		if(!$this->cart) {
			return false;
		}
		return $this->cart;
	}
}