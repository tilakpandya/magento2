<?php

class Ccc_Order_Block_Adminhtml_Cart_Total extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_cart';
        $this->_blockGroup = 'order';
        $this->_headerText = $this->__('Cart');
        //$this->setTemplate('order/adminhtml/cart.phtml');
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

    public function getTotal()
    {
        return $this->getCart()->getItems();
    }

}
