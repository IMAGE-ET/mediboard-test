<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPImeds
 * @version $Revision$
 * @author Romain Ollivier
 */

class CSetupdPImeds extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPImeds";
    
    $this->makeRevision("all");
    
    $this->mod_version = "0.1";
  }
}
?>