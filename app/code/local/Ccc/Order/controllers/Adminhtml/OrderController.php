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

    public function viewAction()
    {
        
        $this->_title($this->__('Orders'));
        $this->loadLayout()
            ->_setActiveMenu('order')
            ->_addBreadcrumb($this->__('Orders'), $this->__('View'));
        $this->_addContent($this->getLayout()->createBlock('order/adminhtml_order_view'));
        $this->renderLayout();
    }

    public function startAction()
    {
        $cart = $this->getCart();
        $this->_title($this->__('Orders'));
        $this->loadLayout()
            ->_setActiveMenu('order');

        $block = $this->getLayout()->getBlock('cart');
        /* echo "<pre>";
        print_r(get_class( $block)); 
        die; */ 
        if ($cart) {
            $block->setCart($cart); 

        } 
        $this->renderLayout(); 
    }

    public function selectCustomerAction()
    {
        try {
            echo "<pre>";
            
           $customerId = $this->getRequest()->getPost('customer_id');
            
            if ($customerId <= 0) {
               throw new Exception("Invalid Customer...");
            }               
            if ($customerId != 'Select') {
                $this->getCart($customerId);
            }

            if(array_key_exists('reset',$this->getRequest()->getPost())){
                Mage::getSingleton('order/session')->unsCustomerId();
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
        echo 111;die;
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
            $total = [];
            foreach ($productItems as $id => $value) {
                $productCollection = Mage::getModel('catalog/product')->getCollection();
                $productCollection->addAttributeToSelect(['entity_id','name','price'],'inner');
                $productCollection->addAttributeToFilter('entity_id',['eq'=>$id]);
                $productCollection->getSelect()->reset(Zend_Db_Select::COLUMNS)
                    ->columns(['entity_id','price'=>'at_price.value','name'=>'at_name.value']);
                $select = $productCollection->getSelect();
                $product = $productCollection->getResource()->getReadConnection()->fetchRow($select); 
                //
                
                $cartItem = Mage::getModel('order/cart_item');
                $cartItem->cart_id = $cart['cart_id'];
                $cartItem->product_id = $product['entity_id'];
                $cartItem->product_name = $product['name'];
                $cartItem->quantity = 1;
                $cartItem->base_price = $product['price'];
                $cartItem->price = $product['price'];
                //$total[] = $product['price'];
                $cartItem->discount = 0;
                $cartItem->created_at = date('Y-m-d H:i:s');
                print_r($cartItem);
                $cartItem->save();
                
            }
            //$this->updateTotalCart($total);
            
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

    public function savePaymentMethodAction()
    {
        echo "<pre>";
        $billingMethod = $this->getRequest()->getPost('paymentMethod');
        $billingMethod = explode('=>',$billingMethod);
        /* print_r($billingMethod);
        die;  */
        $cart = $this->getCart();
        $cart->	payment_method_code = $billingMethod[0];
        $cart->	payment_name = $billingMethod[1];
        $cart->save();
        $this->_redirect('*/adminhtml_order/start');

    }

    public function saveShippingMethodAction()
    {
        echo "<pre>";
        $shppingData = $this->getRequest()->getPost('shippingMethod');
        
        $shppingData = explode('=>',$shppingData);
        
        $cart = $this->getCart();
        $cart->setShippingMethodCode($shppingData[0]);
        $cart->setShippingAmount($shppingData[1]);
        $cart->setShippingName($shppingData[2]); 
        $cart->save();
        $this->_redirect('*/adminhtml_order/start');
    }

    public function placeOrderAction()
    {
        echo "<pre>";
        $cart= $this->getCart();
        $order = Mage::getModel('order/order');
        $orderItem = Mage::getModel('order/order_item');
        $orderAddress = Mage::getModel('order/order_address');
        
        if ($order) {
            $this->setOrder($order);
        } 
        if ($orderAddress) {
            $this->setOrderAddress();
        } 
        if ($orderItem) {
            $this->setOrderItem();
        }
 
        if ($order->load($cart->getCustomer()->getId(),'customer_id')) {

           $cart->delete();
        }
        
        $this->_redirect('*/adminhtml_order/index');
    }

    public function setOrder($order)
    {
        $cart = $this->getCart();
        $customer = $cart->getCustomer();
      
        $order->customer_id = $customer->getId();
        $order->customer_name = $customer->getFirstname().' '.$customer->getLastname();
        $order->customer_email = $customer->getEmail();
        $order->shipping_name = $cart->getShippingName();
        $order->shipping_code = $cart->getShippingMethodCode();
        $order->shipping_charge = $cart->getShippingAmount();
        $order->shipping_amount = $cart->getShippingAmount();
        $order->payment_code = $cart->getPaymentMethodCode();
        $order->payment_name = $cart->getPaymentName();
        $order->save();
    }

    public function setOrderItem()
    {
        
        $cart = $this->getCart();
        $cartItem = $cart->getItems();
        $orderId = Mage::getModel('order/order')->load($cart->getCustomerId(),'customer_id')->getOrderId();
        foreach ($cartItem as $key => $item) {
            $cartItem = Mage::getModel('order/cart_item')->load($item['item_id']);
            $order = Mage::getModel('order/order_item');
            $order->order_id = $orderId;
            $order->product_name = $item['product_name'];
            $order->product_id = $item['product_id'];
            $order->quantity = $item['quantity'];
            $order->base_price = $item['base_price'];
            $order->price = $item['price'];
            $order->discount = $item['discount']; 
            $order->created_at = date('Y-m-d H:i:s');
            $order->save();
            $cartItem->delete();
        }
    }

    public function setOrderAddress()
    {
        $cart = $this->getCart();
        $orderId = Mage::getModel('order/order')->load($cart->getCustomerId(),'customer_id')->getOrderId();
        
        $cartBillingAddress = $cart->getBillingAddress();
        $cartShippingAddress = $cart->getShippingAddress();
        
        if ($cartBillingAddress->getId()) {
            $orderAddress = Mage::getModel('order/order_address');
            $orderAddress->order_id = $orderId;
            $orderAddress->address_id = $cartBillingAddress->getAddressId();
            $orderAddress->address = $cartBillingAddress->getAddress();
            $orderAddress->city = $cartBillingAddress->getCity();
            $orderAddress->address_type = 'Billing';
            $orderAddress->state= $cartBillingAddress->getState();
            $orderAddress->Country = $cartBillingAddress->getCountry();
            $orderAddress->zipcode = $cartBillingAddress->getZipcode();
            $orderAddress->phone = $cartBillingAddress->getPhone();
            $orderAddress->save();

            $cartBillingAddress->delete();

        }

        if ($cartShippingAddress->getId()) {
            $orderAddress = Mage::getModel('order/order_address');
            $orderAddress->order_id = $orderId;
            $orderAddress->address_id = $cartShippingAddress->getAddressId();
            $orderAddress->address = $cartShippingAddress->getAddress();
            $orderAddress->city = $cartShippingAddress->getCity();
            $orderAddress->address_type = 'Shipping';
            $orderAddress->state= $cartShippingAddress->getState();
            $orderAddress->Country = $cartShippingAddress->getCountry();
            $orderAddress->zipcode = $cartShippingAddress->getZipcode();
            $orderAddress->phone = $cartShippingAddress->getPhone();
            $orderAddress->save();

            $cartShippingAddress->delete();
        } 
       /*  print_r($cartBillingAddress);
        die; */
    }
}