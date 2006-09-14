<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPressources";
$config["mod_version"]     = "0.11";
$config["mod_type"]        = "user";
$config["mod_config"]      = true;

if (@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupdPressources {

  function configure() {
    global $AppUI;
    $AppUI->redirect("m=dPressources&a=configure");
    return true;
  }

  function remove() {
    db_exec("DROP TABLE plageressource;"); db_error();
    return null;
  }

  function upgrade($old_version) {
    switch ($old_version) {
      case "all":
        $sql = "CREATE TABLE `plageressource` (
                 `plageressource_id` BIGINT NOT NULL AUTO_INCREMENT ,
                 `prat_id` BIGINT,
                 `date` DATE NOT NULL ,
                 `debut` TIME NOT NULL ,
                 `fin` TIME NOT NULL ,
                 `tarif` FLOAT DEFAULT '0' NOT NULL ,
                 `paye` TINYINT DEFAULT '0' NOT NULL ,
                 `libelle` VARCHAR( 50 ) ,
                 PRIMARY KEY ( `plageressource_id` )
               ) TYPE=MyISAM COMMENT = 'Table des plages de ressource';";
        db_exec( $sql ); db_error();
      case "0.1":
        $sql = "ALTER TABLE `plageressource` ADD INDEX ( `prat_id` ) ;";
        db_exec($sql); db_error();
      case "0.11":
        return "0.11";
      }
    return false;
  }
}

?>