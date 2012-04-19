<?php

/**
 * Represents an HL7 PID message segment (Patient Identification) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentPID 
 * PID - Represents an HL7 PID message segment (Patient Identification)
 */

class CHL7v2SegmentPID_FR extends CHL7v2SegmentPID {
  function fillOtherIdentifiers(&$identifiers, CPatient $patient) {
    if ($patient->INSC) {
      $identifiers[] = array(
        $patient->INSC,
        null,
        null,
        // PID-3-4 Autorité d'affectation
        $this->getAssigningAuthority("INS-C"),
        "INS-C",
        null,
        mbDate($patient->INSC_date)
      );
    }
    
    $identifiers[] = array(
      $patient->_id,
      null,
      null,
      // PID-3-4 Autorité d'affectation
      $this->getAssigningAuthority("mediboard"),
      "RI"
    );
  }
}
