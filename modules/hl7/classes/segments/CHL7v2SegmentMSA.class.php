<?php

/**
 * Represents an HL7 MSA message segment (Message Acknowledgment) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License; see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentMSA 
 * MSA - Represents an HL7 MSA message segment (Message Acknowledgment)
 */

class CHL7v2SegmentMSA extends CHL7v2Segment {
  /**
   * @var string
   */
  public $name           = "MSA";
  
  /**
   * @var CHL7v2Acknowledgment
   */
  public $acknowledgment;

  /**
   * Build MSA segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $acknowledgment = $this->acknowledgment;
    
    $data = array();
    
    // MSA-1: Acknowledgment Code (ID)
    // Table - 0008
    // AA  - Original mode: Application Accept - Enhanced mode: Application acknowledgment: Accept   
    // AE  - Original mode: Application Error - Enhanced mode: Application acknowledgment: Error   
    // AR  - Original mode: Application Reject - Enhanced mode: Application acknowledgment: Reject   
    // CA  - Enhanced mode: Accept acknowledgment: Commit Accept   
    // CE  - Enhanced mode: Accept acknowledgment: Commit Error  
    // CR  - Enhanced mode: Accept acknowledgment: Commit Reject 
    $data[] = $acknowledgment->ack_code; 
    
    // MSA-2: Message Control ID
    $data[] = $acknowledgment->message_control_id; 
    
    // MSA-3: Text Message (ST) (optional)
    $data[] = null;
    
    // MSA-4: Expected Sequence Number (NM) (optional)
    $data[] = null;
    
    // MSA-5: Delayed Acknowledgment Type (ID) (optional)
    $data[] = null;
    
    // MSA-6: Error Condition (CE) (optional)
    $data[] = array(
      $acknowledgment->hl7_error_code
    );
    
    $this->fill($data);
  }
}