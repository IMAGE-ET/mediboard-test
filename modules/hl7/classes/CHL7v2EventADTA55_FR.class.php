<?php

/**
 * A55 - Cancel change attending doctor - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA55_FR
 * A55 - Cancel change attending doctor
 */
class CHL7v2EventADTA55_FR extends CHL7v2EventADTA55 {
  function __construct() {
    parent::__construct();
        
    $this->transaction = CPAMFr::getTransaction($this->code);
  }
  
  function build($sejour) {
    parent::build($sejour);
    
    // Movement segment
    $this->addZBE($sejour);
  }
}

?>