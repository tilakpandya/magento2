<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `cart_address` ADD `phone` varchar(15) NOT NULL"
);
$installer->endSetup();

$installer->startSetup();

$installer->run("
ALTER TABLE `order_address` ADD `phone` varchar(15) NOT NULL"
);
$installer->endSetup();