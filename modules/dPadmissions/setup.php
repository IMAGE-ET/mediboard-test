<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupdPadmissions extends CSetup {
  
  function __construct() {
    parent::__construct();
  
    $this->mod_name = "dPadmissions";
   
    $this->makeRevision("all");
    
    $this->mod_version = "0.1";
 
  }
}

?>