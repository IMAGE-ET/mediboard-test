<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Sébastien Fillonneau
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPdeveloppement";
$config["mod_version"]     = "0.1";
$config["mod_type"]        = "user";
$config["mod_config"]      = true;

if (@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupdPdeveloppement {
  function configure() {
    global $AppUI;
    $AppUI->redirect("m=system&a=configure");
    return true;
  }

  function upgrade($old_version) {
    switch ($old_version) {
      case "all":
      case "0.1":
        return "0.1";
    }
    return false;
  }
}
?>