<?php

/**
 * Represents an HL7 AIL message segment (Appointment Information - Location Resource) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentAIL
 * AIL - Represents an AIL ZBE message segment (Appointment Information - Location Resource)
 */

class CHL7v2SegmentAIL extends CHL7v2Segment {
  var $name = "AIL";
  var $set_id = null;
  
  /**
   * @var CConsultation
   */
  var $scheduling = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $scheduling = $this->scheduling;
        
    $data = array();
    
    // AIL-1: Set ID - AIL (SI)
    $data[] = $this->set_id;
    
    // AIL-2: Segment Action Code (ID) (optional)
    $data[] = $this->getSegmentActionCode($event);
    
    // AIL-3: Location Resource ID (PL) (optional repeating)
    $data[] = array(
      array(
        null,
        null,
        null,
        null,
        null,
        null,
        // Building
        $scheduling->loadRefGroup()->_view
      )
    );
    
    // AIL-4: Location Type-AIL (CE) (optional)
    $data[] = null;
    
    // AIL-5: Location Group (CE) (optional)
    $data[] = null;
    
    // AIL-6: Start Date/Time (TS) (optional)
    $data[] = null;
    
    // AIL-7: Start Date/Time Offset (NM) (optional)
    $data[] = null;
    
    // AIL-8: Start Date/Time Offset Units (CE) (optional)
    $data[] = null;
    
    // AIL-9: Duration (NM) (optional)
    $data[] = null;
    
    // AIL-10: Duration Units (CE) (optional)
    $data[] = null;
    
    // AIL-11: Allow Substitution Code (IS) (optional)
    $data[] = null;
    
    // AIL-12: Filler Status Code (CE) (optional)
    $data[] = $this->getFillerStatutsCode($scheduling);
    
    $this->fill($data);
  }    
} 