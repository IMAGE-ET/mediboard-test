<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
    
    $this->makeRevision('0.91');
    $sql = 'ALTER TABLE `product_order_item` 
      DROP `date_received`,
      DROP `quantity_received`';
    $this->addQuery($sql);
    
    $sql = 'CREATE TABLE `product_order_item_reception` (
      `order_item_reception_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `order_item_id` INT (11) UNSIGNED NULL,
      `quantity` INT (11) NOT NULL,
      `code` VARCHAR (255),
      `date` DATETIME NOT NULL
    ) TYPE=MYISAM;';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_order_item_reception` 
      ADD INDEX (`order_item_id`),
      ADD INDEX (`date`),
      ADD INDEX (`code`)';
    $this->addQuery($sql);
    
    $this->makeRevision('0.92');
    $sql = 'CREATE TABLE `product_delivery_trace` (
      `delivery_trace_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `delivery_id` INT (11) UNSIGNED NOT NULL,
      `quantity` INT (11) NOT NULL,
      `code` VARCHAR (255),
      `date_delivery` DATETIME NOT NULL,
      `date_reception` DATETIME) TYPE=MYISAM;';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_delivery_trace` 
      ADD INDEX (`delivery_id`),
      ADD INDEX (`code`),
      ADD INDEX (`date_delivery`),
      ADD INDEX (`date_reception`);';
    $this->addQuery($sql);
    
    $sql = 'INSERT INTO `product_delivery_trace`
      SELECT \'\', product_delivery.delivery_id, 
                   product_delivery.quantity, 
                   product_delivery.code, 
                   product_delivery.date_delivery, 
                   product_delivery.date_reception
      FROM product_delivery';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_delivery`
      DROP `code`, 
      DROP `date_delivery`,
      DROP `date_reception`';
    $this->addQuery($sql);
	  
    $sql = 'ALTER TABLE `product_reference` CHANGE `price` `price` DECIMAL(10, 5) NOT NULL';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_stock_service`
      ADD `order_threshold_critical` INT(11) UNSIGNED, 
      ADD `order_threshold_min` INT(11) UNSIGNED NOT NULL, 
      ADD `order_threshold_optimum` INT(11) UNSIGNED, 
      ADD `order_threshold_max` INT(11) UNSIGNED NOT NULL';
    $this->addQuery($sql);
    
    $this->makeRevision('0.93');
    $sql = 'CREATE TABLE `product_discrepancy` (
		  `discrepancy_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
		  `quantity` INT (11) NOT NULL,
		  `date` DATETIME NOT NULL,
		  `description` TEXT,
		  `object_id` INT (11) UNSIGNED NOT NULL,
		  `object_class` ENUM (\'CProductStockGroup\',\'CProductStockService\') NOT NULL) TYPE=MYISAM;';
    $this->addQuery($sql);
    
		$sql = 'ALTER TABLE `product_discrepancy` 
		  ADD INDEX (`date`),
		  ADD INDEX (`object_id`);';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_delivery_trace` CHANGE `date_delivery` `date_delivery` DATETIME NULL';
    $this->addQuery($sql);
    
    $this->makeRevision('0.94');
    $sql = 'ALTER TABLE `product` 
		  CHANGE `societe_id` `societe_id` INT (11) UNSIGNED,
		  ADD `quantity` INT (10) UNSIGNED NOT NULL,
		  ADD `item_title` VARCHAR (255),
		  ADD `unit_quantity` DECIMAL (10,4) UNSIGNED,
		  ADD `unit_title` VARCHAR (255),
		  ADD `packaging` VARCHAR (255)';
    $this->addQuery($sql);
    
    $this->makeRevision('0.95');
    $sql = 'ALTER TABLE `product_order_item_reception` ADD `lapsing_date` DATE NOT NULL AFTER `code`';
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `product` ADD `renewable` ENUM( '0', '1', '2' ) NOT NULL";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `product_order_item_reception` ADD `barcode_printed` ENUM( '0', '1' ) NOT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.96");
    $sql = "ALTER TABLE `product` ADD `code_lpp` INT (7) UNSIGNED ZEROFILL";
    $this->addQuery($sql);
		
		$this->makeRevision("0.97");
		$sql = "ALTER TABLE `product_stock_group` ADD `location_id` INT (11) UNSIGNED;";
		$this->addQuery($sql);
		$sql = "ALTER TABLE `product_stock_group` ADD INDEX (`location_id`);";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `product_stock_location` (
					  `stock_location_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
					  `name` VARCHAR (255) NOT NULL,
					  `desc` TEXT,
					  `position` INT (11),
					  `group_id` INT (11) UNSIGNED NOT NULL
					) TYPE=MYISAM;";
		$this->addQuery($sql);
    $sql = "ALTER TABLE `product_stock_location` ADD INDEX (`group_id`);";
		$this->addQuery($sql);
    
    $this->makeRevision('0.98');
    $sql = "ALTER TABLE `product_delivery` ADD `order` ENUM('0','1')";
    $this->addQuery($sql);
    
    $this->makeRevision('0.99');
    $sql = "ALTER TABLE `product` ADD `cancelled` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($sql);
    
    $this->makeRevision('1.00');
    $sql = "ALTER TABLE `product_stock_service` ADD `common` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($sql);
    
    $this->makeRevision('1.01');
    $sql = "ALTER TABLE `product_delivery` 
              ADD INDEX (`patient_id`);";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `product_order_item_reception` 
              ADD INDEX (`lapsing_date`)";
    $this->addQuery($sql); 
       
    $this->makeRevision('1.02');
    $sql = "UPDATE `product_stock_service` SET `common` = '1' WHERE `common` IS NULL OR `common` = ''";
    $this->addQuery($sql);
       
    $this->makeRevision('1.03');
    $sql = "ALTER TABLE `product_order_item` CHANGE `order_id` `order_id` INT (11) UNSIGNED";
    $this->addQuery($sql);
    
    $this->mod_version = "1.04";
  }
}
