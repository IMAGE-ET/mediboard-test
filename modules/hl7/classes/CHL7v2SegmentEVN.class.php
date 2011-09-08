<?php

/**
 * Represents an HL7 EVN message segment (Event Type) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentEVN 
 * EVN - Represents an HL7 EVN message segment (Event Type)
 */

class CHL7v2SegmentEVN extends CHL7v2Segment {
  var $planned_datetime = null;
  var $occured_datetime = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event, "EVN");

    $data = array();
    
    // EVN-1: Event Type Code (ID) (optional)
    $data[] = $event->code;
    
    // EVN-2: Recorded Date/Time (TS)
    $data[] = mbDateTime();
    
    // EVN-3: Date/Time Planned Event (TS)(optional)
    $data[] = $this->planned_datetime;
    
    // EVN-4: Event Reason Code (IS) (optional)
    // Table 062
    // 01 - Patient request
    // 02 - Physician/health practitioner order 
    // 03 - Census management
    // O  - Other 
    // U  - Unknown
    $data[] = null;
    
    // EVN-5: Operator ID (XCN) (optional repeating)
    $data[] = $event->last_log->loadRefUser();
    
    // EVN-6: Event Occurred (TS) (optional)
    $data[] = $this->occured_datetime;
    
    // EVN-7: Event Facility (HD) (optional)
    $data[] = null;
    
    $this->fill($data);
  }
}
?>