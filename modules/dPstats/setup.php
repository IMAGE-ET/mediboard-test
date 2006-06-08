<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'dPstats';
$config['mod_version'] = '0.1';
$config['mod_directory'] = 'dPstats';
$config['mod_setup_class'] = 'CSetupdPstats';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Stats';
$config['mod_ui_icon'] = 'dPstats.png';
$config['mod_description'] = 'Reporting';
$config['mod_config'] = true;

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupdPstats {

	function configure() {
	global $AppUI;
		$AppUI->redirect( 'm=dPstats&a=configure' );
  		return true;
	}

	function remove() {

		return null;
	}

	function upgrade( $old_version ) {
		switch ( $old_version )
		{
		case "all":
		case "0.1":
			return "0.1";
		}
		return false;
	}

	function install() {
		
		$this->upgrade("all");

		return null;
	}
}

?>