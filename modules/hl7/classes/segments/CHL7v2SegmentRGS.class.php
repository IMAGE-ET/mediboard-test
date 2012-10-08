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
  var $name = "RGS";
  
  function build(CHL7v2Event $event) {
    parent::build($event);
        
    $data = array();
    
    // RGS-1: Set ID - RGS (SI) 
    $data[] = null;
    
    // RGS-2: Segment Action Code (ID) (optional)
    $data[] = null;
    
    // RGS-3: Resource Group ID (CE) (optional)
    $data[] = null;
    
    $this->fill($data);
  }  
} 