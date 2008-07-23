<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

class CSetupdPstock extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = 'dPstock';
    $this->makeRevision('all');

    $sql = 'CREATE TABLE `product` (
			 `product_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
			 `name` VARCHAR(50) NOT NULL, 
			 `description` TEXT, 
			 `code` VARCHAR(32) NULL,
			 `category_id` INT(11) UNSIGNED NOT NULL, 
			 `societe_id` INT(11) UNSIGNED NOT NULL, 
			PRIMARY KEY (`product_id`)) TYPE=MYISAM;';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product`
      ADD UNIQUE INDEX (code),
      ADD INDEX (category_id),
      ADD INDEX (societe_id)';
    $this->addQuery($sql);

    $sql = 'CREATE TABLE `product_category` (
			 `category_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
			 `name` VARCHAR(50) NOT NULL, 
			PRIMARY KEY (`category_id`)) TYPE=MYISAM;';
    $this->addQuery($sql);

    $sql = 'CREATE TABLE `product_delivery` (
		 `delivery_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
		 `product_id` INT(11) UNSIGNED NOT NULL, 
		 `date` DATETIME NOT NULL, 
		 `target_class` VARCHAR(25) NOT NULL, 
		 `target_id` INT(11) UNSIGNED NOT NULL, 
		 `code` VARCHAR(32) NULL, 
		 `description` TEXT, 
		PRIMARY KEY (`delivery_id`)) TYPE=MYISAM;';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_delivery`
      ADD INDEX (code),
      ADD INDEX (product_id),
      ADD INDEX (target_id),
      ADD INDEX (target_class),
      ADD INDEX (date)';
    $this->addQuery($sql);

    $sql = 'CREATE TABLE `product_order` (
		 `order_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
		 `date_ordered` DATETIME, 
		 `societe_id` INT(11) UNSIGNED NOT NULL, 
		 `group_id` INT(11) UNSIGNED NOT NULL, 
		 `locked` BOOL NULL DEFAULT \'0\',
		 `cancelled` BOOL NULL DEFAULT \'0\',
		 `deleted` BOOL NULL DEFAULT \'0\',
		 `order_number` VARCHAR(64) NOT NULL, 
		PRIMARY KEY (`order_id`)) TYPE=MYISAM;';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_order`
      ADD UNIQUE INDEX (order_number),
      ADD INDEX (societe_id),
      ADD INDEX (group_id),
      ADD INDEX (date_ordered)';
    $this->addQuery($sql);

    $sql = 'CREATE TABLE `product_order_item` (
		 `order_item_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
		 `reference_id` INT(11) UNSIGNED NOT NULL, 
		 `order_id` INT(11) UNSIGNED NOT NULL, 
		 `quantity` INT(11) UNSIGNED NOT NULL, 
		 `unit_price` FLOAT, 
		 `date_received` DATETIME, 
		 `quantity_received` INT(11) UNSIGNED DEFAULT \'0\',
		PRIMARY KEY (`order_item_id`)) TYPE=MYISAM;';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_order_item`
      ADD INDEX (reference_id),
      ADD INDEX (order_id),
      ADD INDEX (date_received)';
    $this->addQuery($sql);

    $sql = 'CREATE TABLE `product_reference` (
		 `reference_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
		 `product_id` INT(11) UNSIGNED NOT NULL, 
		 `societe_id` INT(11) UNSIGNED NOT NULL, 
		 `quantity` INT(11) UNSIGNED NOT NULL, 
		 `code` VARCHAR(32) NULL, 
		 `price` FLOAT NOT NULL, 
		PRIMARY KEY (`reference_id`)) TYPE=MYISAM;';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_reference`
      ADD UNIQUE INDEX (code),
      ADD INDEX (product_id),
      ADD INDEX (societe_id)';
    $this->addQuery($sql);

    $sql = 'CREATE TABLE `product_stock` (
		 `stock_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
		 `product_id` INT(11) UNSIGNED NOT NULL, 
		 `group_id` INT(11) UNSIGNED NOT NULL, 
		 `quantity` INT(11) UNSIGNED NOT NULL, 
		 `order_threshold_critical` INT(11) UNSIGNED, 
		 `order_threshold_min` INT(11) UNSIGNED NOT NULL, 
		 `order_threshold_optimum` INT(11) UNSIGNED, 
		 `order_threshold_max` INT(11) UNSIGNED NOT NULL, 
		PRIMARY KEY (`stock_id`)) TYPE=MYISAM;';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_stock`
      ADD INDEX (product_id),
      ADD INDEX (group_id)';
    $this->addQuery($sql);

    $sql = 'CREATE TABLE `societe` (
		 `societe_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
		 `name` VARCHAR(50) NOT NULL, 
		 `address` VARCHAR(255), 
		 `postal_code` INT(11), 
		 `city` VARCHAR(255), 
		 `phone` VARCHAR(255), 
		 `email` VARCHAR(50), 
		 `fax` VARCHAR(16) NULL,
		 `siret` VARCHAR(14) NULL,
		 `contact_name` VARCHAR(50), 
		 `contact_surname` VARCHAR(50), 
		PRIMARY KEY (`societe_id`)) TYPE=MYISAM;';
    $this->addQuery($sql);
    
    $sql = 'CREATE TABLE `product_stock_out` (
		 `stock_out_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
		 `stock_id` INT(11) UNSIGNED NOT NULL, 
		 `date` DATETIME NOT NULL, 
		 `quantity` INT(11) NOT NULL, 
		 `code` VARCHAR(32), 
		 `function_id` INT(11) UNSIGNED, 
		PRIMARY KEY (`stock_out_id`)) TYPE=MYISAM;';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_stock_out` 
      ADD INDEX (code),
      ADD INDEX (stock_id),
      ADD INDEX (function_id)';
    $this->addQuery($sql);
    
    
    $this->makeRevision('0.2');
    $sql = 'CREATE TABLE `product_stock_service` (
      `stock_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `product_id` INT (11) UNSIGNED NOT NULL,
      `function_id` INT (11) UNSIGNED NOT NULL,
      `quantity` INT (11) NOT NULL
    ) TYPE=MYISAM;';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_stock_service` 
      ADD INDEX (`product_id`),
      ADD INDEX (`function_id`);';
    $this->addQuery($sql);
    
    $sql = 'RENAME TABLE `product_stock` TO `product_stock_group` ;';
    $this->addQuery($sql);
    
    $this->makeRevision('0.3');
    $sql = 'DROP TABLE `product_delivery`;';
    $this->addQuery($sql);
    
    $this->makeRevision('0.4');
    $sql = 'RENAME TABLE `product_stock_out` TO `product_delivery`;';
    $this->addQuery($sql);
    
    $this->makeRevision('0.5');
    $sql = 'ALTER TABLE `product_delivery` 
            CHANGE `stock_out_id` `delivery_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT'; 
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_delivery` CHANGE `function_id` `service_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_stock_service` CHANGE `function_id` `service_id` INT( 11 ) UNSIGNED NOT NULL';
    $this->addQuery($sql);
    
    $this->makeRevision('0.6');
    $sql = 'ALTER TABLE `product_delivery` 
      ADD `status` ENUM (\'planned\',\'done\') NOT NULL,
      ADD INDEX (`date`);';
    $this->addQuery($sql);
    
    $this->makeRevision('0.7');
    $sql = 'ALTER TABLE `product_delivery` 
      CHANGE `date` `date_dispensation` DATETIME NOT NULL';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_delivery` 
      ADD `date_delivery` DATETIME NULL AFTER `date_dispensation`,
      DROP `status`,
      CHANGE `service_id` `service_id` INT( 11 ) UNSIGNED NOT NULL,
      ADD INDEX (`date_delivery`);';
    $this->addQuery($sql);
    
    $this->makeRevision('0.8');
    $sql = 'ALTER TABLE `product_delivery` ADD `patient_id` INT( 11 ) UNSIGNED NULL ;';
    $this->addQuery($sql);
    
    $this->makeRevision('0.9');
    $sql = 'ALTER TABLE `product_delivery` ADD `date_reception` DATETIME NULL AFTER `date_delivery`;';
    $this->addQuery($sql);
    
    $this->mod_version = '0.91';
  }
}

?>