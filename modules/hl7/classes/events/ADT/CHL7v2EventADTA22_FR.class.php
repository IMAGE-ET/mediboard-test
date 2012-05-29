<?php

/**
 * A22 - Patient returns from a _leave of absence_ - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA22_FR
 * A22 - Patient returns from a _leave of absence_
 */
class CHL7v2EventADTA22_FR extends CHL7v2EventADTA22 {
  function __construct($i18n = "FR") {
    parent::__construct($i18n);
  }
  
  /**
   * @see parent::buildI18nSegments()
   */
  function buildI18nSegments($sejour) {
    
    // Movement segment
    $this->addZBE($sejour);
    
    // Mouvement PMSI
    $this->addZFM($sejour);
  }
}

?>