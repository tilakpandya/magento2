<?php

$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('order/cart'))
    ->addColumn('cart_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ))
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ))
    ->addColumn('session_id', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'unsigned' => true,
        'nullable' => true,
    ))
    ->addColumn('total', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array(
        'unsigned' => true,
        'nullable' => false,
    ))
    ->addColumn('discount', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array(
        'unsigned' => true,
        'nullable' => false,
    ))
    ->addColumn('shipping_method_code', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'unsigned' => true,
        'nullable' => false,
    ))
    ->addColumn('shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array(
        'unsigned' => true,
        'nullable' => false,
    ))
    ->addColumn('payment_method_code', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'unsigned' => true,
        'nullable' => false,
    ))
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'unsigned' => true,
        'nullable' => false,
    ));    

$installer->getConnection()->createTable($table);

$installer->endSetup();



$installer = $this;
$installer->startSetUp();

//Table Order
$orderTable = $installer->getConnection()
    ->newTable($installer->getTable('order/order'))
    ->addColumn('order_id',Varien_Db_Ddl_Table::TYPE_SMALLINT,null,array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'identity' =>true
    ),'Order Id')
    ->addColumn('customer_id',Varien_Db_Ddl_Table::TYPE_SMALLINT,null,array(
        'nullable' => false,
    ),'Customer Id')
    ->addColumn('customer_name',Varien_Db_Ddl_Table::TYPE_SMALLINT,null,array(
        'nullable' => false,
    ),'Customer Name')
    ->addColumn('customer_email',Varien_Db_Ddl_Table::TYPE_SMALLINT,null,array(
        'nullable' => false,
    ),'Customer Email')
    ->addColumn('shipping_name',Varien_Db_Ddl_Table::TYPE_VARCHAR,null,array(
        'nullable' => false
    ),'Shipping Name')
    ->addColumn('shipping_code',Varien_Db_Ddl_Table::TYPE_VARCHAR,null,array(
        'nullable' => false
    ),'Shipping code')
    ->addColumn('shipping_charge',Varien_Db_Ddl_Table::TYPE_DECIMAL,null,array(
        'nullable' => false
    ),'Shipping charge')
    ->addColumn('shipping_amount',Varien_Db_Ddl_Table::TYPE_DECIMAL,null,array(
        'nullable' => false
    ),'Shipping amount')
    ->addColumn('payment_code',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(
        'nullable' => true
    ),'payment id')
    ->addColumn('payment_name',Varien_Db_Ddl_Table::TYPE_VARCHAR,null,array(
        'nullable' => false
    ),'Payment Name')
    
    ->addForeignKey(
        $installer->getFkName(
            'order/order',
            'customer_id',
            'customer/entity',
            'entity_id'
        ),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
   
$installer->getConnection()->createTable($orderTable);

$installer->endSetup();

