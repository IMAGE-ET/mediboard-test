<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$config = array();
$config["mod_name"]        = "dPImeds";
$config["mod_version"]     = "0.1";
$config["mod_type"]        = "user";
$config["mod_config"]      = true;

if (@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupdPImeds {

  function configure() {
    global $AppUI;
    $AppUI->redirect("m=dPImeds&a=configure");
    return true;
  }

  function remove() {
    return null;
  }

  function upgrade($old_version) {
    switch ($old_version) {
      case "all":
        
      case "0.1";
        return "0.1";
    }
    return false;
  }
}

?>