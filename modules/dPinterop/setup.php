<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"] = "dPinterop";
$config["mod_version"] = "0.1";
$config["mod_directory"] = "dPinterop";
$config["mod_setup_class"] = "CSetupdPinterop";
$config["mod_type"] = "user";
$config["mod_ui_name"] = "Interop";
$config["mod_ui_icon"] = "dPinterop.png";
$config["mod_description"] = "Module d'interopérabilité pour Mediboard";
$config["mod_config"] = true;

if (@$a == "setup") {
	echo dPshowModuleConfig( $config );
}

class CSetupdPinterop {

	function configure() {
    global $AppUI;
		$AppUI->redirect( "m=dPinterop&a=configure" );
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