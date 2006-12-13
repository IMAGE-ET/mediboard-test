<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPinterop";
$config["mod_version"]     = "0.1";
$config["mod_type"]        = "user";

class CSetupdPinterop extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPinterop";
    
    $this->makeRevision("all");
    
    $this->mod_version = "0.1";
  }
}
?>