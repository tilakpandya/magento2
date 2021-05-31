<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `order`
MODIFY  customer_name text");
$installer->endSetup();

$installer->startSetup();
$installer->run("
ALTER TABLE `order`
MODIFY  customer_email text");
$installer->endSetup();


