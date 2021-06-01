<?php
$installer = $this;

$installer->startSetup();
$installer->run("
ALTER TABLE `order`
ADD  status text");
$installer->endSetup();

$installer->startSetup();
$installer->run("
ALTER TABLE `order`
ADD  created_at timestamp");
$installer->endSetup();




