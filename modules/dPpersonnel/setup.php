<?php

/**
* @package Mediboard
* @subpackage dPpersonnel
* @version $Revision:  $
* @author Alexis Granger
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPpersonnel";
$config["mod_version"]     = "0.1";
$config["mod_type"]        = "user";


class CSetupdPpersonnel extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPpersonnel";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE `affectation_personnel` (
             `affect_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,  
             `user_id` INT(11) UNSIGNED NOT NULL, 
             `realise` ENUM('0','1') NOT NULL, 
             `debut` DATETIME, 
             `fin` DATETIME, 
             `object_id` INT(11) UNSIGNED NOT NULL, 
             `object_class` VARCHAR(25) NOT NULL, 
             PRIMARY KEY (`affect_id`)
             ) TYPE=MYISAM COMMENT='Table des affectations du personnel';";
    
    $this->addQuery($sql);
    
    $this->mod_version = "0.1";
    
  }
}
    
