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
    
    $this->makeRevision("0.1");
    
    $this->addDependency("sip", "0.15");
    
    $this->moveConf("dPinterop hprim_export hostname", "ftp SIP ftphost");
    $this->moveConf("dPinterop hprim_export username", "ftp SIP ftpuser");
    $this->moveConf("dPinterop hprim_export userpass", "ftp SIP ftppass");
    
    $this->moveConf("dPinterop hprim_export fileprefix"   , "sip fileprefix");
    $this->moveConf("dPinterop hprim_export fileextension", "sip fileextension");
    $this->moveConf("dPinterop hprim_export filenbroll"   , "sip filenbroll");
    
    $this->mod_version = "0.11";
  }
}
?>