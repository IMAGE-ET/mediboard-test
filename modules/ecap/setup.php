<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupecap extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "ecap";
    
    $this->makeRevision("all");
        
    $this->mod_version = "0.01";
  }  
}

?>