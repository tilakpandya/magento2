<?php
class Ccc_Order_Model_Resource_Chat_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {  
        $this->_init('chat/chat');
    }  
}