<?php

/**
 * Represents an HL7 OBX message segment (Observation/Result) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentOBX
 * OBX - Represents an HL7 OBX message segment (Complément d'information sur la venue)
 */

class CHL7v2SegmentOBX extends CHL7v2Segment {
  /**
   * @var string
   */
  public $name   = "OBX";

  /**
   * Build OBX segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    // OBX-1: Set ID - OBX (SI) (optional)
    $data[] = null;
    
    // OBX-2: Value Type (ID) (optional)
    $data[] = null;
    
    // OBX-3: Observation Identifier (CE)
    $data[] = null;
    
    // OBX-4: Observation Sub-ID (ST) (optional)
    $data[] = null;
    
    // OBX-5: Observation Value (Varies) (optional repeating)
    $data[] = null;
    
    // OBX-6: Units (CE) (optional)
    $data[] = null;
    
    // OBX-7: References Range (ST) (optional)
    $data[] = null;
    
    // OBX-8: Abnormal Flags (IS) (optional repeating)
    $data[] = null;
    
    // OBX-9: Probability (NM) (optional)
    $data[] = null;
    
    // OBX-10: Nature of Abnormal Test (ID) (optional repeating)
    $data[] = null;
    
    // OBX-11: Observation Result Status (ID)
    $data[] = null;
     
    // OBX-12: Effective Date of Reference Range (TS) (optional)
    $data[] = null;
    
    // OBX-13: User Defined Access Checks (ST) (optional)
    $data[] = null;
    
    // OBX-14: Date/Time of the Observation (TS) (optional)
    $data[] = null;
    
    // OBX-15: Producer's ID (CE) (optional)
    $data[] = null;
    
    // OBX-16: Responsible Observer (XCN) (optional repeating)
    $data[] = null;
    
    // OBX-17: Observation Method (CE) (optional repeating)
    $data[] = null;
    
    // OBX-18: Equipment Instance Identifier (EI) (optional repeating)
    $data[] = null;
    
    // OBX-19: Date/Time of the Analysis (TS) (optional)
    $data[] = null;
    
    $this->fill($data);
  }
}