<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage importTools
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Outils d\'import Setup class
 */
class CSetupimportTools extends CSetup {
  /**
   * @see parent::__construct()
   */
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "importTools";
    $this->makeRevision("all");
    
    $this->mod_version = "0.01";    
  }
}
