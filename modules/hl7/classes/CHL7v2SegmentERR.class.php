<?php

/**
 * Represents an HL7 ERR message segment (Error) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License; see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentERR 
 * ERR - Represents an HL7 ERR message segment (Error)
 */

class CHL7v2SegmentERR extends CHL7v2Segment {
  var $name           = "ERR";
  var $acknowledgment = null;
  var $error          = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $error          = $this->error;
    $acknowledgment = $this->acknowledgment;
    
    $data = array();
    
    // ERR-1: Error Code and Location (ELD) (optional repeating)
    $data[] = null; 
    
    if ($error) {
      // ERR-2: Error Location (ERL) (optional repeating)
      $data[] = $error->getLocation();
      // ERR-3: HL7 Error Code (CWE) 
      $data[] = $error->code;
      // ERR-4: Severity (ID) 
      // Table - 0516
      // W - Warning - Transaction successful, but there may issues 
      // I - Information - Transaction was successful but includes information e.g., inform patient
      // E - Error - Transaction was unsuccessful 
      $data[] = ($error->level == CHL7v2Error::E_ERROR) ? "E" : "W"; 
    } else {
      $data[] = null;
      $data[] = $acknowledgment->hl7_error_code;
      $data[] = $acknowledgment->severity; 
    }
    
    // ERR-5: Application Error Code (CWE) (optional)
    $data[] = array(
      $acknowledgment->mb_error_code,
      utf8_encode(CAppUI::tr("CHL7EventADT-$acknowledgment->ack_code-$acknowledgment->mb_error_code"))
    ); 
    
    // ERR-6: Application Error Parameter (ST) (optional repeating)
    $data[] = null; 
    
    // ERR-7: Diagnostic Information (TX) (optional)
    $data[] = null; 
    
    // ERR-8: User Message (TX) (optional)
    $data[] = utf8_encode($acknowledgment->comments); 
    
    // ERR-9: Inform Person Indicator (IS) (optional repeating)
    $data[] = null; 
    
    // ERR-10: Override Type (CWE) (optional)
    $data[] = null; 
    
    // ERR-11: Override Reason Code (CWE) (optional repeating)
    $data[] = null; 
    
    // ERR-12: Help Desk Contact Point (XTN) (optional repeating) 
    $data[] = null; 
    
    $this->fill($data);
  }
}
?>