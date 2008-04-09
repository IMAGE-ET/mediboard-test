<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI;

// MODULE CONFIGURATION
// redundant now but mandatory until end of refactoring
$config = array();
$config['mod_name']    = 'pharmacie';
$config['mod_version'] = '0.1';
$config['mod_type']    = 'user';

class CSetuppharmacie extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = 'pharmacie';
    $this->makeRevision('all');

    /*$sql = '';
    $this->addQuery($sql);*/
    
    $this->mod_version = '0.1';
  }
}

?>