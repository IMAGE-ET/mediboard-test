<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * dPdeveloppement Setup class
 */
class CSetupdPdeveloppement extends CSetup {
  /**
   * @see parent::__construct
   */
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPdeveloppement";
    
    $this->makeRevision("all");
    
    $this->mod_version = "0.1";
  }
}
