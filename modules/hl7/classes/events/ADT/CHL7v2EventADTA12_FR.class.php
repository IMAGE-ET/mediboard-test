<?php

/**
 * A12 - Cancel transfer - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA12_FR
 * A12 - Cancel transfer
 */
class CHL7v2EventADTA12_FR extends CHL7v2EventADTA12 {
  function __construct($i18n = "FR") {
    parent::__construct($i18n);
        
    $this->transaction = CPAMFR::getTransaction($this->code);
  }
  
  function build($sejour) {
    parent::build($sejour);
    
    // Movement segment
    $this->addZBE($sejour);
  }
  
}

?>