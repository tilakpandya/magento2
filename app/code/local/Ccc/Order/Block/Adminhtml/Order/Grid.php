<?php
class Ccc_Order_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        
        parent::__construct();
        $this->setId('order_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'order/order_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('order_id', array(
            'header'=> Mage::helper('order')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));


        $this->addColumn('customer_name', array(
            'header' => Mage::helper('order')->__('Customer Name'),
            'index' => 'customer_name',
        ));

        $this->addColumn('customer_email', array(
            'header' => Mage::helper('order')->__('Customer Email'),
            'index' => 'customer_email',
            
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('order')->__('Shipping Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('payment_name', array(
            'header' => Mage::helper('order')->__('payment name'),
            'index' => 'payment_name',
        ));

            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('order')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('order')->__('View'),
                            'url'     => array('base'=>'*/adminhtml_order/view'),
                            'field'   => 'order_id',
                            'data-column' => 'action',
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        
        $this->addRssList('rss/order/new', Mage::helper('order')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('order')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('order')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_id');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('order')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('order')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('order/order_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('order')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/adminhtml_order/view', array('order_id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/index', array('_current'=>true));
    }

}
