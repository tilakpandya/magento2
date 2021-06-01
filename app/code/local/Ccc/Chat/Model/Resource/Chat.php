<?php

class Ccc_Chat_Model_Resource_Chat extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct() 
    {
        $this->_init('chat/chat','user_id');
    }
}
