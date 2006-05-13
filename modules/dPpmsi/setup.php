<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'dPpmsi';
$config['mod_version'] = '0.11';
$config['mod_directory'] = 'dPpmsi';
$config['mod_setup_class'] = 'CSetupdPpmsi';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'PMSI';
$config['mod_ui_icon'] = 'dPpmsi.png';
$config['mod_description'] = 'Gestion des actes PMSI';
$config['mod_config'] = true;

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupdPpmsi {

	function configure() {
	global $AppUI;
		$AppUI->redirect( 'm=dPpmsi&a=configure' );
  		return true;
	}

	function remove() {
    db_exec( "DROP TABLE ghm;" ); db_error();
		return null;
	}

	function upgrade( $old_version ) {
		switch ( $old_version )
		{
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
                ) COMMENT = 'Table des GHM';";
        db_exec( $sql ); db_error();
    case "0.11" :
			return true;
		default:
			return false;
		}
		return false;
	}

	function install() {
		
		$this->upgrade("all");

		return null;
	}
}

?>