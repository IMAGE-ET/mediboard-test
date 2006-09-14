<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPadmissions";
$config["mod_version"]     = "0.1";
$config["mod_type"]        = "user";
$config["mod_config"] = true;

if (@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupdPadmissions {

  function configure() {
    global $AppUI;
    $AppUI->redirect("m=dPadmissions&a=configure");
    return true;
  }

  function remove() {
    return null;
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