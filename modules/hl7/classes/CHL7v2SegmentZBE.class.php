<?php

/**
 * Represents an HL7 ZBE message segment (Movement) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentZBE
 * ZBE - Represents an HL7 ZBE message segment (Movement)
 */

class CHL7v2SegmentZBE extends CHL7v2Segment {
  var $name   = "ZBE";
  var $sejour = null;
  
  function build(CHL7v2Event $event) {
    $data[] = null;
    
    $sejour = new CSejour();
    $sejour = $this->sejour;
    
    parent::build($event);

    // ZBE-1: Movement ID (EI) <b>optional </b>
    $data[] = null;
    
    // ZBE-2: Start of Movement Date/Time (TS)
    $data[] = null;
    
    // ZBE-3: End of Movement Date/Time (TS) <b>optional </b>
    $data[] = null;
    
    // ZBE-4: Action on the Movement (ID)
    $data[] = null;
    
    // ZBE-5: Indicator "Historical Movement" (ID) 
    $data[] = null;
    
    // ZBE-6: Original trigger event code (ID) <b>optional </b>
    $data[] = null;
    
    // ZBE-7: Ward of medical responsibility in the period starting with this movement (XON) <b>optional </b>
    $data[] = null;
    
    // ZBE-8: Ward of care responsibility in the period starting with this movement (XON) <b>optional </b>
    $data[] = null;
    
    // ZBE-9: Nature of this movement (CWE)
    $data[] = null;
    
    $this->fill($data);
  }
}

?>