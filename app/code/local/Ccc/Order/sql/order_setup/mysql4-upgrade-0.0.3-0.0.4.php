<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `cart_item` CHANGE `cart_item_id` `item_id` INT(11) NOT NULL AUTO_INCREMENT"
);
$installer->endSetup();



$installer->startSetup();

$installer->run("
ALTER TABLE `order_item` CHANGE `order_item_id` `item_id` INT(11) NOT NULL AUTO_INCREMENT"
);
$installer->endSetup();