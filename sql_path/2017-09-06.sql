ALTER TABLE  `fash_warehouse` ADD  `is_main` TINYINT NOT NULL AFTER  `shop_id` ;

ALTER TABLE  `fash_operation_product` ADD  `sub_operation_id` INT( 4 ) NOT NULL AFTER  `operation_id` ;

ALTER TABLE `fash_operation` DROP `sub_operation_id`;

