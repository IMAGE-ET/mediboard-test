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

class CSetupdPadmissions extends CSetup {
  
  function __construct() {
    parent::__construct();
  
    $this->mod_name = "dPadmissions";
   
    $this->makeRevision("all");
    
    $this->mod_version = "0.1";
 
  }
}

?>