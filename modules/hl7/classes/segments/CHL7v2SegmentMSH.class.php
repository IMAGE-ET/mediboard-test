<?php

/**
 * Represents an HL7 MSH message segment (Message Header) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License; see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentMSH 
 * MSH - Represents an HL7 MSH message segment (Message Header) 
 */

class CHL7v2SegmentMSH extends CHL7v2Segment {
  var $name = "MSH";
  
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $message  = $event->message;
    // Dans le cas d'un segment MSH la création peut-être soit : receiver / sender (ack)
    $actor    = (isset($event->_sender->_id)) ? $event->_sender : $event->_receiver;
    $actor->loadRefGroup();
    $actor->loadConfigValues();
    $group    = $actor->_ref_group;
    
    $data = array();
    
    // MSH-1: Field Separator (ST)
    $data[] = $message->fieldSeparator;  
           
    // MSH-2: Encoding Characters (ST)
    $data[] = substr($message->getEncodingCharacters(), 1);       
     
    // MSH-3: Sending Application (HD) (optional)
    $data[] = CAppUI::conf("hl7 sending_application"); 
    
    // MSH-4: Sending Facility (HD) (optional)
    $data[] = CAppUI::conf("hl7 sending_facility");
    
    // MSH-5: Receiving Application (HD) (optional)
    $data[] = isset($actor->_configs["receiving_application"]) ? $actor->_configs["receiving_application"] : $actor->nom; 
    
    // MSH-6: Receiving Facility (HD) (optional)
    $data[] = isset($actor->_configs["receiving_facility"]) ? $actor->_configs["receiving_facility"] : $actor->nom; 
    
    // MSH-7: Date/Time Of Message (TS)
    $data[] = mbDateTime(); 
    
    // MSH-8: Security (ST) (optional)
    $data[] = null; 
    
    // MSH-9: Message Type (MSG)
    $data[] = $event->msg_codes; 
    
    // MSH-10: Message Control ID (ST) 
    $data[] = $event->_exchange_ihe->_id; 
    
    // MSH-11: Processing ID (PT) 
    // Table 103 
    // D - Debugging
    // P - Production
    // T - Training
    $data[] = (CAppUI::conf("instance_role") == "prod") ? "P" : "D";
     
    // MSH-12: Version ID (VID)     
    $data[] = CHL7v2::prepareHL7Version($event->version); 
    
    // MSH-13: Sequence Number (NM) (optional)
    $data[] = null; 
    
    // MSH-14: Continuation Pointer (ST) (optional)
    $data[] = null; 
    
    // MSH-15: Accept Acknowledgment Type (ID) (optional)
    // Table 155
    // AL - Always 
    // NE - Never  
    // ER - Error/reject conditions only 
    // SU - Successful completion only
    $data[] = null;
    
    // MSH-16: Application Acknowledgment Type (ID) (optional)
    // Table 155
    // AL - Always 
    // NE - Never  
    // ER - Error/reject conditions only 
    // SU - Successful completion only
    $data[] = null;
    
    // MSH-17: Country Code (ID) (optional)
    // FRA = 250
    $data[] = CHL7v2TableEntry::mapTo("399", "250"); 
    
    // MSH-18: Character Set (ID) (optional repeating)
    $encoding = isset($actor->_configs["encoding"]) ? $actor->_configs["encoding"] : "UTF-8";
    $data[] = CHL7v2TableEntry::mapTo("211", $encoding); 
    
    // MSH-19: Principal Language Of Message (CE) (optional)
    $data[] = array(
      "FR"
    ); 
    
    // MSH-20: Alternate Character Set Handling Scheme (ID) (optional)
    $data[] = null; 
    
    // MSH-21: Message Profile Identifier (EI) (optional repeating) 
    $data[] = null;
    
    $this->fill($data);
  }
  
  function fill($fields) {
    $message = $this->getMessage();
    
    // Field separator
    $fields[0] = $message->fieldSeparator; 
    
    // Encoding characters without the field separator
    $fields[1] = substr($message->getEncodingCharacters(), 1); 
    
    // Message type
    $fields[8] = $message->name;
    
    return parent::fill($fields);
  }
}

?>