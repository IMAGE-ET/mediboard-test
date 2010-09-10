<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSetuphl7 extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "hl7";
    $this->makeRevision("all");
          
    // Déplacement des requêtes dans le module H'XML     
     
    $this->mod_version = "0.01";
  }
}

?>