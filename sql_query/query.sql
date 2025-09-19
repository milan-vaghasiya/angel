ALTER TABLE `temp_stock` ADD `item_type` INT NOT NULL DEFAULT '0' AFTER `location_id`;
UPDATE `temp_stock` SET `item_type` = '3' WHERE `temp_stock`.`id` = 560;

update `temp_stock` set category_id = 12 WHERE `item_type` = 1;
update `temp_stock` set category_id = 13 WHERE `item_type` = 3;
update `temp_stock` set category_id = 14 WHERE `item_type` = 2;
update `temp_stock` set category_id = 15 WHERE `item_type` = 9;
