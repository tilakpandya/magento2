<?php

class Ccc_Order_Block_Adminhtml_Cart_Item extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_cart';
        $this->_blockGroup = 'order';
        $this->_headerText = $this->__('Cart');
        $this->setTemplate('order/adminhtml/item.phtml');

    }

    public function getCartItems()
    {
        $customerId = Mage::getSingleton('order/session')->getCustomerId();

        $cart =  Mage::getModel('order/cart')->getCollection();
                $collection = $cart->addFieldToSelect(['cart_id','customer_id'])
                    ->addFieldToFilter('customer_id',['eq'=>$customerId]);

                $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
                    ->columns(['cart_id','customer_id']);
                $select = $collection->getSelect();
                $cart = $collection->getResource()->getReadConnection()->fetchRow($select);

        $cartItemCollection = Mage::getModel('order/cart_item')->getCollection();
        $collection = $cartItemCollection->addFieldToSelect(['item_id','cart_id','customer_id','product_id','quantity','base_price','price','discount'])
                    ->addFieldToFilter('cart_id',['eq'=>$cart['cart_id']]);
                $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
                    ->columns(['item_id','cart_id','product_id','quantity','base_price','price','discount']);
                    
                $select = $collection->getSelect(); 
                $cartItem = $collection->getResource()->getReadConnection()->fetchAll($select); 
                
        
       return $cartItem;
    }
}
