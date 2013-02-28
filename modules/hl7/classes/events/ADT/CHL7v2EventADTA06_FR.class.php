<?php

/**
 * A06 - Change an outpatient to an inpatient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA06_FR
 * A06 - Change an outpatient to an inpatient
 */
class CHL7v2EventADTA06_FR extends CHL7v2EventADTA06 {
  /**
   * Construct
   *
   * @param string $i18n i18n
   *
   * @return \CHL7v2EventADTA06_FR
   */
  function __construct($i18n = "FR") {
    parent::__construct($i18n);
  }

  /**
   * Build i18n segements
   *
   * @param CSejour $sejour Admit
   *
   * @see parent::buildI18nSegments()
   *
   * @return void
   */
  function buildI18nSegments($sejour) {
    // Patient Visit - Additionale Info
    $this->addPV2($sejour);
    
    // Movement segment
    $this->addZBE($sejour);
  }
}