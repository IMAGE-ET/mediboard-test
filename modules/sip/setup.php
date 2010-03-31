<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSetupsip extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "sip";
    $this->makeRevision("all");
      
    $this->makeRevision("0.11");
      
    // Déplacement des requêtes dans le module H'XML     
     
    $this->mod_version = "0.23.1";
  }
}
?>