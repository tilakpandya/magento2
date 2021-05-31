<?php

$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('order/cart_address'))
    ->addColumn('cart_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ))
    ->addColumn('cart_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ))
    ->addColumn('address_id',  Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
    ))
    ->addColumn('address_type', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ))
    ->addColumn('address', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ))
    ->addColumn('city', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ))
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ))
    ->addColumn('country', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ))
    ->addColumn('zipcode', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ))
    ->addColumn('same_as_billing', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable' => true,
    ))
    ->addColumn('phone', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ))

    ->addForeignKey(
        $installer->getFkName(
            'order/cart_item',
            'address_id',
            'customer/address_entity',
            'entity_id'
        ),
        'address_id', $installer->getTable('customer/address_entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

        
    


$installer->getConnection()->createTable($table);
$installer->endSetup();



$installer = $this;
$installer->startSetUp();

//Table Order
$orderTable = $installer->getConnection()
    ->newTable($installer->getTable('order/cart_item'))
    ->addColumn('item_id',Varien_Db_Ddl_Table::TYPE_SMALLINT,null,array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'identity' =>true
    ))
    ->addColumn('cart_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ))
    ->addColumn('product_id',  Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ))
    ->addColumn('quantity',  Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ))
    ->addColumn('base_price',  Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array(
        'nullable' => false,
    ))
    ->addColumn('price',  Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array(
        'nullable' => false,
    ))
    ->addColumn('discount',  Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array(
        'nullable' => false,
    ))
    ->addColumn('created_at',  Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => false,
    ));
            
$installer->getConnection()->createTable($orderTable);

$installer->endSetup();

