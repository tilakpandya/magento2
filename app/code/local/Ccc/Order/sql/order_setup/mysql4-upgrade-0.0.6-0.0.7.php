<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `order_item`
MODIFY  product_name text");
$installer->endSetup();

