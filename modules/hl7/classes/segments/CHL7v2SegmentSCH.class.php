<?php

/**
 * Represents an HL7 SCH message segment (Scheduling Activity Information) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentSCH
 * SCH - Represents an HL7 SCH message segment (Scheduling Activity Information)
 */

class CHL7v2SegmentSCH extends CHL7v2Segment {
  var $name = "SCH";
  
  /**
   * @var CConsultation
   */
  var $scheduling = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $receiver = $event->_receiver;
    $group    = $receiver->_ref_group;
    
    $scheduling = $this->scheduling;
        
    $data = array();
    
    // SCH-1: Placer Appointment ID (EI) (optional)
    $data[] = null;
    
    // SCH-2: Filler Appointment ID (EI) (optional)
    $identifiers[] = array(
      // Entity identifier
      $scheduling->_id,
      // Autorité assignement
      CAppUI::conf("hl7 assigning_authority_namespace_id"),
      CAppUI::conf("hl7 assigning_authority_universal_id"),
      CAppUI::conf("hl7 assigning_authority_universal_type_id"),
    );
    
    $idex = CIdSante400::getMatch("CConsultation", $receiver->_tag_consultation, null, $scheduling->_id);
    if ($idex->_id) {
      $configs = $receiver->_configs;
      $identifiers[] = array(
        // Entity identifier
        $idex->id400,
        // Autorité assignement
        $configs["assigning_authority_namespace_id"],
        $configs["assigning_authority_universal_id"],
        $configs["assigning_authority_universal_type_id"]
      );
    }
    $data[] = $identifiers;
    
    // SCH-3: Occurrence Number (NM) (optional)
    $data[] = null;
    
    // SCH-4: Placer Group Number (EI) (optional)
    $data[] = null;
    
    // SCH-5: Schedule ID (CE) (optional)
    $data[] = null;
    
    // SCH-6: Event Reason (CE)
    $data[] = array(
      array(
        null,
        $scheduling->motif
      )
    );
    
    // SCH-7: Appointment Reason (CE) (optional)
    $data[] = null;
    
    // SCH-8: Appointment Type (CE) (optional)
    $data[] = null;
    
    // SCH-9: Appointment Duration (NM) (optional)
    $data[] = null;
    
    // SCH-10: Appointment Duration Units (CE) (optional)
    $data[] = null;
    
    // SCH-11: Appointment Timing Quantity (TQ) (optional repeating)
    $data[] = array (
        array(
        null,
        null,
        // Durée (M puis le nb de minutes)
        "M".$scheduling->_duree,
        $scheduling->_datetime,
        $scheduling->_date_fin,
      )
    );
    
    // SCH-12: Placer Contact Person (XCN) (optional repeating)
    $data[] = $this->getXCN($scheduling->_ref_praticien, $receiver);
    
    // SCH-13: Placer Contact Phone Number (XTN) (optional)
    $data[] = null;
    
    // SCH-14: Placer Contact Address (XAD) (optional repeating)
    $data[] = null;
    
    // SCH-15: Placer Contact Location (PL) (optional)
    $data[] = null;
    
    // SCH-16: Filler Contact Person (XCN) ( repeating)
    $first_log = $scheduling->loadFirstLog();
    $mediuser = $first_log->loadRefUser()->loadRefMediuser();
    $data[] = $this->getXCN($mediuser, $receiver);
    
    // SCH-17: Filler Contact Phone Number (XTN) (optional)
    $data[] = null;
    
    // SCH-18: Filler Contact Address (XAD) (optional repeating)
    $data[] = null;
    
    // SCH-19: Filler Contact Location (PL) (optional)
    $data[] = null;
    
    // SCH-20: Entered By Person (XCN) ( repeating)
    $data[] = $this->getXCN($mediuser, $receiver);
    
    // SCH-21: Entered By Phone Number (XTN) (optional repeating)
    $data[] = null;
    
    // SCH-22: Entered By Location (PL) (optional)
    $data[] = null;
    
    // SCH-23: Parent Placer Appointment ID (EI) (optional)
    $data[] = null;
    
    // SCH-24: Parent Filler Appointment ID (EI) (optional)
    $data[] = null;
    
    // SCH-25: Filler Status Code (CE) (optional)
    // Table - 0278
    // Pending   - Appointment has not yet been confirmed  
    // Waitlist  - Appointment has been placed on a waiting list for a particular slot, or set of slots  
    // Booked    - The indicated appointment is booked   
    // Started   - The indicated appointment has begun and is currently in progress  
    // Complete  - The indicated appointment has completed normally (was not discontinued, canceled, or deleted)   
    // Cancelled - The indicated appointment was stopped from occurring (canceled prior to starting)   
    // Dc        - The indicated appointment was discontinued (DC'ed while in progress, discontinued parent appointment, or discontinued child appointment)  
    // Deleted   - The indicated appointment was deleted from the filler application   
    // Blocked   - The indicated time slot(s) is(are) blocked  
    // Overbook  - The appointment has been confirmed; however it is confirmed in an overbooked state  
    // Noshow    - The patient did not show up for the appointment 
    $status_code = "Booked";
    switch ($scheduling->chrono) {
      case '32': case '48':
        $status_code = "Started";
        break;
      case '64':
        $status_code = "Complete";
        break;
    }
    if ($scheduling->annule) {
      $status_code = "Cancelled";
    }
    $data[] = $status_code;
    
    // SCH-26: Placer Order Number (EI) (optional repeating)
    $data[] = null;
    
    // SCH-27: Filler Order Number (EI) (optional repeating)
    $data[] = null;
       
    $this->fill($data);
  } 
} 