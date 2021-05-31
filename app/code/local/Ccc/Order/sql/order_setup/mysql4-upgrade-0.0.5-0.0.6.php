<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `cart_item`
ADD  product_name text");
$installer->endSetup();




