<?php

/**
 * Represents an HL7 IN1 message segment (Insurance) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentIN1 
 * IN1 - Represents an HL7 IN1 message segment (Insurance)
 */

class CHL7v2SegmentIN1 extends CHL7v2Segment {
  /**
   * @var string
   */
  public $name    = "IN1";
  
  /**
   * @var CPatient
   */
  public $patient;

  /**
   * Build IN1 segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $data = array();
    
    // IN1-1: Set ID - IN1 (SI) 
    $data[] = null;
    
    // IN1-2: Insurance Plan ID (CE) 
    $data[] = null;
    
    // IN1-3: Insurance Company ID (CX) (repeating) 
    $data[] = null;
    
    // IN1-4: Insurance Company Name (XON) (optional repeating) 
    $data[] = null;
    
    // IN1-5: Insurance Company Address (XAD) (optional repeating) 
    $data[] = null;
    
    // IN1-6: Insurance Co Contact Person (XPN) (optional repeating) 
    $data[] = null;
    
    // IN1-7: Insurance Co Phone Number (XTN) (optional repeating) 
    $data[] = null;
    
    // IN1-8: Group Number (ST) (optional) 
    $data[] = null;
    
    // IN1-9: Group Name (XON) (optional repeating) 
    $data[] = null;
    
    // IN1-10: Insured's Group Emp ID (CX) (optional repeating) 
    $data[] = null;
    
    // IN1-11: Insured's Group Emp Name (XON) (optional repeating) 
    $data[] = null;
    
    // IN1-12: Plan Effective Date (DT) (optional) 
    $data[] = null;
    
    // IN1-13: Plan Expiration Date (DT) (optional) 
    $data[] = null;
    
    // IN1-14: Authorization Information (AUI) (optional) 
    $data[] = null;
    
    // IN1-15: Plan Type (IS) (optional) 
    $data[] = null;
    
    // IN1-16: Name Of Insured (XPN) (optional repeating) 
    $data[] = null;
    
    // IN1-17: Insured's Relationship To Patient (CE) (optional) 
    $data[] = null;
    
    // IN1-18: Insured's Date Of Birth (TS) (optional) 
    $data[] = null;
    
    // IN1-19: Insured's Address (XAD) (optional repeating) 
    $data[] = null;
    
    // IN1-20: Assignment Of Benefits (IS) (optional) 
    $data[] = null;
    
    // IN1-21: Coordination Of Benefits (IS) (optional) 
    $data[] = null;
    
    // IN1-22: Coord Of Ben. Priority (ST) (optional) 
    $data[] = null;
    
    // IN1-23: Notice Of Admission Flag (ID) (optional) 
    $data[] = null;
    
    // IN1-24: Notice Of Admission Date (DT) (optional) 
    $data[] = null;
    
    // IN1-25: Report Of Eligibility Flag (ID) (optional) 
    $data[] = null;
    
    // IN1-26: Report Of Eligibility Date (DT) (optional) 
    $data[] = null;
    
    // IN1-27: Release Information Code (IS) (optional) 
    $data[] = null;
    
    // IN1-28: Pre-Admit Cert (PAC) (ST) (optional) 
    $data[] = null;
    
    // IN1-29: Verification Date/Time (TS) (optional) 
    $data[] = null;
    
    // IN1-30: Verification By (XCN) (optional repeating) 
    $data[] = null;
    
    // IN1-31: Type Of Agreement Code (IS) (optional) 
    $data[] = null;
    
    // IN1-32: Billing Status (IS) (optional) 
    $data[] = null;
    
    // IN1-33: Lifetime Reserve Days (NM) (optional) 
    $data[] = null;
    
    // IN1-34: Delay Before L.R. Day (NM) (optional) 
    $data[] = null;
    
    // IN1-35: Company Plan Code (IS) (optional) 
    $data[] = null;
    
    // IN1-36: Policy Number (ST) (optional) 
    $data[] = null;
    
    // IN1-37: Policy Deductible (CP) (optional) 
    $data[] = null;
    
    // IN1-38: Policy Limit - Amount (CP) (optional) 
    $data[] = null;
    
    // IN1-39: Policy Limit - Days (NM) (optional) 
    $data[] = null;
    
    // IN1-40: Room Rate - Semi-Private (CP) (optional) 
    $data[] = null;
    
    // IN1-41: Room Rate - Private (CP) (optional) 
    $data[] = null;
    
    // IN1-42: Insured's Employment Status (CE) (optional) 
    $data[] = null;
    
    // IN1-43: Insured's Administrative Sex (IS) (optional) 
    $data[] = null;
    
    // IN1-44: Insured's Employer's Address (XAD) (optional repeating) 
    $data[] = null;
    
    // IN1-45: Verification Status (ST) (optional) 
    $data[] = null;
    
    // IN1-46: Prior Insurance Plan ID (IS) (optional) 
    $data[] = null;
    
    // IN1-47: Coverage Type (IS) (optional) 
    $data[] = null;
    
    // IN1-48: Handicap (IS) (optional) 
    $data[] = null;
    
    // IN1-49: Insured's ID Number (CX) (optional repeating) 
    $data[] = null;
    
    // IN1-50: Signature Code (IS) (optional) 
    $data[] = null;
    
    // IN1-51: Signature Code Date (DT) (optional) 
    $data[] = null;
    
    // IN1-52: Insured_s Birth Place (ST) (optional) 
    $data[] = null;
    
    // IN1-53: VIP Indicator (IS) (optional)
    $data[] = null;
    
    $this->fill($data);
  }
}