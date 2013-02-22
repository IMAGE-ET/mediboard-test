<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage CDA
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CSetupcda extends CSetup {
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "cda";
    $this->makeRevision("all");
    
    $this->mod_version = "0.01";    
  }
}
