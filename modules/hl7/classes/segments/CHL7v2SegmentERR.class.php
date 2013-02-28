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
  /**
   * @var string
   */
  public $name           = "ERR";
  
  /**
   * @var CHL7v2Acknowledgment
   */
  public $acknowledgment;
  
  /**
   * @var CHL7v2Error
   */
  public $error;

  /**
   * Build ERR segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $version        = $event->message->version;

    $error          = $this->error;
    $acknowledgment = $this->acknowledgment;
    
    $data = array();

    if ($error) {
      // ERR-1: Error Code and Location (ELD) (optional repeating)
      if ($version < "2.5") {
        $data[] = $error->getCodeLocation();
        return $this->fill($data);
      }
      $data[] = null;
      
      // ERR-2: Error Location (ERL) (optional repeating)
      $data[] = array(
        $error->getLocation()
      );
      
      // ERR-3: HL7 Error Code (CWE) 
      $data[] = array(
        $error->getHL7Code()
      );
      
      if ($error->level == CHL7v2Error::E_ERROR) {
        // ERR-4: Severity (ID) 
        // Table - 0516
        // W - Warning - Transaction successful, but there may issues 
        // I - Information - Transaction was successful but includes information e.g., inform patient
        // E - Error - Transaction was unsuccessful 
        $data[] = "E";
        // ERR-5: Application Error Code (CWE) (optional)
        $data[] = array(
          array (
            "E002",
            CAppUI::tr("CHL7Event-AR-E002")
          )
        );
      }
      else {
        $data[] = "W";
        $data[] = array(
           /*array (
             "A002",
             CAppUI::tr("CHL7Event-AA-A002")
           )*/
        );
      }
      
      // ERR-6: Application Error Parameter (ST) (optional repeating)
      $data[] = null;
      
      // ERR-7: Diagnostic Information (TX) (optional)
      $data[] = null;
      
      // ERR-8: User Message (TX) (optional)
      $data[] = CAppUI::tr("CHL7v2Exception-$error->code") . "($error->data)";
      
      // ERR-9: Inform Person Indicator (IS) (optional repeating)
      $data[] = null;
      
      // ERR-10: Override Type (CWE) (optional)
      $data[] = null;
      
      // ERR-11: Override Reason Code (CWE) (optional repeating)
      $data[] = null;
      
      // ERR-12: Help Desk Contact Point (XTN) (optional repeating) 
      $data[] = null;
    }
    else {
      // ERR-1: Error Code and Location (ELD) (optional repeating)
      if ($version < "2.5") {
        $data[] = array(
          array(
            null,
            null,
            null,
            array(
              $acknowledgment->hl7_error_code,
              null,
              null,
              $acknowledgment->_mb_error_code,
              CAppUI::tr("CHL7Event-$acknowledgment->ack_code-$acknowledgment->_mb_error_code")
            )
          )
        );
        return $this->fill($data);
      }
      $data[] = null;
      
      // ERR-2
      $data[] = array(
        array(
          0,
          0
        )
      );
      
      // ERR-3
      $data[] = $acknowledgment->hl7_error_code;
      // ERR-4
      $data[] = $acknowledgment->severity;
      // ERR-5 
      $data[] = array(
          array (
            $acknowledgment->_mb_error_code,
            CAppUI::tr("CHL7Event-$acknowledgment->ack_code-$acknowledgment->_mb_error_code")
          )
      );
      // ERR-6
      $data[] = null;
      // ERR-7
      $data[] = null;
      // ERR-8
      $data[] = $acknowledgment->comments;
      // ERR-9
      $data[] = null;
      // ERR-10
      $data[] = null;
      // ERR-11
      $data[] = null;
      // ERR-12
      $data[] = null;
    }
    
    $this->fill($data);
  }
}