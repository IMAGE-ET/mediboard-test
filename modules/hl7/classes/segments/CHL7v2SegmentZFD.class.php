<?php

/**
 * Represents an HL7 ZFD message segment (Complément démographique) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentZFD
 * ZFD - Represents an HL7 ZFD message segment (Complément démographique)
 */

class CHL7v2SegmentZFD extends CHL7v2Segment {
  /**
   * @var string
   */
  var $name   = "ZFD";
  
  /**
   * @var CPatient
   */
  var $patient = null;

  /**
   * Build ZFD segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $patient = $this->patient;
    
     // ZFD-1: Date lunaire
    if (isLunarDate($patient->naissance)) {
      $date = explode("-", $patient->naissance);
      $data[] = array(
        // ZFD-1.1 : Jour
        $date[2],
        // ZFD-1.2 : Mois
        $date[1],
        // ZFD-1.1 : Année
        $date[0]
      );
    } else {
      $data[] = null;
    }
    
    // ZFD-2: Nombre de semaines de gestation
    $data[] = null;
    
    $this->fill($data);
  }
}

?>