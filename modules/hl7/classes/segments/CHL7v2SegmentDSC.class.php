<?php

/**
 * Represents an HL7 DSC message segment (Continuation Pointer) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentDSC
 * DSC - Represents an HL7 DSC message segment (Continuation Pointer)
 */

class CHL7v2SegmentDSC extends CHL7v2Segment {

  /** @var string */
  public $name    = "DSC";


  /** @var CPatient */
  public $patient;

  /**
   * Build DSC segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    $patient = $this->patient;

    // DSC-1: Continuation Pointer (ST) (optional)
    $data[] = $patient->_pointer;
    
    // DSC-2: Continuation Style (ID) (optional)
    $data[] =  "I";

    $this->fill($data);
  }
}