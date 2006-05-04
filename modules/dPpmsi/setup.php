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
$config['mod_version'] = '0.1';
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

		return null;
	}

	function upgrade( $old_version ) {
		switch ( $old_version )
		{
		case "all":
		case "0.1":
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