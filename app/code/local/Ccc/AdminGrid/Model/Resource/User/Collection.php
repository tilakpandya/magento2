<?php
class Ccc_AdminGrid_Model_Resource_User_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {  
        $this->_init('admingrid/user');
    }  
}