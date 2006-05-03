<?php /* $Id: setup.php,v 1.5 2006/04/21 19:50:40 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 1.5 $
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'system';
$config['mod_version'] = '1.0.0';
$config['mod_directory'] = 'system';
$config['mod_setup_class'] = 'CSetupSystem';
$config['mod_type'] = 'core';
$config['mod_ui_name'] = 'Administration';
$config['mod_ui_icon'] = '48_my_computer.png';
$config['mod_description'] = 'Administration système';
$config['mod_config'] = true;

if (@$a == 'setup') {
  echo dPshowModuleConfig( $config );
}

class CSetupSystem {

  function configure() {
  global $AppUI;
    $AppUI->redirect( 'm=system&a=configure' );
      return true;
  }

  function remove() {
    return "impossible de supprimer le module 'system'";
  }

  function upgrade( $old_version ) {
    switch ( $old_version ) {
      case "all":
      case "1.0.0":
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