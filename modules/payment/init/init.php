<?php

// Add payment types to class loader
Phpr::$class_loader->add_module_directory('drivers/fee_actions');
Phpr::$class_loader->add_module_directory('drivers/fee_events');
Phpr::$class_loader->add_module_directory('drivers/payment_gateways');
