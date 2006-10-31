<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPccam";
$config["mod_version"]     = "0.11";
$config["mod_type"]        = "user";
$config["mod_config"]      = true;

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
        $sql = "CREATE TABLE `ccamfavoris` (
            `favoris_id` bigint(20) NOT NULL auto_increment,
            `favoris_user` int(11) NOT NULL default '0',
            `favoris_code` varchar(7) NOT NULL default '',
            PRIMARY KEY  (`favoris_id`)
            ) TYPE=MyISAM COMMENT='table des favoris'";
        db_exec( $sql );
        db_error();
			case "0.1":
        $sql = "ALTER TABLE `ccamfavoris` " .
               "\nCHANGE `favoris_id` `favoris_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `favoris_user` `favoris_user` int(11) unsigned NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
        
      case "0.11":
				return "0.11";
		}
		return false;
	}
}

?>