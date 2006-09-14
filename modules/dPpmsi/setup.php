<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

require_once($AppUI->getModuleClass("system", "module"));

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPpmsi";
$config["mod_version"]     = "0.13";
$config["mod_type"]        = "user";
$config["mod_config"]      = true;

if (@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupdPpmsi {

  function configure() {
    global $AppUI;
    $AppUI->redirect("m=dPpmsi&a=configure");
    return true;
  }

  function remove() {
    db_exec("DROP TABLE ghm;"); db_error();
    return null;
  }

  function upgrade($old_version) {
    switch ($old_version) {
      case "all":
      case "0.1":
        $sql = "CREATE TABLE `ghm` (
          `ghm_id` BIGINT NOT NULL AUTO_INCREMENT ,
          `operation_id` BIGINT NOT NULL ,
          `DR` VARCHAR( 10 ) ,
          `DASs` TEXT,
          `DADs` TEXT,
          PRIMARY KEY ( `ghm_id` ) ,
          INDEX ( `operation_id` )
          ) TYPE=MyISAM COMMENT = 'Table des GHM';";
          db_exec( $sql ); db_error();
  
      case "0.11":
        $module = @CModule::getInstalled("dPplanningOp");
        if (!$module || $module->mod_version < "0.38") {
          return "0.11";
        }

        $sql = "ALTER TABLE `ghm`" .
          "ADD `sejour_id` INT NOT NULL AFTER `operation_id`;";
        db_exec($sql); db_error();
          
        $sql = "UPDATE `ghm`, `operations` SET" .
          "\n`ghm`.`sejour_id` = `operations`.`sejour_id`" .
          "\nWHERE `ghm`.`operation_id` = `operations`.`operation_id`";
        db_exec($sql); db_error();
  
      case "0.12":
        $sql = "ALTER TABLE `ghm` DROP `operation_id` ;";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `ghm` ADD INDEX ( `sejour_id` ) ;";
        db_exec($sql); db_error();
      case "0.13":
        return "0.13";
    }
    return false;
  }
}

?>