<?php

/**
 * A05 - Pre-admit a patient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA05_FR
 * A05 - Pre-admit a patient
 */
class CHL7v2EventADTA05_FR extends CHL7v2EventADTA05 {
  function __construct() {
    parent::__construct();
        
    $this->transaction = CPAMFR::getTransaction($this->code);
  }
  
  function build($sejour) {
    parent::build($sejour);

    // Movement segment
    $this->addZBE($sejour);
  }
  
}

?>