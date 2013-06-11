<?php

/**
 * Represents an HL7 ZFP message segment (Situation professionnelle) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentZFP
 * ZFP - Represents an HL7 ZFP message segment (Situation professionnelle)
 */

class CHL7v2SegmentZFP extends CHL7v2Segment {

  /** @var string */
  public $name   = "ZFP";
  

  /** @var CPatient */
  public $patient;

  /**
   * Build ZFP segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $patient = $this->patient;
    
    // ZFP-1: Activité socio-professionnelle (nomemclature INSEE)
    $data[] = $patient->csp ? substr($patient->csp, 0, 1) : null;
    
    // ZFP-2: Catégorie socio-professionnelle (nomemclature INSEE)
    $data[] = $patient->csp;
    
    $this->fill($data);
  }
}