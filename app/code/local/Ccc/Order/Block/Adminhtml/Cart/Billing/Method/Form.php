<?php

class Ccc_Order_Block_Adminhtml_Cart_Billing_Method_Form extends Mage_Core_Block_Template
{
    public function getPayemntMethodTitle()
    {
        $methods = Mage::getModel('payment/config');
        $activemethod = $methods->getActiveMethods();
        unset($activemethod['paypal_billing_agreement']);
        unset($activemethod['ccsave']);
        unset($activemethod['free']);
        return $activemethod;
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
