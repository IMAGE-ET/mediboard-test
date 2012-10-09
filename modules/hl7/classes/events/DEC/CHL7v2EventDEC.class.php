<?php

/**
 * Device Enterprise Communication HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Classe CHL7v2EventDEC 
 * Device Enterprise Communication
 */
class CHL7v2EventDEC extends CHL7v2Event implements CHL7EventDEC {
  var $event_type = "ORU";
  
  function __construct() {
    parent::__construct();
    
    $this->profil      = "DEC";
    $this->msg_codes   = array ( 
      array(
        $this->event_type, $this->code
      )
    );
    $this->transaction = CIHE::getDECTransaction($this->code);
  }
  
  /**
   * @see parent::build()
   */
  function build($object) {
    parent::build($object);
        
    /* @todo Pas de création de message pour le moment */
  }

}

?>