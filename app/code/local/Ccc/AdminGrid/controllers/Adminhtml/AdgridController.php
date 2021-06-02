<?php

class Ccc_AdminGrid_Adminhtml_AdgridController extends Mage_Adminhtml_Controller_Action
{
    protected $header = [];
    protected $data = [];
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admingrid/adgrid');
    }


	public function indexAction()
    {
        echo "<pre>";
       	/* $this->loadLayout();
	    $this->_title($this->__("Admin Grid"));
        $this->_addContent($this->getLayout()->createBlock('admingrid/adminhtml_adgrid'));
	    $this->renderLayout(); */
        
         //insert Data from mysql to csv
        $userData = $this->fetchData();
        $file = Mage::getBaseDir().'/media/order/data2.csv';
        $heading = [];
        $handler = fopen($file,'w',false);
        
        fputcsv($handler,$heading,",");
        foreach ($userData as $key => $data) {
            
            if (!$heading) {
                foreach ($data as $key => $value) {
                   if (!in_array($key,$heading)) {
                       $heading[] = $key;
                   }
                }
                fputcsv($handler,$heading,",");
            }
            $lineData = [$data['id'],$data['name'],$data['email'],$data['phone_number']];
            fputcsv($handler,$lineData,",");
           print_r($heading);
            
        }
        die;

        //insert Data from csv to mysql
        $dataArray = 0;
        $file = Mage::getBaseDir().'/media/order/data.csv';
        $handler = fopen($file,'r',false);
        
        while($row = fgetcsv($handler,4096,',','"','\\')){
            if (!$this->header) {
                $this->header = $row;
            } else {
                $this->data[] = array_combine($this->header, $row);
                //$this->data[] = $dataArray;
            }
        }
        
    }

    public function insertRecord()
    {
        if (!$this->data) {
            return false;
        }
        foreach ($this->data as $key => $data) { 
           $user = Mage::getModel('admingrid/user');          
           $user->addData($data);
           $user->save();
           print_r($user);  
        }
         
    }
    

    public function fetchData()
    {
       return $user = Mage::getModel('admingrid/user')->getCollection()->getData();           
    }

    public function saveAction()
    {
        echo "<pre>";
        $post = $this->getRequest()->getPost();
        $id = $this->getRequest()->getParam('id');
        if ($post) {
            try {
                $admingridModel = Mage::getModel('admingrid/adgrid');
                if ($id) {
                    $admingridModel = Mage::getModel('admingrid/adgrid')->load($id);
                }
                $admingridModel->item = $post['Admingrid']['item'];
                $admingridModel->save();
                
            } catch (\Throwable $th) {
                Mage::getSingleton('core/session')->addError('Unable to submit your request. Please, try again later');
                $this->_redirect('*/*/');
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    public function newAction()
    {
       $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $admingridModel = Mage::getModel('admingrid/adgrid')->load($id);
        
        if ($admingridModel->getId()) {    
  
           Mage::register('AdminGrid_data',$admingridModel);
        }
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('admingrid/adminhtml_adgrid_edit'));        
        $this->renderLayout();
        
    }

    public function deleteAction()
    {
        if($this->getRequest()->getParam('id') > 0 ) {
            try {
                $admingridModel = Mage::getModel('admingrid/adgrid');
                $admingridModel->setId($this->getRequest()->getParam('id'))
                ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/index');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
            $this->_redirect('*/*/index');
    }
}


?>