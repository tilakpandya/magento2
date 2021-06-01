<?php
class Ccc_Order_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    protected $total = []; 
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
        $this->_initLayoutMessages('core/session');
        $this->_addContent($this->getLayout()->createBlock('order/adminhtml_order_view'));
        $this->renderLayout();
    }

    public function startAction()
    {
        $cart = $this->getCart();
        $this->_title($this->__('Orders'));
        $this->loadLayout()
            ->_setActiveMenu('order');
        $this->_initLayoutMessages('core/session');
        $block = $this->getLayout()->getBlock('cart');
        
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
                Mage::getSingleton('core/session')->addSuccess($this->__('Customer Remove successfully.'));

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
            Mage::getSingleton('core/session')->addSuccess($this->__('Customer Added successfully.'));

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
            $productItems = $this->getRequest()->getPost('id');
            $cart =  $this->getCart();
            $total = [];

            if (empty($productItems)) {
                Mage::getSingleton('core/session')->addError($this->__('Need to Add Product...'));
                $this->_redirect('*/*/start');
                return;
            }
            
            if (!$productItems) {

                throw new Exception("Invalid Product...");
            }

            foreach ($productItems as $id => $value) {
                $productCollection = Mage::getModel('catalog/product')->getCollection();
                $productCollection->addAttributeToSelect(['entity_id','name','price'],'inner');
                $productCollection->addAttributeToFilter('entity_id',['eq'=>$id]);
                $productCollection->getSelect()->reset(Zend_Db_Select::COLUMNS)
                    ->columns(['entity_id','price'=>'at_price.value','name'=>'at_name.value']);
                $select = $productCollection->getSelect();
                $product = $productCollection->getResource()->getReadConnection()->fetchRow($select); 
                
                $cartItem = Mage::getModel('order/cart_item');
                $cartItem->cart_id = $cart['cart_id'];
                $cartItem->product_id = $product['entity_id'];
                $cartItem->product_name = $product['name'];
                $cartItem->quantity = 1;
                $cartItem->base_price = $product['price'];
                $this->total[] = $product['price'];
                $cartItem->price = $product['price'];
                $cartItem->discount = 0;
                $cartItem->created_at = date('Y-m-d H:i:s');
                $cartItem->save();
                
            }
            if (!empty($this->total)) {
                $cart->total = array_sum($this->total);
                $cart->save();
            }
            Mage::getSingleton('core/session')->addSuccess('Item Placed in cart successfully');
            $this->_redirect('*/adminhtml_order/start');
        } catch (Exception $e) {
            Mage::helper('order')->__($e);
        }

    }

    public function deleteItemAction($id)
    {
        try {
            $cartItem = Mage::getModel('order/cart_item')->load($id);
            $basePrice = $cartItem->getBasePrice();
            $cart = $this->getCart();
            $cartTotal = $cart->getTotal();
            $cart->total = $cartTotal - $basePrice;
            
            if (!$cartItem->delete()) {
                throw new Exception("Cannot Delete..");
            }
            if (!$cart->save()) {
                throw new Exception("Cannot Modify Total..");
            }
            Mage::getSingleton('core/session')->addSuccess($this->__('Item Deleted successfully.'));
            $this->_redirect('*/adminhtml_order/start');
        } catch (\Throwable $th) {
            Mage::getModel('order/session')->addException($th, $this->__($th));

        }
       
    }

    public function updateItemToCartAction()
    {
            try {
                $Items = $this->getRequest()->getPost('quantity');
            $cartItem = $this->getCart()->getItems();
            $total = [];
            $cart = $this->getCart();
            $delete = $this->getRequest()->getPost('delete');
            
            if (!$Items) {
                throw new Exception("Item Not Selected");
                
            }           
            
            foreach ($Items as $cartItemId => $quantity) {
                $cartItem = Mage::getModel('order/cart_item')->load($cartItemId);
                $cartItem->quantity = $quantity;
                $price = $cartItem->price;    
                $cartItem->base_price = $price * $quantity;
                $total[] = $cartItem->base_price;
                $cartItem->save(); 
             }
            if (!empty($total)) {
                $cart->total = array_sum($total);
                $cart->save();
            }
            if ($delete) {
                $this->deleteItemAction($delete);
            }

             Mage::getSingleton('core/session')->addSuccess($this->__('Item Updated successfully.'));
             $this->_redirect('*/adminhtml_order/start');
            } catch (Exception $th) {
                Mage::getModel('order/session')->addError($th);
                $this->_redirect('*/*/start');

            }
            
    }

    public function cartTotal()
    {
        echo "<pre>";
        $cart =  $this->getCart();
        $cart->getItems();
        print_r($cart->getItems());
        die;
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
           
        } 
        Mage::getSingleton('core/session')->addSuccess($this->__('Shipping Address Save successfully'));
        $this->_redirect('*/adminhtml_order/start');
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
        Mage::getSingleton('core/session')->addSuccess($this->__('Billing Address Save successfully'));
        $this->_redirect('*/adminhtml_order/start');
    }

    public function savePaymentMethodAction()
    {
        echo "<pre>";
        $billingMethod = $this->getRequest()->getPost('paymentMethod');
        $billingMethod = explode('=>',$billingMethod);
        $cart = $this->getCart();
        $cart->	payment_method_code = $billingMethod[0];
        $cart->	payment_name = $billingMethod[1];
        $cart->save();
        Mage::getSingleton('core/session')->addSuccess($this->__('Payment Method Set successfully'));
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
        Mage::getSingleton('core/session')->addSuccess($this->__('Shipping Method Set successfully'));
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
            $this->setOrder();
        }  
        if ($orderItem) {
            $this->setOrderItem();
        }
       
        if ($orderAddress) {
            $this->setOrderAddress();
        }
        
        $cart->delete();
        Mage::getSingleton('core/session')->addSuccess($this->__('Ordered Placed Successfully'));
        $this->_redirect('*/adminhtml_order/index');
    }

    public function setOrder()
    {
        $order = Mage::getModel('order/order');
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
        $order->status = "Pending";
        $order->created_at = date('Y-m-d H:i:s');
        $order->cart_id = $cart->getId(); 
        $order->save();
    }

    public function setOrderItem()
    {
        $cart = $this->getCart();
        $cartItem = $cart->getItems();
        $orderId = Mage::getModel('order/order')->load($cart->getCartId(),'cart_id')->getOrderId();
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
            print_r($order);
            $this->deleteItemAction($item['item_id']);
        }
        
    }

    public function setOrderAddress()
    {
        $cart = $this->getCart();
        $orderId = Mage::getModel('order/order')->load($cart->getCartId(),'cart_id')->getOrderId();
        
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

            if ($cartBillingAddress->same_as_billing) {
                $orderAddress = Mage::getModel('order/order_address');
                $orderAddress->order_id = $orderId;
                $orderAddress->address_id = $cartBillingAddress->getAddressId();
                $orderAddress->address = $cartBillingAddress->getAddress();
                $orderAddress->city = $cartBillingAddress->getCity();
                $orderAddress->address_type = 'Shipping';
                $orderAddress->state= $cartBillingAddress->getState();
                $orderAddress->Country = $cartBillingAddress->getCountry();
                $orderAddress->zipcode = $cartBillingAddress->getZipcode();
                $orderAddress->phone = $cartBillingAddress->getPhone();
                $orderAddress->save();
            }

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

    public function massDeleteAction()
    {
        echo 111;
        die;
    }
}