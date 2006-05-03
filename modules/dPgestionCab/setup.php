<?php /* $Id: setup.php,v 1.1 2006/04/05 00:02:41 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision: 1.1 $
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'dPgestionCab';
$config['mod_version'] = '0.1';
$config['mod_directory'] = 'dPgestionCab';
$config['mod_setup_class'] = 'CSetupdPgestionCab';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Gestion Cab.';
$config['mod_ui_icon'] = 'dPgestionCab.png';
$config['mod_description'] = 'Gestion comptable de cabinet';
$config['mod_config'] = true;

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupdPgestionCab {

	function configure() {
	global $AppUI;
		$AppUI->redirect( 'm=dPgestionCab&a=configure' );
  		return true;
	}

	function remove() {
    $sql = "DROP TABLE `gestioncab`;";
    db_exec( $sql ); db_error();
    $sql = "DROP TABLE `rubrique_gestioncab`;";
    db_exec( $sql ); db_error();
    $sql = "DROP TABLE `mode_paiement`;";
    db_exec( $sql ); db_error();
		return null;
	}

	function upgrade( $old_version ) {
		switch ( $old_version ) {
		case "all":
		case "0.1":
			return true;
		default:
			return false;
		}
		return false;
	}

	function install() {
    $sql = "CREATE TABLE `gestioncab` (
              `gestioncab_id` INT NOT NULL AUTO_INCREMENT ,
              `function_id` INT NOT NULL ,
              `libelle` VARCHAR( 50 ) DEFAULT 'inconnu' NOT NULL ,
              `date` DATE NOT NULL ,
              `rubrique_id` INT DEFAULT '0' NOT NULL ,
              `montant` FLOAT DEFAULT '0' NOT NULL ,
              `mode_paiement_id` INT DEFAULT '0' NOT NULL ,
              `rques` TEXT,
              PRIMARY KEY ( `gestioncab_id` ) ,
              INDEX ( `function_id` , `rubrique_id` , `mode_paiement_id` )
            ) COMMENT = 'Table des lignes de la comptabilité de cabinet';";
    db_exec( $sql ); db_error();
    $sql = "CREATE TABLE `rubrique_gestioncab` (
              `rubrique_id` INT NOT NULL AUTO_INCREMENT ,
              `function_id` INT DEFAULT '0' NOT NULL ,
              `nom` VARCHAR( 30 ) DEFAULT 'divers' NOT NULL ,
              PRIMARY KEY ( `rubrique_id` ) ,
              INDEX ( `function_id` )
            ) COMMENT = 'Table des rubriques pour la gestion comptable de cabinet';";
    db_exec( $sql ); db_error();
    $sql = "INSERT INTO `rubrique_gestioncab` ( `rubrique_id` , `function_id` , `nom` )
            VALUES ('', '0', 'divers');";
    db_exec( $sql ); db_error();
    $sql = "CREATE TABLE `mode_paiement` (
              `mode_paiement_id` INT NOT NULL AUTO_INCREMENT ,
              `function_id` INT DEFAULT '0' NOT NULL ,
              `nom` VARCHAR( 30 ) DEFAULT 'inconnu' NOT NULL ,
              PRIMARY KEY ( `mode_paiement_id` ) ,
              INDEX ( `function_id` )
            ) COMMENT = 'Table des modes de règlement';";
    db_exec( $sql ); db_error();
    $sql = "INSERT INTO `mode_paiement` ( `mode_paiement_id` , `function_id` , `nom` )
            VALUES ('', '0', 'Chèque');";
    db_exec( $sql ); db_error();
    $sql = "INSERT INTO `mode_paiement` ( `mode_paiement_id` , `function_id` , `nom` )
            VALUES ('', '0', 'CB');";
    db_exec( $sql ); db_error();
    $sql = "INSERT INTO `mode_paiement` ( `mode_paiement_id` , `function_id` , `nom` )
            VALUES ('', '0', 'Virement');";
    db_exec( $sql ); db_error();
		
		$this->upgrade("all");

		return null;
	}
}

?>