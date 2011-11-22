<?php

/**
 * A44 - Move account information - patient account number - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA44_FR
 * A44 - Move account information - patient account number
 */
class CHL7v2EventADTA44_FR extends CHL7v2EventADTA44 {
  function __construct($i18n = null) {
    parent::__construct($i18n);
        
    $this->transaction = CPAMFR::getTransaction($this->code);
  }
}

?>