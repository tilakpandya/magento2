<?php
class Ccc_Order_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Sales');
    }

    public function indexAction()
    {
        
        $this->_title($this->__('Orders'));
        $this->loadLayout()
            ->_setActiveMenu('order')
            ->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));
        //$this->_addContent($this->getLayout()->createBlock('order/adminhtml_order'));
        $this->renderLayout();
    }

    public function startAction()
    {
        $this->_title($this->__('Orders'));
        $this->loadLayout()
            ->_setActiveMenu('order')
            ->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));
        $this->renderLayout();
    }

    public function selectCustomerAction()
    {
        try {
            $customerId = $this->getRequest()->getPost('customer_id');
           
            /* if ($customerId == 'Select') {
                Mage::getSingleton('order/session')->setCustomerId(null);
            } */

            if ($customerId <= 0) {
               throw new Exception("Invalid Customer...");
            }

            if ($customerId != 'Select') {
                $this->getCart($customerId);
            }
                    
            $this->_redirect('*/adminhtml_order/start');
        } catch (Exception $e) {
            Mage::helper('order')->__($e);
        }
    
    }

    protected function getCart($customerId=NULL)
    {
        $session = Mage::getSingleton('order/session'); 
        
        if ($customerId) {
            
            $session->setCustomerId($customerId);
        } 
       
        $customerId = $session->getCustomerId();
        $cartload = Mage::getModel('order/cart')->load($customerId);
        /* echo "<pre>";
        print_r($cartload->getId());
        die; */
         if ($cartload->getId()) {
            
            return $cartload;
        }  

        $cart = Mage::getModel('order/cart');
        $cart->customer_id = $customerId;
        $cart->created_at = date('Y-m-d H:i:s');
        $cart->save();
        return $cart;  
    }

    public function unsetCustomerAction()
    {
        Mage::getSingleton('order/session')->unsCustomerId();
        $this->_redirect('*/adminhtml_order/start');
    }

    public function addItemToCartAction()
    {
        try {
            echo "<pre>";
            $productItems = $this->getRequest()->getPost('id');
            $customerId = Mage::getSingleton('order/session')->getCustomerId();
            //print_r($productItems);
            if (!$productItems) {

                throw new Exception("Invalid Product...");
            }

            $cart =  Mage::getModel('order/cart')->getCollection();
                $collection = $cart->addFieldToSelect(['cart_id','customer_id'])
                    ->addFieldToFilter('customer_id',['eq'=>$customerId]);

                $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
                    ->columns(['cart_id','customer_id']);
                $select = $collection->getSelect();
                $cart = $collection->getResource()->getReadConnection()->fetchRow($select); 
                //print_r($cart);

            foreach ($productItems as $id => $value) {
                $productCollection = Mage::getModel('catalog/product')->getCollection();
                $productCollection->addAttributeToSelect(['entity_id','name','price'],'inner');
                $productCollection->addAttributeToFilter('entity_id',['eq'=>$id]);
                $productCollection->getSelect()->reset(Zend_Db_Select::COLUMNS)
                    ->columns(['entity_id','price'=>'at_price.value','name'=>'at_name.value']);
                $select = $productCollection->getSelect();
                $product = $productCollection->getResource()->getReadConnection()->fetchRow($select); 
                //print_r($product);

                $cartItem = Mage::getModel('order/cart_item');
                $cartItem->cart_id = $cart['cart_id'];
                $cartItem->product_id = $product['entity_id'];
                $cartItem->quantity = 1;
                $cartItem->base_price = $product['price'];
                $cartItem->price = $product['price'];
                $cartItem->discount = 0;
                $cartItem->created_at = date('Y-m-d H:i:s');
                $cartItem->save();
                print_r($cartItem);
                
            }
            
        } catch (Exception $e) {
            Mage::helper('order')->__($e);
        }
        $this->_redirect('*/adminhtml_order/start');

    }

    public function deleteItemToCartAction()
    {
       echo 111;
       die;
    }

    public function updateItemToCartAction()
    {
        echo "<pre>";
            $qtyItems = $this->getRequest()->getPost('quantity');
            $discountItems = $this->getRequest()->getPost('discount');

            $customerId = Mage::getSingleton('order/session')->getCustomerId();
             
            foreach ($qtyItems as $cartItemId => $quantity) {
                $cartItem = Mage::getModel('order/cart_item')->load($cartItemId);
                
                $cartItem->quantity = $quantity;
                    
                $cartItem->save();
             }

            foreach ($discountItems as $cartItemId => $discount) {
                $cartItem = Mage::getModel('order/cart_item')->load($cartItemId);
               
                if($cartItem['price']>$discount){
                    $cartItem->discount = $discount;
                    $cartItem->save();
                }
            
             } 
        
             $this->_redirect('*/adminhtml_order/start');
     
    }
}