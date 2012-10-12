<?php

/**
 * Represents an HL7 NK1 message segment (Next of Kin / Associated Parties) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentNK1 
 * NK1 - Represents an HL7 NK1 message segment (Next of Kin / Associated Parties)
 */

class CHL7v2SegmentNK1 extends CHL7v2Segment {
  var $name          = "NK1";
  var $set_id        = null;
  
  /**
   * @var CCorrespondantPatient
   */
  var $correspondant = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $correspondant = $this->correspondant;
    $message       = $event->message;
    
    // NK1-1: Set ID - NK1 (SI)
    $data[] = $this->set_id;
     
    // NK1-2: NK Name (XPN) (optional repeating)
    $data[] = $this->getXPN($correspondant);
    
    // NK1-3: Relationship (CE) (optional)
    // Table 0063 - Relationship
    $data[] = array(
      array(
        CHL7v2TableEntry::mapTo("63", $correspondant->parente),
        ($correspondant->parente == "autre") ? $correspondant->parente_autre : null, 
      )
    );
    
    // NK1-4: Address (XAD) (optional repeating)
    $linesAdress = explode("\n", $correspondant->adresse, 2);
    $data[] = array(
      array(
        CValue::read($linesAdress, 0),
        str_replace("\n", $message->componentSeparator, CValue::read($linesAdress, 1)),
        $correspondant->ville,
        null,
        $correspondant->cp,
      )
    );
    
    // NK1-5: Phone Number (XTN) (optional repeating)
    
    // Table - 0201
    // ASN - Answering Service Number
    // BPN - Beeper Number 
    // EMR - Emergency Number  
    // NET - Network (email) Address
    // ORN - Other Residence Number 
    // PRN - Primary Residence Number 
    // VHN - Vacation Home Number  
    // WPN - Work Number
        
    // Table - 0202
    // BP       - Beeper  
    // CP       - Cellular Phone  
    // FX       - Fax 
    // Internet - Internet Address: Use Only If Telecommunication Use Code Is NET 
    // MD       - Modem 
    // PH       - Telephone  
    // TDD      - Telecommunications Device for the Deaf  
    // TTY      - Teletypewriter
    
    $phones = array();
    if ($correspondant->tel) {
      $phones[] = array(
        null,
        // Table - 0201
        "PRN",
        // Table - 0202
        "PH",
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        $correspondant->tel
      );
    }
    
    if ($correspondant->mob) {
      $phones[] = array(
        null,
        // Table - 0201
        "PRN",
        // Table - 0202
        "CP",
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        $correspondant->mob
      );
    }
    
    if ($correspondant->email) {
      $phones[] = array(
        null,
        // Table - 0201
        "NET",
        // Table - 0202
        "Internet",
        $correspondant->email,
      );
    }
      
    $data[] = $phones;
    
    // NK1-6: Business Phone Number (XTN) (optional repeating)
    $data[] = null;
    
    // NK1-7: Contact Role (CE) (optional)
    // Table - 0131
    $data[] = array(
      array(
        CHL7v2TableEntry::mapTo("131", $correspondant->relation),
        ($correspondant->relation == "autre") ? $correspondant->relation_autre : null, 
      )
    );
    
    // NK1-8: Start Date (DT) (optional)
    $data[] = null;
    
    // NK1-9: End Date (DT) (optional)
    $data[] = null;
    
    // NK1-10: Next of Kin / Associated Parties Job Title (ST) (optional)
    $data[] = null;
    
    // NK1-11: Next of Kin / Associated Parties Job Code/Class (JCC) (optional)
    $data[] = null;
    
    // NK1-12: Next of Kin / Associated Parties Employee Number (CX) (optional)
    $data[] = null;
    
    // NK1-13: Organization Name - NK1 (XON) (optional repeating)
    $data[] = null;
    
    // NK1-14: Marital Status (CE) (optional)
    $data[] = null;
    
    // NK1-15: Administrative Sex (IS) (optional)
    $data[] = null;
    
    // NK1-16: Date/Time of Birth (TS) (optional)
    $data[] = null;
    
    // NK1-17: Living Dependency (IS) (optional repeating)
    $data[] = null;
    
    // NK1-18: Ambulatory Status (IS) (optional repeating)
    $data[] = null;
    
    // NK1-19: Citizenship (CE) (optional repeating)
    $data[] = null;
    
    // NK1-20: Primary Language (CE) (optional)
    $data[] = null;
    
    // NK1-21: Living Arrangement (IS) (optional)
    $data[] = null;
    
    // NK1-22: Publicity Code (CE) (optional)
    $data[] = null;
    
    // NK1-23: Protection Indicator (ID) (optional)
    $data[] = null;
    
    // NK1-24: Student Indicator (IS) (optional)
    $data[] = null;
    
    // NK1-25: Religion (CE) (optional)
    // Interdit IHE France
    $data[] = null;
    
    // NK1-26: Mother's Maiden Name (XPN) (optional repeating)
    $data[] = null;
    
    // NK1-27: Nationality (CE) (optional)
    $data[] = null;
    
    // NK1-28: Ethnic Group (CE) (optional repeating)
    // Interdit IHE France
    $data[] = null;
    
    // NK1-29: Contact Reason (CE) (optional repeating)
    $data[] = null;
    
    // NK1-30: Contact Person's Name (XPN) (optional repeating)
    $data[] = null;
    
    // NK1-31: Contact Person's Telephone Number (XTN) (optional repeating)
    $data[] = null;
    
    // NK1-32: Contact Person's Address (XAD) (optional repeating)
    $data[] = null;
    
    // NK1-33: Next of Kin/Associated Party's Identifiers (CX) (optional repeating)
    $data[] = null;
    
    // NK1-34: Job Status (IS) (optional)
    $data[] = null;
    
    // NK1-35: Race (CE) (optional repeating)
    // Interdit IHE France
    $data[] = null;
    
    // NK1-36: Handicap (IS) (optional)
    $data[] = null;
    
    // NK1-37: Contact Person Social Security Number (ST) (optional)
    $data[] = null;
    
    // NK1-38: Next of Kin Birth Place (ST) (optional)
    $data[] = null;
    
    // NK1-39: VIP Indicator (IS) (optional)
    $data[] = null;
    
    $this->fill($data);
  }
}  
?>