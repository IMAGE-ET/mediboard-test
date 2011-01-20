<?php 
/**
 * Setup EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CSetupEAI extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "EAI";
    $this->makeRevision("all");
        
    $this->mod_version = "0.01";
  }
}

?>