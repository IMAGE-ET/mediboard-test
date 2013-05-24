<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage smp
 * @version $Revision: 10799 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSetupsmp extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "smp";
    $this->makeRevision("all");
      
    $this->mod_version = "0.01";
  }
}
