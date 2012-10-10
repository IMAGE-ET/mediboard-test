<?php

/**
 * Represents an HL7 RGS message segment (Resource Group) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentRGS
 * RGS - Represents an HL7 RGS message segment (Resource Group)
 */

class CHL7v2SegmentRGS extends CHL7v2Segment {
  var $name   = "RGS";
  var $set_id = null;
  
  /**
   * @var CConsultation
   */
  var $scheduling = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event);
        
    $data = array();
    
    // RGS-1: Set ID - RGS (SI) 
    $data[] = $this->set_id;
    
    // RGS-2: Segment Action Code (ID) (optional)
    switch ($event->code) {
      case 'S12':
        $data[] = "A";
        break;
      case 'S13' : case 'S14' :
        $data[] = "U";
        break;
      case 'S15' :
        $data[] = "D";
        break;
    }
    
    // RGS-3: Resource Group ID (CE) (optional)
    $data[] = $this->scheduling->_id;
    
    $this->fill($data);
  }  
} 