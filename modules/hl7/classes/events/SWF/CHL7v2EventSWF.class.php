<?php

/**
 * Scheduled Workflow HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Classe CHL7v2EventSWF 
 * Scheduled Workflow
 */
class CHL7v2EventSWF extends CHL7v2Event implements CHL7EventSWF {
  var $event_type = "ORU";
  
  function __construct() {
    parent::__construct();
    
    $this->profil    = "SWF";
    $this->msg_codes = array ( 
      array(
        $this->event_type, $this->code
      )
    );
    $this->transaction = CIHE::getSWFTransaction($this->code, $i18n);
  }
  
  /**
   * @see parent::build()
   */
  function build($object) {
    parent::build($object);
        
    
  }

}

?>