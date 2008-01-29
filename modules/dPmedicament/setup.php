<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $AppUI;
 
// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPmedicament";
$config["mod_version"]     = "0.1";
$config["mod_type"]        = "user";


class CSetupdPmedicament extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPmedicament";
       
    $this->makeRevision("all");
    
    $this->mod_version = "0.1";
  }  
}

?>