<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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