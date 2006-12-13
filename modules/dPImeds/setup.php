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

class CSetupdPImeds extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPImeds";
    
    $this->makeRevision("all");
    
    $this->mod_version = "0.1";
  }
}
?>