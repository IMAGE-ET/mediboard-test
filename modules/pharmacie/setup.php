<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author Fabien Ménager
 */

class CSetuppharmacie extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = 'pharmacie';

    $this->makeRevision('all');

//    $sql = '';
//    $this->addQuery($sql);
    
    $this->mod_version = '0.1';
  }
}

?>