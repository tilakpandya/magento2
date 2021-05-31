<?php
class Ccc_Order_Block_Adminhtml_Order_View extends Mage_Core_Block_Template
{

    public function __construct()
    {

        parent::__construct();
        $this->_controller = 'adminhtml_order';
        $this->_blockGroup = 'order';
        $this->_headerText = $this->__('Cart');
        $this->setTemplate('order/adminhtml/order/view.phtml');
    }

    public function getCustomer()
    {
       $orderId = $this->getRequest()->getParams('order_id')['order_id'];
       $order = Mage::getModel('order/order')->load($orderId);
       return $order;
    }

    public function getitem()
    {
       $orderId = $this->getRequest()->getParams('order_id')['order_id'];
       $order = Mage::getModel('order/order_item');
       $collection = $order->getCollection()->addFieldToFilter('order_id',['eq'=>$orderId]);
       $select = $collection->getSelect();
       $itemCollection = $order->getResource()->getReadConnection()->fetchAll($select);
       return $itemCollection;
    }

    public function getBillingAddress()
    {
        $orderId = $this->getRequest()->getParams('order_id')['order_id'];
       $order = Mage::getModel('order/order_address');
       $collection = $order->getCollection()
       ->addFieldToFilter('order_id',['eq'=>$orderId])
       ->addFieldToFilter('address_type',['eq'=>'Billing']);
       $select = $collection->getSelect();
       $itemCollection = $order->getResource()->getReadConnection()->fetchRow($select);
       $order->load($itemCollection['order_address_id']);
       return $order;
    }

    public function getShippingAddress()
    {
        $orderId = $this->getRequest()->getParams('order_id')['order_id'];
       $order = Mage::getModel('order/order_address');
       $collection = $order->getCollection()
       ->addFieldToFilter('order_id',['eq'=>$orderId])
       ->addFieldToFilter('address_type',['eq'=>'Shipping']);
       $select = $collection->getSelect();
       $itemCollection = $order->getResource()->getReadConnection()->fetchRow($select);
       $order->load($itemCollection['order_address_id']);
       return $order;
    }

    public function total()
    {
        $total = [];
        $orderId = $this->getRequest()->getParams('order_id')['order_id'];
        $order = Mage::getModel('order/order_item');
        $collection = $order->getCollection()->addFieldToFilter('order_id',['eq'=>$orderId]);
        $select = $collection->getSelect();
        $itemCollection = $order->getResource()->getReadConnection()->fetchAll($select);

        foreach ($itemCollection as $key => $value) {
            $total[] = ($value['price'] * $value['quantity']) - $value['discount'];
        }
        /*  */
        return array_sum($total);
    }
}
