<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage xds
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * XDS Setup class
 */
class CSetupxds extends CSetup {
  /**
   * @see parent::__construct()
   */
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "xds";
    $this->makeRevision("all");
    
    $this->mod_version = "0.01";    
  }
}
