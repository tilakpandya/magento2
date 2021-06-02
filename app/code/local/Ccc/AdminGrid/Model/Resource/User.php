<?php

class Ccc_AdminGrid_Model_Resource_User extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct() 
    {
        $this->_init('admingrid/user','id');
    }
}
