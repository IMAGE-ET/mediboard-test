<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI;

// MODULE CONFIGURATION
// redundant now but mandatory until end of refactoring
$config = array();
$config['mod_name']    = 'dPstock';
$config['mod_version'] = '0.2';
$config['mod_type']    = 'user';

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
    
    $sql = 'ALTER TABLE `product` ADD UNIQUE (`code`);';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product` ADD INDEX (`code`);';
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

    $sql = 'CREATE TABLE `product_order` (
 `order_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
 `date_ordered` DATETIME, 
 `societe_id` INT(11) UNSIGNED NOT NULL, 
 `group_id` INT(11) UNSIGNED NOT NULL, 
 `locked` BOOL NOT DEFAULT \'0\',
 `cancelled` BOOL NULL DEFAULT \'0\',
 `deleted` BOOL NULL DEFAULT \'0\';
 `order_number` VARCHAR(64) NOT NULL, 
PRIMARY KEY (`order_id`)) TYPE=MYISAM;';
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

    $sql = 'CREATE TABLE `product_reference` (
 `reference_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
 `product_id` INT(11) UNSIGNED NOT NULL, 
 `societe_id` INT(11) UNSIGNED NOT NULL, 
 `quantity` INT(11) UNSIGNED NOT NULL, 
 `code` VARCHAR(32) NULL, 
 `price` FLOAT NOT NULL, 
PRIMARY KEY (`reference_id`)) TYPE=MYISAM;';
    $this->addQuery($sql);
    
    $sql = 'ALTER TABLE `product_reference` ADD INDEX (`code`);';
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
    
    $this->mod_version = '0.2';
  }
}

?>