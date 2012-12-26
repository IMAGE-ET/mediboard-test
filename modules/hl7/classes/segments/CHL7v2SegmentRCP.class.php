<?php

/**
 * Represents an HL7 RCP message segment (Response Control Parameter) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentRCP
 * RCP - Represents an HL7 RCP message segment (Response Control Parameter)
 */

class CHL7v2SegmentRCP extends CHL7v2Segment {
  var $name   = "RCP";
  
  function build(CHL7v2Event $event) {
    parent::build($event);

    // RCP-1: Query Priority (ID) (optional)
    $data[] = null;
    
    // RCP-2: Quantity Limited Request (CQ) (optional)
    $data[] = null;
    
    // RCP-3: Response Modality (CE) (optional)
    $data[] = null;

    // RCP-4: Execution and Delivery Time (TS) (optional)
    $data[] = null;

    // RCP-5: Modify Indicator (ID) (optional)
    $data[] = null;

    // RCP-6: Sort-by Field (SRT) (optional repeating)
    $data[] = null;

    // RCP-7: Segment group inclusion (ID) (optional repeating)
    $data[] = null;

    $this->fill($data);
  }
}

?>