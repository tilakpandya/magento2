<?php
class Ccc_Order_Model_Resource_Cart_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {  
        $this->_init('order/cart_item');
    }  
}