<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPhospi";
$config["mod_version"]     = "0.18";
$config["mod_type"]        = "user";
$config["mod_config"]      = true;

if (@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupdPhospi {

  function configure() {
    global $AppUI;
    $AppUI->redirect("m=dPhospi&a=configure");
    return true;
  }

  function remove() {
    db_exec("DROP TABLE `service`;");     db_error();
    db_exec("DROP TABLE `chambre`;");     db_error();
    db_exec("DROP TABLE `lit`;");         db_error();
    db_exec("DROP TABLE `affectation`;"); db_error();

    return null;
  }

  function upgrade($old_version) {
    switch ($old_version) {
      case "all":
        $sql = "CREATE TABLE `service` (" .
          "\n`service_id` INT NOT NULL AUTO_INCREMENT ," .
          "\n`nom` VARCHAR( 50 ) NOT NULL ," .
          "\n`description` TEXT," .
          "\nPRIMARY KEY ( `service_id` )) TYPE=MyISAM;";
        db_exec($sql); db_error();
        $sql = "CREATE TABLE `chambre` (" .
          "\n`chambre_id` INT NOT NULL AUTO_INCREMENT ," .
          "\n`service_id` INT NOT NULL ," .
          "\n`nom` VARCHAR( 50 ) ," .
          "\n`caracteristiques` TEXT," .
          "\nPRIMARY KEY ( `chambre_id` ) ," .
          "\nINDEX ( `service_id` )) TYPE=MyISAM;";
        db_exec($sql); db_error();
        $sql = "CREATE TABLE `lit` (" .
          "\n`lit_id` INT NOT NULL AUTO_INCREMENT ," .
          "\n`chambre_id` INT NOT NULL," .
          "\n`nom` VARCHAR( 50 ) NOT NULL ," .
          "\nPRIMARY KEY ( `lit_id` ) ," .
          "\nINDEX ( `chambre_id` )) TYPE=MyISAM;";
        db_exec($sql); db_error();
      case "0.1":
        $sql = "CREATE TABLE `affectation` (" .
            "\n`affectation_id` INT NOT NULL AUTO_INCREMENT," .
            "\n`lit_id` INT NOT NULL ," .
            "\n`operation_id` INT NOT NULL ," .
            "\n`entree` DATETIME NOT NULL ," .
            "\n`sortie` DATETIME NOT NULL ," .
            "\nPRIMARY KEY ( `affectation_id` ) ," .
            "\nINDEX ( `lit_id` , `operation_id` )) TYPE=MyISAM;";
        db_exec($sql); db_error();

      case "0.11":
        $sql = "ALTER TABLE `affectation` " .
            "\nADD `confirme` TINYINT DEFAULT '0' NOT NULL," .
            "\nADD `effectue` TINYINT DEFAULT '0' NOT NULL ;";
        db_exec($sql); db_error();

      case "0.12":
        $sql = "ALTER TABLE `affectation` ADD INDEX ( `entree` );";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `affectation` ADD INDEX ( `sortie` );";
        db_exec($sql); db_error();

      case "0.13":
        $sql = "ALTER TABLE `affectation` ADD INDEX ( `operation_id` ) ;";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `affectation` ADD INDEX ( `lit_id` ) ;";
        db_exec($sql); db_error();

      case "0.14":
        $module = @CModule::getInstalled("dPplanningOp");
        if (!$module || $module->mod_version < "0.38") {
          return "0.14";
        }

        $sql = "DELETE affectation.* FROM affectation" .
            "\nLEFT JOIN operations" .
            "\nON affectation.operation_id = operations.operation_id" .
            "\nWHERE operations.operation_id IS NULL;";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `affectation`" .
            "\nADD `sejour_id` INT UNSIGNED DEFAULT '0' NOT NULL AFTER `operation_id`;";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `affectation` ADD INDEX (`sejour_id`);";
        db_exec($sql); db_error();
        $sql = "UPDATE `affectation`,`operations`" .
            "\nSET `affectation`.`sejour_id` = `operations`.`sejour_id`" .
            "\nWHERE `affectation`.`operation_id` = `operations`.`operation_id`;";
        db_exec($sql); db_error();
      case "0.15":
        $module = @CModule::getInstalled("dPetablissement");
        if (!$module || $module->mod_version < "0.1") {
          return "0.15";
        }
        $sql = "ALTER TABLE `service` ADD `group_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `service_id`;";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `service` ADD INDEX ( `group_id` ) ;";
        db_exec( $sql ); db_error();
      case "0.16":
        $sql = "ALTER TABLE `affectation` DROP `operation_id`";
        db_exec( $sql ); db_error();
      case "0.17":
        $sql = "ALTER TABLE `affectation` " .
               "\nCHANGE `affectation_id` `affectation_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `lit_id` `lit_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `confirme` `confirme` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `effectue` `effectue` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `sejour_id` `sejour_id` int(11) unsigned NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `chambre` " .
               "\nCHANGE `chambre_id` `chambre_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `service_id` `service_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL;";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `lit` " .
               "\nCHANGE `lit_id` `lit_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chambre_id` `chambre_id` int(11) unsigned NOT NULL," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL;";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `service` " .
               "\nCHANGE `service_id` `service_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `group_id` `group_id` int(11) unsigned NOT NULL DEFAULT '1'," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL;";
        db_exec( $sql ); db_error();
        
      case "0.18":
        return "0.18";
    }
    return false;
  }

}

?>