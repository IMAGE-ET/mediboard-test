<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

require_once($AppUI->getSystemClass("mbsetup"));

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'dPhospi';
$config['mod_version'] = '0.15';
$config['mod_directory'] = 'dPhospi';
$config['mod_setup_class'] = 'CSetupdPhospi';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Planning Hospi';
$config['mod_ui_icon'] = 'dPhospi.png';
$config['mod_description'] = 'Gestion de l\'hospitalisation';
$config['mod_config'] = true;

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupdPhospi {

	function configure() {
	global $AppUI;
		$AppUI->redirect( 'm=dPhospi&a=configure' );
  		return true;
	}

	function remove() {
    db_exec( "DROP TABLE `service`;" ); db_error();
    db_exec( "DROP TABLE `chambre`;" ); db_error();
    db_exec( "DROP TABLE `lit`;" ); db_error();
    db_exec( "DROP TABLE `affectation`;" ); db_error();

		return null;
	}

	function upgrade( $old_version ) {
		switch ( $old_version ) {
		  case "all":
		  case "0.1":
        $sql = "CREATE TABLE `affectation` (" .
            "\n`affectation_id` INT NOT NULL AUTO_INCREMENT," .
            "\n`lit_id` INT NOT NULL ," .
            "\n`operation_id` INT NOT NULL ," .
            "\n`entree` DATETIME NOT NULL ," .
            "\n`sortie` DATETIME NOT NULL ," .
            "\nPRIMARY KEY ( `affectation_id` ) ," .
            "\nINDEX ( `lit_id` , `operation_id` ));";
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
        $sql = "ALTER TABLE `affectation` DROP INDEX ( `lit_id` ) ;";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `affectation` ADD INDEX ( `lit_id` ) ;";
        db_exec($sql); db_error();

      case "0.14":
        if(CMbSetup::getVersionOf("dPplanningOp") < "0.38") {
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
	      return "0.15";
    }
	  return false;
	}

	function install() {
    $sql = "CREATE TABLE `service` (" .
      "\n`service_id` INT NOT NULL AUTO_INCREMENT ," .
      "\n`nom` VARCHAR( 50 ) NOT NULL ," .
      "\n`description` TEXT," .
      "\nPRIMARY KEY ( `service_id` ));";
    db_exec($sql); db_error();
    $sql = "CREATE TABLE `chambre` (" .
      "\n`chambre_id` INT NOT NULL AUTO_INCREMENT ," .
      "\n`service_id` INT NOT NULL ," .
      "\n`nom` VARCHAR( 50 ) ," .
      "\n`caracteristiques` TEXT," .
      "\nPRIMARY KEY ( `chambre_id` ) ," .
      "\nINDEX ( `service_id` ));";
    db_exec($sql); db_error();
    $sql = "CREATE TABLE `lit` (" .
      "\n`lit_id` INT NOT NULL AUTO_INCREMENT ," .
      "\n`chambre_id` INT NOT NULL," .
      "\n`nom` VARCHAR( 50 ) NOT NULL ," .
      "\nPRIMARY KEY ( `lit_id` ) ," .
      "\nINDEX ( `chambre_id` ));";
    db_exec($sql); db_error();
    
    $this->upgrade("all");
		return null;
	}
}

?>