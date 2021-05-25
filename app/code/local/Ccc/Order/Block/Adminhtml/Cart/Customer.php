<?php

class Ccc_Order_Block_Adminhtml_Cart_Customer extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_cart';
        $this->_blockGroup = 'order';
        $this->_headerText = $this->__('Cart');
        $this->setTemplate('order/adminhtml/customer.phtml');

    }

    public function getCustomers()
    {
        $collection = Mage::getModel('customer/customer')->getResourceCollection();
        $collection->addAttributeToSelect('name');
        $collection->joinAttribute(
            'firstName',
            'customer/firstName',
            'entity_id',
            null,
            'inner'
        );
        $collection->joinAttribute(
            'lastName',
            'customer/lastName',
            'entity_id',
            null,
            'inner'
        );
       return $collection;
    }
}
