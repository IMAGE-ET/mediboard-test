<?php

/**
 * Represents an HL7 ZBE message segment (Movement) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentZBE_FR
 * ZBE - Represents an HL7 ZBE message segment (Movement)
 */

class CHL7v2SegmentZBE_FR extends CHL7v2SegmentZBE {
  /**
   * Fill other identifiers
   *
   * @param array       &$data Datas
   * @param array       $ufs   UF
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function fillOtherSegments(&$data, $ufs = array(), CHL7v2Event $event) {    
    // ZBE-8: Ward of care responsibility in the period starting with this movement (XON) (optional)
    $uf_soins = isset($ufs["soins"]) ? $ufs["soins"] : null;
    if (isset($uf_soins->_id)) {
      $data[] = array(
        array(
          // ZBE-8.1 : Libellé de l'UF
          $uf_soins->libelle,
          null,
          null,
          null,
          null,
          // ZBE-8.6 : Identifiant de l'autorité d'affectation  qui a attribué l'identifiant de l'UF de responsabilité médicale
          $this->getAssigningAuthority("mediboard"),
          // ZBE-8.7 : La seule valeur utilisable de la table 203 est "UF"
          "UF",
          null,
          null,
          // ZBE-8.10 : Identifiant de l'UF de responsabilité médicale
          $uf_soins->code
        )
      );
    }
    else {
      $data[] = null;
    }
    
    // ZBE-9: Nature of this movement (CWE)
    // S - Changement de responsabilité de soins uniquement
    // H - Changement de  responsabilité  d'hébergement  soins uniquement
    // M - Changement de responsabilité médicale uniquement
    // L - Changement de lit uniquement
    // D - Changement de prise en charge médico-administrative laissant les responsabilités et la localisation du patient inchangées 
    //     (ex : changement de tarif du séjour en unité de soins)
    // SM - Changement de responsabilité soins + médicale
    // SH - Changement de responsabilité soins + hébergement
    // MH - Changement de responsabilité hébergement + médicale
    // LD - Changement de prise en charge médico-administrative et de lit, laissant les responsabilités inchangées
    // HMS - Changement conjoint des trois responsabilités.
    /* @todo Voir comment gérer ceci... */
   
    // Changement d'UF médicale
    if (CMbArray::in($event->code, "Z80 Z81 Z82 Z83")) {
      $data[] = "M";
    }
    // Changement d'UF de soins
    elseif (CMbArray::in($event->code, "Z84 Z85 Z86 Z87")) {
      $data[] = "S";
    }
    else {
      $data[] = "HMS";
    }
  }
}