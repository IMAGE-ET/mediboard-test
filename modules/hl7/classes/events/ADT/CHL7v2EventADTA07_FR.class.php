<?php

/**
 * A07 - Change an inpatient to an outpatient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA07_FR
 * A07 - Change an inpatient to an outpatient
 */
class CHL7v2EventADTA07_FR extends CHL7v2EventADTA07 {
  function __construct($i18n = "FR") {
    parent::__construct($i18n);
  }
  
  /**
   * @see parent::buildI18nSegments()
   */
  function buildI18nSegments($sejour) {
    // Patient Visit - Additionale Info
    $this->addPV2($sejour);
    
    // Movement segment
    $this->addZBE($sejour);
  }
  
}

?>