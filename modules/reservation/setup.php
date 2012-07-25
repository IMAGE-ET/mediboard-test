<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CSetupreservation extends CSetup {
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "reservation";
    $this->makeRevision("all");
    
    $this->mod_version = "0.01";    
  }
}
