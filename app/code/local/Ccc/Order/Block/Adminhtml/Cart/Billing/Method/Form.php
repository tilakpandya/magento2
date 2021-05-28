<?php

class Ccc_Order_Block_Adminhtml_Cart_Billing_Method_Form extends Mage_Core_Block_Template
{
    public function getPayemntMethodTitle()
    {
        $methods = Mage::getModel('payment/config');
        $activemethod = $methods->getActiveMethods();
        unset($activemethod['paypal_billing_agreement']);
        unset($activemethod['checkmo']);
        unset($activemethod['free']);
        return $activemethod;
    }

}
