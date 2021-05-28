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
        $cartId = $this->getCart()->getId();
        $customerId = Mage::getModel('order/session')->getCustomerId();
        $shippingAddress = $this->getRequest()->getPost('shipping');
        $sameAsBilling = $this->getRequest()->getPost('sameasbilling');
        $customerAddressId = Mage::getModel('customer/customer')->load($customerId)->getDefaultShippingAddress()->getEntityId();
        
        $cartAddressCollection = Mage::getModel('order/cart_address')->getCollection();
        $select = $cartAddressCollection->getSelect()->where("cart_id = $cartId AND address_type = 'Shipping'");
        $cartAddressData = $cartAddressCollection->getResource()->getReadConnection()->fetchRow($select);
       
        $cartShippingAddress = Mage::getModel('order/cart_address');
        
        if ($cartAddressData) {
            $cartShippingAddress->load($cartAddressData['cart_address_id']);
            unset($cartAddressData['cart_address_id']);
            
        }
        if ($sameAsBilling) {
            $cartAddressCollection = Mage::getModel('order/cart_address')->getCollection();
            echo $select = $cartAddressCollection->getSelect()->where("cart_id = $cartId AND address_type = 'Billing'");
            $cartAddressData = $cartAddressCollection->getResource()->getReadConnection()->fetchRow($select);
            $cartBillingAddress = Mage::getModel('order/cart_address')->load($cartAddressData['cart_address_id'],'cart_address_id');
            /* print_r($cartBillingAddress); die; */
            $cartBillingAddress->same_as_billing = 1;
            $cartBillingAddress->save();
            /* $select = $cartAddressCollection->getSelect()->where("cart_id = $cartId AND address_type = 'Shipping'");
            $cartAddressData = $cartAddressCollection->getResource()->getReadConnection()->fetchRow($select);
            $cartShippingAddress = Mage::getModel('order/cart_address')->load($cartAddressData['cart_address_id'],'cart_address_id')->delete(); */
            }
            
        //$cartShippingAddress->setData($shippingAddress);
        $cartShippingAddress->address = $shippingAddress['address'];
        $cartShippingAddress->state = $shippingAddress['state'];
        $cartShippingAddress->city = $shippingAddress['city'];
        $cartShippingAddress->zipcode = $shippingAddress['zipcode'];
        $cartShippingAddress->country = $shippingAddress['country'];
        $cartShippingAddress->phone = $shippingAddress['phone'];
        $cartShippingAddress->cart_id = $cartId;
        $cartShippingAddress->address_id = $customerAddressId;
        $cartShippingAddress->same_as_billing = 0;
        $cartShippingAddress->address_type = "Shipping";
        
        $cartShippingAddress->save();
        print_r($cartShippingAddress);
        die; 
        $this->_redirect('*/adminhtml_order/start');
    }

    public function saveBillingAddressAction()
    {
        echo "<pre>";
        $cartId = $this->getCart()->getId();
        $customerId = Mage::getModel('order/session')->getCustomerId();
        $billingAddress = $this->getRequest()->getPost('billing'); 
        $customerAddressId = Mage::getModel('customer/customer')->load($customerId)->getDefaultBillingAddress()->getEntityId();

        $cartAddressCollection = Mage::getModel('order/cart_address')->getCollection();
        $select = $cartAddressCollection->getSelect()->where("cart_id = $cartId AND address_type = 'Billing'");
        $cartAddressData = $cartAddressCollection->getResource()->getReadConnection()->fetchRow($select);
        
        if ($cartAddressData) {
            $cartAddress = Mage::getModel('order/cart_address')->load($cartAddressData['cart_address_id']); 
            unset($cartAddressData['cart_address_id']);
        }else{
            $cartAddress = Mage::getModel('order/cart_address');
        }
        $cartAddress->address = $billingAddress['address'];
        $cartAddress->state = $billingAddress['state'];
        $cartAddress->city = $billingAddress['city'];
        $cartAddress->zipcode = $billingAddress['zipcode'];
        $cartAddress->country = $billingAddress['country'];
        $cartAddress->phone = $billingAddress['phone'];
        $cartAddress->cart_id = $cartId;
        $cartAddress->address_id = $customerAddressId;
        $cartAddress->address_type = "Billing";
       
        $cartAddress->save();
        print_r($cartAddress);
        die;
        $this->_redirect('*/adminhtml_order/start');

    }

}