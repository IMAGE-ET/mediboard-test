<?php

/**
 * A11 - Cancel admit/visit notification - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA11_FR
 * A11 - Cancel admit/visit notification
 */
class CHL7v2EventADTA11_FR extends CHL7v2EventADTA11 {
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