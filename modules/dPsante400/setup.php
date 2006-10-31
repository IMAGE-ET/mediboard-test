<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsante400
* @version $Revision: $
* @author Thomas Despoix
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPsante400";
$config["mod_version"]     = "0.13";
$config["mod_type"]        = "user";
$config["mod_config"]      = true;

if (@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupdPsante400 {

  function configure() {
    global $AppUI;
    $AppUI->redirect("m=dPsante400&a=configure");
    return true;
  }

  function remove() {
    db_exec("DROP TABLE `id_sante400`;"); db_error();
    return null;
  }

  function upgrade($old_version) {
    switch ($old_version) {
      case "all":
        $sql = "CREATE TABLE `id_sante400` (" .
            "\n`id_sante400_id` INT NOT NULL AUTO_INCREMENT ," .
            "\n`object_class` VARCHAR( 25 ) NOT NULL ," .
            "\n`object_id` INT NOT NULL ," .
            "\n`tag` VARCHAR( 80 ) ," .
            "\n`last_update` DATETIME NOT NULL ," .
            "\nPRIMARY KEY ( `id_sante400_id` ) ," .
            "\nINDEX ( `object_class` , `object_id` , `tag` )) TYPE=MyISAM;";
        db_exec( $sql ); db_error();
        
      case "0.1":
        $sql = "ALTER TABLE `id_sante400` ADD `id400` VARCHAR( 8 ) NOT NULL ;";
        db_exec( $sql ); db_error();
        
      case "0.11":
        $sql = "ALTER TABLE `id_sante400` CHANGE `id400` `id400` VARCHAR( 10 ) NOT NULL ;";
        db_exec( $sql ); db_error();
      case "0.12":
        $sql = "ALTER TABLE `id_sante400` " .
               "\nCHANGE `id_sante400_id` `id_sante400_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `object_id` `object_id` int(11) unsigned NOT NULL;";
        db_exec( $sql ); db_error();
        
      case "0.13":
        return "0.13";
      }
    return false;
  }
}

?>