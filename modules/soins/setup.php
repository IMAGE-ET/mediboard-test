<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision: $
 *  @author Alexis Granger
 */

class CSetupsoins extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = 'soins';

    $this->makeRevision('all');

    $this->mod_version = '0.1';
  }
}

?>