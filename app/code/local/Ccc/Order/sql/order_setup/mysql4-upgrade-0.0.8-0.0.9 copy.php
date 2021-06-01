<?php
$installer = $this;

$installer->startSetup();
$installer->run("
ALTER TABLE `order`
ADD  cart_id int(11) null");
$installer->endSetup();






