<?php
class Ccc_Chat_Block_Adminhtml_Chat extends Mage_Core_Block_Template
{
    public function _construct() {
        //$this->setTemplate('chat/adminhtml/grid.phtml');
        $this->_controller = 'adminhtml_chat';
        $this->_blockGroup = 'chat';
        $this->_headerText = $this->__('Chat');
        parent::_construct();
    }

}