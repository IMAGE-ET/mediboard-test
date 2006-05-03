<?php /* $Id: setup.php,v 1.8 2006/04/21 19:40:12 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: 1.8 $
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"] = "dPsalleOp";
$config["mod_version"] = "0.14";
$config["mod_directory"] = "dPsalleOp";
$config["mod_setup_class"] = "CSetupdPsalleOp";
$config["mod_type"] = "user";
$config["mod_ui_name"] = "Salle d'op";
$config["mod_ui_icon"] = "dPsalleOp.png";
$config["mod_description"] = "Gestion des salles d'opération";
$config["mod_config"] = true;

if (@$a == "setup") {
	echo dPshowModuleConfig( $config );
}

class CSetupdPsalleOp {

	function configure() {
	global $AppUI;
		$AppUI->redirect( "m=dPsalleOp&a=configure" );
  		return true;
	}

	function remove() {
    db_exec( "DROP TABLE acte_ccam;" ); db_error();

		return null;
	}

	function upgrade( $old_version ) {
		switch ( $old_version ) {
		case "all":	
		case "0.1":
      $sql = "CREATE TABLE `acte_ccam` (" .
          "\n`acte_id` INT NOT NULL ," .
          "\n`code_activite` VARCHAR( 2 ) NOT NULL ," .
          "\n`code_phase` VARCHAR( 1 ) NOT NULL ," .
          "\n`execution` DATETIME NOT NULL ," .
          "\n`modificateurs` VARCHAR( 4 ) ," .
          "\n`montant_depassement` FLOAT," .
          "\n`commentaire` TEXT," .
          "\n`operation_id` INT NOT NULL ," .
          "\n`executant_id` INT NOT NULL ," .
          "\nPRIMARY KEY ( `acte_id` ))";
      db_exec($sql); db_error();

		case "0.11":
      $sql = "ALTER TABLE `acte_ccam` " .
          "ADD `code_acte` CHAR( 7 ) NOT NULL AFTER `acte_id`";
      db_exec($sql); db_error();
      
      $sql = "ALTER TABLE `acte_ccam` " .
          "ADD UNIQUE (" .
            "`code_acte` ," .
            "`code_activite` ," .
            "`code_phase` ," .
            "`operation_id`)";
      db_exec($sql); db_error();
      
    case "0.12":
      $sql =  "ALTER TABLE `acte_ccam` " .
          "CHANGE `acte_id` `acte_id` INT( 11 ) NOT NULL AUTO_INCREMENT";
      db_exec($sql); db_error();
      
    case "0.13":
      $sql =  "ALTER TABLE `acte_ccam` DROP INDEX `code_acte`";
      db_exec($sql); db_error();
      
		case "0.14":
    	return true;
		}

		return false;
	}

	function install() {
		
		$this->upgrade("all");

		return null;
	}
}

?>