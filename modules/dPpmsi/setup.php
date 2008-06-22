<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision$
 * @author Romain Ollivier
 */

class CSetupdPpmsi extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPpmsi";
    
    $this->makeRevision("all");
    $this->makeRevision("0.1");
    $sql = "CREATE TABLE `ghm` (
          `ghm_id` BIGINT NOT NULL AUTO_INCREMENT ,
          `operation_id` BIGINT NOT NULL ,
          `DR` VARCHAR( 10 ) ,
          `DASs` TEXT,
          `DADs` TEXT,
          PRIMARY KEY ( `ghm_id` ) ,
          INDEX ( `operation_id` )
          ) TYPE=MyISAM COMMENT = 'Table des GHM';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $this->addDependency("dPplanningOp", "0.38");
    $sql = "ALTER TABLE `ghm` ADD `sejour_id` INT NOT NULL AFTER `operation_id`;";
    $this->addQuery($sql);
    $sql = "UPDATE `ghm`, `operations` SET" .
          "\n`ghm`.`sejour_id` = `operations`.`sejour_id`" .
          "\nWHERE `ghm`.`operation_id` = `operations`.`operation_id`";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql = "ALTER TABLE `ghm` DROP `operation_id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `ghm` ADD INDEX ( `sejour_id` ) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `ghm` " .
               "\nCHANGE `ghm_id` `ghm_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `sejour_id` `sejour_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    
    $this->mod_version = "0.14";
  }
}
?>