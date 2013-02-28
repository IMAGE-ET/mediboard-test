<?php

/**
 * Z99 - Change admit - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTZ99_FR
 * Z99 - Change admit
 */
class CHL7v2EventADTZ99_FR  extends CHL7v2EventADTZ99 {
  /**
   * Construct
   *
   * @param string $i18n i18n
   *
   * @return \CHL7v2EventADTZ99_FR
   */
  function __construct($i18n = "FR") {
    parent::__construct($i18n);
  }

  /**
   * Build i18n segements
   *
   * @param CMbObject $object Object
   *
   * @see parent::buildI18nSegments()
   *
   * @return void
   */
  function buildI18nSegments($object) {
    if ($object instanceof CAffectation) {
      /** @var CSejour $sejour */
      $sejour                       = $object->_ref_sejour;
      $sejour->_ref_hl7_affectation = $object;
    }
    else {
      $sejour = $object;
    }

    // Movement segment
    $this->addZBE($sejour);
    
    // Situation professionnelle
    // Si A01, A04, A05, A14
    $this->addZFP($sejour);
    
    // Compléments sur la rencontre
    // Si A01, A02, A03, A04, A05, A14, A21
    $this->addZFV($sejour);
    
    // Mouvement PMSI
    // Si A01, A02, A03, A04, A05, A14, 
    // Z80, Z81, Z82, Z83, Z84, Z85, Z86, Z87 
    $this->addZFM($sejour);
    
    // Complément démographique
    // Si A01, A04, A05, A14
    $this->addZFD($sejour);
  }
}