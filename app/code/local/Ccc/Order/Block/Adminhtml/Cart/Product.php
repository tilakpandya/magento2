<?php

class Ccc_Order_Block_Adminhtml_Cart_Product extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_cart';
        $this->_blockGroup = 'order';
        $this->_headerText = $this->__('Cart');
        $this->setTemplate('order/adminhtml/product.phtml');

    }

    public function getProducts()
    {
        $collection = Mage::getModel('catalog/product')->getResourceCollection();
        $collection->addAttributeToSelect('name');
        $collection->joinAttribute(
            'name',
            'catalog_product/name',
            'entity_id',
             null,
            'inner'
        );
        $collection->joinAttribute(
            'price',
            'catalog_product/price',
            'entity_id',
             null,
            'inner'
        );
       return $collection->getData();
    }
}
