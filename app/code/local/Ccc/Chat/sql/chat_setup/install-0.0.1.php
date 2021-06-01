<?php

$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('chat/chat'))
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ))
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ))
    ->addColumn('contact_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ))
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'unsigned' => true,
        'nullable' => true,
    ));
       

$installer->getConnection()->createTable($table);

$installer->endSetup();



