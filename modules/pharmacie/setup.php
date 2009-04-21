<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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