<?php
class Ccc_Chat_Adminhtml_ChatController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {   
        $this->_title($this->__('Chat'));
        $this->loadLayout()
            ->_setActiveMenu('chat');
        $this->renderLayout();
    }
}