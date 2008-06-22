<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author Romain Ollivier
 */

class CSetupdPadmissions extends CSetup {
  
  function __construct() {
    parent::__construct();
  
    $this->mod_name = "dPadmissions";
   
    $this->makeRevision("all");
    
    $this->mod_version = "0.1";
 
  }
}

?>