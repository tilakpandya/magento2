<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE cart ADD shipping_name text"
);
$installer->endSetup();

$installer->startSetup();

$installer->run("
ALTER TABLE cart ADD payment_name text"
);
$installer->endSetup();

