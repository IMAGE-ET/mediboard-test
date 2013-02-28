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
  /**
   * Construct
   *
   * @param string $i18n i18n
   *
   * @return \CHL7v2EventADTA05_FR
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
    // Movement segment
    $this->addZBE($sejour);
    
    // Situation professionnelle
    $this->addZFP($sejour);
    
    // Compléments sur la rencontre
    $this->addZFV($sejour);
    
    // Mouvement PMSI
    $this->addZFM($sejour);
    
    // Complément démographique
    $this->addZFD($sejour);
  }
}