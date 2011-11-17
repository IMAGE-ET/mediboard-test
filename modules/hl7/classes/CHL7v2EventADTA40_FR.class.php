<?php

/**
 * A40 - Merge patient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA40_FR
 * A40 - Merge patient
 */
class CHL7v2EventADTA40_FR extends CHL7v2EventADTA40 {
  function __construct() {
    parent::__construct();
        
    $this->transaction = CPAMFR::getTransaction($this->code);
  }
}


?>