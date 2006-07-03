<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"] = "dPccam";
$config["mod_version"] = "0.1";
$config["mod_directory"] = "dPccam";
$config["mod_setup_class"] = "CSetupdPccam";
$config["mod_type"] = "user";
$config["mod_ui_name"] = "CCAM";
$config["mod_ui_icon"] = "dPccam.png";
$config["mod_description"] = "Aide au codage CCAM";
$config["mod_config"] = true;

if (@$a == "setup") {
	echo dPshowModuleConfig($config);
}

class CSetupdPccam {

	function configure() {
		global $AppUI;
		$AppUI->redirect( "m=dPccam&a=configure" );
		return true;
	}

	function remove() {
		db_exec( "DROP TABLE ccamfavoris;" );
		return null;
	}


	function upgrade( $old_version ) {
		switch ( $old_version ) {
			case "all":
			case "0.1":
				return "0.1";
		}
		return false;
	}

	function install() {
		$sql = "CREATE TABLE `ccamfavoris` (
				`favoris_id` bigint(20) NOT NULL auto_increment,
				`favoris_user` int(11) NOT NULL default '0',
				`favoris_code` varchar(7) NOT NULL default '',
				PRIMARY KEY  (`favoris_id`)
				) TYPE=MyISAM COMMENT='table des favoris'";
		db_exec( $sql );
		db_error();
		$this->upgrade("all");
		
		return null;
	}

}

?>