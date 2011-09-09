<?php

/**
 * Represents an HL7 PV1 message segment (Patient Visit) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentPV1 
 * PV1 - Represents an HL7 PV1 message segment (Patient Visit)
 */

class CHL7v2SegmentPV1 extends CHL7v2Segment {
  var $sejour  = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event, "PID");
    
    $message  = $event->message;
    $receiver = $event->_receiver;
    $group    = $receiver->_ref_group;
    
    $sejour  = $this->sejour;
    
    $data = array();

    // PV1-1: Set ID - PV1 (SI) (optional)
    $data[] = null;
    
    // PV1-2: Patient Class (IS)
    // Table - 0002
    // E - Emergency - Passage aux Urgences - Arrivée aux urgences
    // I - Inpatient - Hospitalisation
    // N - Not Applicable - Non applicable - 
    // O - Outpatient - Actes et consultation externe
    // R - Recurring patient - Séances
    if (!$sejour) {
      $data[] = "N";
    } else {
      $data[] = null;
    }
    
    // PV1-3: Assigned Patient Location (PL) (optional)
    $data[] = null;
    
    // PV1-4: Admission Type (IS) (optional)
    $data[] = null;
    
    // PV1-5: Preadmit Number (CX) (optional)
    $data[] = null;
    
    // PV1-6: Prior Patient Location (PL) (optional)
    $data[] = null;
    
    // PV1-7: Attending Doctor (XCN) (optional repeating)
    $data[] = null;
    
    // PV1-8: Referring Doctor (XCN) (optional repeating)
    $data[] = null;
    
    // PV1-9: Consulting Doctor (XCN) (optional repeating)
    $data[] = null;
    
    // PV1-10: Hospital Service (IS) (optional)
    $data[] = null;
    
    // PV1-11: Temporary Location (PL) (optional)
    $data[] = null;
    
    // PV1-12: Preadmit Test Indicator (IS) (optional)
    $data[] = null;
    
    // PV1-13: Re-admission Indicator (IS) (optional)
    $data[] = null;
    
    // PV1-14: Admit Source (IS) (optional)
    $data[] = null;
    
    // PV1-15: Ambulatory Status (IS) (optional repeating)
    $data[] = null;
    
    // PV1-16: VIP Indicator (IS) (optional)
    $data[] = null;
    
    // PV1-17: Admitting Doctor (XCN) (optional repeating)
    $data[] = null;
    
    // PV1-18: Patient Type (IS) (optional)
    $data[] = null;
    
    // PV1-19: Visit Number (CX) (optional)
    $data[] = null;
    
    // PV1-20: Financial Class (FC) (optional repeating)
    $data[] = null;
    
    // PV1-21: Charge Price Indicator (IS) (optional)
    $data[] = null;
    
    // PV1-22: Courtesy Code (IS) (optional)
    $data[] = null;
    
    // PV1-23: Credit Rating (IS) (optional)
    $data[] = null;
    
    // PV1-24: Contract Code (IS) (optional repeating)
    $data[] = null;
    
    // PV1-25: Contract Effective Date (DT) (optional repeating)
    $data[] = null;
    
    // PV1-26: Contract Amount (NM) (optional repeating)
    $data[] = null;
    
    // PV1-27: Contract Period (NM) (optional repeating)
    $data[] = null;
    
    // PV1-28: Interest Code (IS) (optional)
    $data[] = null;
    
    // PV1-29: Transfer to Bad Debt Code (IS) (optional)
    $data[] = null;
    
    // PV1-30: Transfer to Bad Debt Date (DT) (optional)
    $data[] = null;
    
    // PV1-31: Bad Debt Agency Code (IS) (optional)
    $data[] = null;
    
    // PV1-32: Bad Debt Transfer Amount (NM) (optional)
    $data[] = null;
    
    // PV1-33: Bad Debt Recovery Amount (NM) (optional)
    $data[] = null;
    
    // PV1-34: Delete Account Indicator (IS) (optional)
    $data[] = null;
    
    // PV1-35: Delete Account Date (DT) (optional)
    $data[] = null;
    
    // PV1-36: Discharge Disposition (IS) (optional)
    $data[] = null;
    
    // PV1-37: Discharged to Location (DLD) (optional)
    $data[] = null;
    
    // PV1-38: Diet Type (CE) (optional)
    $data[] = null;
    
    // PV1-39: Servicing Facility (IS) (optional)
    $data[] = null;
    
    // PV1-40: Bed Status (IS) (optional)
    $data[] = null;
    
    // PV1-41: Account Status (IS) (optional)
    $data[] = null;
    
    // PV1-42: Pending Location (PL) (optional)
    $data[] = null;
    
    // PV1-43: Prior Temporary Location (PL) (optional)
    $data[] = null;
    
    // PV1-44: Admit Date/Time (TS) (optional)
    $data[] = null;
    
    // PV1-45: Discharge Date/Time (TS) (optional repeating)
    $data[] = null;
    
    // PV1-46: Current Patient Balance (NM) (optional)
    $data[] = null;
    
    // PV1-47: Total Charges (NM) (optional)
    $data[] = null;
    
    // PV1-48: Total Adjustments (NM) (optional)
    $data[] = null;
    
    // PV1-49: Total Payments (NM) (optional)
    $data[] = null;
    
    // PV1-50: Alternate Visit ID (CX) (optional)
    $data[] = null;
    
    // PV1-51: Visit Indicator (IS) (optional)
    $data[] = null;
    
    // PV1-52: Other Healthcare Provider (XCN) (optional repeating)
    $data[] = null;
    
    $this->fill($data);
  }
}

?>