<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPstats";
$config["mod_version"]     = "0.14";
$config["mod_type"]        = "user";
$config["mod_config"]      = true;

if (@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupdPstats {

  function configure() {
    global $AppUI;
    $AppUI->redirect("m=dPstats&a=configure");
    return true;
  }

  function remove() {
    db_exec( "DROP TABLE `temps_op`;" );
    db_exec( "DROP TABLE `temps_prepa`;" );
    return null;
  }

  function upgrade($old_version) {
    switch ($old_version) {
      case "all":
      case "0.1":
        $sql = "CREATE TABLE `temps_op` (
               `temps_op_id` INT(11) NOT NULL AUTO_INCREMENT ,
               `chir_id` INT(11) NOT NULL ,
               `ccam` VARCHAR( 100 ) NOT NULL ,
               `nb_intervention` INT(11) NOT NULL ,
               `estimation` TIME NOT NULL ,
               `occup_moy` TIME NOT NULL ,
               `occup_ecart` TIME NOT NULL ,
               `duree_moy` TIME NOT NULL ,
               `duree_ecart` TIME NOT NULL,
               PRIMARY KEY  (temps_op_id)
               ) TYPE=MyISAM COMMENT='Table temporaire des temps operatoire';";
        db_exec( $sql ); db_error();
        
        $sql = "CREATE TABLE `temps_prepa` (
               `temps_prepa_id` INT(11) NOT NULL AUTO_INCREMENT ,
               `chir_id` INT(11) NOT NULL ,
               `nb_prepa` INT(11) NOT NULL ,
               `nb_plages` INT(11) NOT NULL ,
               `duree_moy` TIME NOT NULL ,
               `duree_ecart` TIME NOT NULL,
               PRIMARY KEY  (temps_prepa_id)
               ) TYPE=MyISAM COMMENT='Table temporaire des temps preparatoire';";
        db_exec( $sql ); db_error();
                
      case "0.11":
        $sql = "ALTER TABLE `temps_op` ADD INDEX ( `chir_id` );";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `temps_op` ADD INDEX ( `ccam` );";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `temps_prepa` ADD INDEX ( `chir_id` );";
        db_exec( $sql ); db_error();
      case "0.12":
        $sql = "CREATE TABLE `temps_hospi` (
               `temps_hospi_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
               `praticien_id` INT(11) UNSIGNED NOT NULL,
               `ccam` VARCHAR( 100 ) NOT NULL,
               `type` ENUM('ambu','comp') DEFAULT 'ambu' NOT NULL,
               `nb_sejour` INT(11) UNSIGNED DEFAULT NULL,
               `duree_moy` FLOAT UNSIGNED DEFAULT NULL,
               `duree_ecart` FLOAT UNSIGNED DEFAULT NULL,
               PRIMARY KEY (`temps_hospi_id`),
               INDEX (`praticien_id`),
               INDEX (`ccam`)
               ) TYPE=MyISAM COMMENT='Table temporaire des temps d\'hospitalisation';";
        db_exec( $sql ); db_error();
      case "0.13":
        $sql = "ALTER TABLE `temps_hospi` " .
               "\nCHANGE `ccam` `ccam` varchar(255) NOT NULL;";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `temps_op` " .
               "\nCHANGE `temps_op_id` `temps_op_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `ccam` `ccam` varchar(255) NOT NULL," .
               "\nCHANGE `nb_intervention` `nb_intervention` int(11) unsigned NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `temps_prepa` " .
               "\nCHANGE `temps_prepa_id` `temps_prepa_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `nb_prepa` `nb_prepa` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `nb_plages` `nb_plages` int(11) unsigned NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
      case "0.14":
        return "0.14";
    }
    return false;
  }
}

?>