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

class CSetupdPdeveloppement extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPdeveloppement";
    
    $this->makeRevision("all");
    
    $this->mod_version = "0.1";
  }
}
?>