<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision$
 * @author Sébastien Fillonneau
 */

class CSetupdPdeveloppement extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPdeveloppement";
    
    $this->makeRevision("all");
    
    $this->mod_version = "0.1";
  }
}
?>