<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author Romain Ollivier
 */

class CSetupdPboard extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPboard";
    
    $this->makeRevision("all");
    
    $this->mod_version = "0.1";
  }
}
?>