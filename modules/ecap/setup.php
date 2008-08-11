<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision: $
 * @author Thomas Despoix
 */

class CSetupecap extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "ecap";
    
    $this->makeRevision("all");
        
    $this->mod_version = "0.01";
  }  
}

?>