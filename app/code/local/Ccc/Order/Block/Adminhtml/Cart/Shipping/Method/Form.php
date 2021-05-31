<?php
class Ccc_Order_Block_Adminhtml_Cart_Shipping_Method_Form
    extends Mage_Core_Block_Template
{
    protected $_rates;
    protected $shippingMethod;
    public function __construct()
    {
        parent::__construct();
        $this->setId('Ccc_Order_Block_Adminhtml_Cart_shipping_method_form');
    }

    public function getShippingMethodTitle()
    {
        //return  Mage::getModel('shipping/config')->getActiveCarriers();
        
    }


    public function getShippingMethod()
    {
        if ($this->shippingMethod) {
            return $this->shippingMethod;
        }
        $zipcode = '2000';
        $country = 'AU';

        // Update the cart's quote.
        $cart = Mage::getSingleton('checkout/cart');
        $address = $cart->getQuote()->getShippingAddress();
        $address->setCountryId($country)
        ->setPostcode($zipcode)
        ->setCollectShippingrates(true);
        $cart->save();

        // Find if our shipping has been included.
        $rates = $address->collectShippingRates()
        ->getGroupedAllShippingRates();
        return $rates;
              
    }

    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;
        return $this;
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
