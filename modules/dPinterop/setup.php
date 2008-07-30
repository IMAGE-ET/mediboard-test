<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPinterop
 * @version $Revision$
 * @author Romain Ollivier
 */

class CSetupdPinterop extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPinterop";
    
    $this->makeRevision("all");
    
    $this->mod_version = "0.10";
  }
}
?>