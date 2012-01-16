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
  function __construct() {
    parent::__construct();
    
    $this->profil      = "DEC";
    $this->event_type  = "ORU";
  }
  
  function build($object) {
    parent::build($object);
        
    /* @todo Pas de création de message pour le moment */
  }

}

?>