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
        $cart = $this->getCart();
        $this->_title($this->__('Orders'));
        $this->loadLayout()
            ->_setActiveMenu('order')
            ->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));    
        $block = $this->getLayout()->getBlock('cart');
		$block->setCart($cart); 
        $this->renderLayout();
    }

    public function selectCustomerAction()
    {
        try {
            $customerId = $this->getRequest()->getPost('customer_id');

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
        $cartload = Mage::getModel('order/cart')->load($customerId,'customer_id');
        
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
            //print_r($productItems);
            if (!$productItems) {

                throw new Exception("Invalid Product...");
            }

            $cart =  $this->getCart();

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
            $this->_redirect('*/adminhtml_order/start');
        } catch (Exception $e) {
            Mage::helper('order')->__($e);
        }

    }

    public function deleteItemToCartAction($id)
    {
        try {
            $cartItem = Mage::getModel('order/cart_item')->load($id);
            if (!$cartItem->delete()) {
                throw new Exception("Cannot Delete..");
                
            }
            $this->_redirect('*/adminhtml_order/start');
        } catch (\Throwable $th) {
            Mage::getModel('order/session')->addException($th, $this->__($th));

        }
       
    }

    public function updateItemToCartAction()
    {

            $qtyItems = $this->getRequest()->getPost('quantity');
            $delete = $this->getRequest()->getPost('delete');
            if ($delete) {
                $this->deleteItemToCartAction($delete);
            }
            
            $discountItems = $this->getRequest()->getPost('discount');
             
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
    public function saveShippingAddressAction()
    {
        echo "<pre>";
        $cart = $this->getCart();
        
        $shippingingAddress = $this->getRequest()->getPost('shipping'); 
        $saveInAddressBook = $this->getRequest()->getPost('shippingSaveAddressBook');
        $sameAsBilling= $this->getRequest()->getPost('sameasbilling');

        if ($sameAsBilling) {
            $cartBillingAddress = $cart->getBillingAddress();
            $cartBillingAddress->setSameAsBilling(1);
            $cartBillingAddress->save();

            $cart->getShippingAddress()->delete();

            if ($saveInAddressBook) {
                $customerShippingAddress = $cart->getCustomer()->getShippingAddress();
                $customerShippingAddress->setStreet($cartBillingAddress->address);
                $customerShippingAddress->setCountryId($cartBillingAddress->country);
                $customerShippingAddress->setRegion($cartBillingAddress->state);
                $customerShippingAddress->setPostcode($cartBillingAddress->zipcode);
                $customerShippingAddress->setTelephone($cartBillingAddress->phone);
                $customerShippingAddress->setCity($cartBillingAddress->city);
                $customerShippingAddress->save();
            }
            $this->_redirect('*/adminhtml_order/start');
        }else{
            $cartShippingAddress = $cart->getShippingAddress();
        
            $cartShippingAddress->addData($shippingingAddress);    
            $cartShippingAddress->setCartId($cart->getId());  
           
            $cartShippingAddress->setAddressId($cart->getCustomer()->getDefaultShippingAddress()->getId());  
            $cartShippingAddress->setAddressType('Shipping');   
            $cartShippingAddress->save();
            
            if ($saveInAddressBook) {
                $customerShippingAddress = $cart->getCustomer()->getShippingAddress();
                $customerShippingAddress->setStreet($shippingingAddress['address']);
                $customerShippingAddress->setCountryId($shippingingAddress['country']);
                $customerShippingAddress->setRegion($shippingingAddress['state']);
                $customerShippingAddress->setPostcode($shippingingAddress['zipcode']);
                $customerShippingAddress->setTelephone($shippingingAddress['phone']);
                $customerShippingAddress->setCity($shippingingAddress['city']);
                $customerShippingAddress->save();
            }
            $this->_redirect('*/adminhtml_order/start');
        } 

        

         
    }

    public function saveBillingAddressAction()
    {
        echo "<pre>";
        $cart = $this->getCart();
        $billingAddress = $this->getRequest()->getPost('billing'); 
        $saveInAddressBook = $this->getRequest()->getPost('billingSaveAddressBook');

        $cartBillingAddress = $cart->getBillingAddress();
        $cartBillingAddress->addData($billingAddress);    
        $cartBillingAddress->setCartId($cart->getId()); 
        $cartBillingAddress->setAddressId($cart->getCustomer()->getDefaultBillingAddress()->getId());   
        $cartBillingAddress->setAddressType('Billing');   
        $cartBillingAddress->save();
        
        if ($saveInAddressBook) {
            $customerBillingAddress = $cart->getCustomer()->getBillingAddress();
            $customerBillingAddress->setStreet($billingAddress['address']);
            $customerBillingAddress->setCountryId($billingAddress['country']);
            $customerBillingAddress->setRegion($billingAddress['state']);
            $customerBillingAddress->setPostcode($billingAddress['zipcode']);
            $customerBillingAddress->setTelephone($billingAddress['phone']);
            $customerBillingAddress->setCity($billingAddress['city']);
            $customerBillingAddress->save();
        }
        
        $this->_redirect('*/adminhtml_order/start');

    }

}