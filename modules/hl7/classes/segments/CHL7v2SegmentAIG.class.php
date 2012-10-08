<?php

/**
 * Represents an HL7 AIG message segment (Appointment Information - General Resource) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentAIG
 * AIG - Represents an HL7 AIG message segment (Appointment Information - General Resource)
 */

class CHL7v2SegmentAIG extends CHL7v2Segment {
  var $name = "AIG";
  
  function build(CHL7v2Event $event) {
    parent::build($event);
        
    $data = array();
    
    // AIG-1: Set ID - AIG (SI)
    $data[] = null;
    
    // AIG-2: Segment Action Code (ID) (optional)
    $data[] = null;
    
    // AIG-3: Resource ID (CE) (optional)
    $data[] = null;
    
    // AIG-4: Resource Type (CE)
    $data[] = null;
    
    // AIG-5: Resource Group (CE) (optional repeating)
    $data[] = null;
    
    // AIG-6: Resource Quantity (NM) (optional)
    $data[] = null;
    
    // AIG-7: Resource Quantity Units (CE) (optional)
    $data[] = null;
    
    // AIG-8: Start Date/Time (TS) (optional)
    $data[] = null;
    
    // AIG-9: Start Date/Time Offset (NM) (optional)
    $data[] = null;
    
    // AIG-10: Start Date/Time Offset Units (CE) (optional)
    $data[] = null;
    
    // AIG-11: Duration (NM) (optional)
    $data[] = null;
    
    // AIG-12: Duration Units (CE) (optional)
    $data[] = null;
    
    // AIG-13: Allow Substitution Code (IS) (optional)
    $data[] = null;
    
    // AIG-14: Filler Status Code (CE) (optional)
    $data[] = null;
    
    $this->fill($data);
  }  
} 