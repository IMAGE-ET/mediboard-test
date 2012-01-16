<?php

/**
 * A28 - Add person information - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA28_FR 
 * A28 - Add person information
 */
class CHL7v2EventADTA28_FR extends CHL7v2EventADTA28 {
  function __construct($i18n = "FR") {
    parent::__construct($i18n);
        
    $this->transaction = CPAMFR::getTransaction($this->code);
  }
}

?>