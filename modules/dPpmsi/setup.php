<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

require_once($AppUI->getSystemClass("mbsetup"));

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'dPpmsi';
$config['mod_version'] = '0.12';
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
        if(CMbSetup::getVersionOf("dPplanningOp") < "0.38") {
          return "0.11";
        }
        $sql = "ALTER TABLE `ghm`" .
            "ADD `sejour_id` INT NOT NULL AFTER `operation_id`;";
        db_exec($sql); db_error();
        
        $sql = "UPDATE `ghm`, `operations` SET" .
            "\n`ghm`.`sejour_id` = `operation`.`sejour_id`" .
            "\nWHERE `ghm`.`operation_id` = `operation`.`operation_id`";
        db_exec($sql); db_error();

    case "0.12" :
			return "0.12";
		}
		return false;
	}

	function install() {
		
		$this->upgrade("all");

		return null;
	}
}

?>