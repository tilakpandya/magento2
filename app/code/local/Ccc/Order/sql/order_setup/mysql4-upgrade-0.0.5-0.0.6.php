<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `cart_item`
DROP FOREIGN KEY product_id");
$installer->endSetup();

$installer->startSetup();
$installer->run("
ALTER TABLE `cart_item`
DROP CONSTRAINT product_id");
$installer->endSetup();

$installer->startSetup();
$installer->run("
ALTER TABLE `cart_item`
DROP CONSTRAINT cart_id");
$installer->endSetup();

$installer->startSetup();
$installer->run("
ALTER TABLE `cart_item`
DROP FOREIGN KEY cart_id"
);
$installer->endSetup();

/////////////////////////////////////////////////

$installer->startSetup();
$installer->run("
ALTER TABLE `order_item`
DROP FOREIGN KEY product_id");
$installer->endSetup();

$installer->startSetup();
$installer->run("
ALTER TABLE `order_item`
DROP CONSTRAINT product_id");
$installer->endSetup();

$installer->startSetup();
$installer->run("
ALTER TABLE `order_item`
DROP FOREIGN KEY order_id"
);
$installer->endSetup();

$installer->startSetup();
$installer->run("
ALTER TABLE `order_item`
DROP CONSTRAINT order_id");
$installer->endSetup();