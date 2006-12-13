<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPboard
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$config = array();
$config["mod_name"]        = "dPboard";
$config["mod_version"]     = "0.1";
$config["mod_type"]        = "user";

class CSetupdPboard extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPboard";
    
    $this->makeRevision("all");
    
    $this->mod_version = "0.1";
  }
}
?>