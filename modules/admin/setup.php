<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'admin';
$config['mod_version'] = '1.0.1';
$config['mod_directory'] = 'admin';
$config['mod_setup_class'] = 'CSetupAdmin';
$config['mod_type'] = 'core';
$config['mod_ui_name'] = 'Utilisateurs';
$config['mod_ui_icon'] = 'admin.png';
$config['mod_description'] = 'Gestion des Utilisateurs';
$config['mod_config'] = false;

if (@$a == 'setup') {
  echo dPshowModuleConfig( $config );
}

class CSetupAdmin {

  function configure() {
  global $AppUI;
    $AppUI->redirect( 'm=admin&a=configure' );
      return true;
  }

  function remove() {
    return "Impossible de supprimer le module 'admin'";
  }

  function upgrade( $old_version ) {
    switch ( $old_version ) {
      case "all":
      case "1.0.0":
        $sql = "ALTER TABLE `users` CHANGE `user_address1` `user_address1` VARCHAR( 50 );";
        db_exec( $sql ); db_error();
      case "1.0.1":
        return "1.0.1";
    }
    

    return false;
  }

  function install() {
    $this->upgrade("all");
    return null;
  }
}

?>