<?php

/**
 * Represents an HL7 ROL message segment (Role) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentROL 
 * ROL - Represents an HL7 ROL message segment (Role)
 */

class CHL7v2SegmentROL extends CHL7v2Segment {
  var $patient = null;
  var $role_id = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event, "ROL");
        
    $patient  = $this->patient;
    
    $data = array();
        
    // ROL-1: Role Instance ID (EI) (optional)
    $data[] = null;
    
    // ROL-2: Action Code (ID)
    $data[] = null;
     
    // ROL-3: Role-ROL (CE)
    $data[] = null;
     
    // ROL-4: Role Person (XCN) (repeating)
    $data[] = null;
    
    // ROL-5: Role Begin Date/Time (TS) (optional)
    $data[] = null;
    
    // ROL-6: Role End Date/Time (TS) (optional)
    $data[] = null;
    
    // ROL-7: Role Duration (CE) (optional)
    $data[] = null;
    
    // ROL-8: Role Action Reason (CE) (optional)
    $data[] = null;
    
    // ROL-9: Provider Type (CE) (optional repeating)
    $data[] = null;
    
    // ROL-10: Organization Unit Type (CE) (optional)
    $data[] = null;
    
    // ROL-11: Office/Home Address/Birthplace (XAD) (optional repeating)
    $data[] = null;
    
    // ROL-12: Phone (XTN) (optional repeating)
    $data[] = null;
    
    $this->fill($data);
  }
}

?>