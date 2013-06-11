<?php

/**
 * Represents an HL7 ACC message segment (Accident) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentACC
 * ACC - Represents an HL7 ACC message segment (Accident)
 */

class CHL7v2SegmentACC extends CHL7v2Segment {

  /** @var string */
  public $name   = "ACC";
  

  /** @var CSejour */
  public $sejour;

  /**
   * Build ACC segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    // ACC-1: Accident Date/Time (TS) <b>optional </b>
    $data[] = null;
    
    // ACC-2: Accident Code (CE) <b>optional </b>
    $data[] = null;
    
    // ACC-3: Accident Location (ST) <b>optional </b>
    $data[] = null;
    
    // ACC-4: Auto Accident State (CE) <b>optional </b>
    $data[] = null;
    
    // ACC-5: Accident Job Related Indicator (ID) <b>optional </b>
    $data[] = null;
    
    // ACC-6: Accident Death Indicator (ID) <b>optional </b>
    $data[] = null;
    
    // ACC-7: Entered By (XCN) <b>optional </b>
    $data[] = null;
    
    // ACC-8: Accident Description (ST) <b>optional </b>
    $data[] = null;
    
    // ACC-9: Brought In By (ST) <b>optional </b>
    $data[] = null;
    
    // ACC-10: Police Notified Indicator (ID) <b>optional </b>
    $data[] = null;
    
    // ACC-11: Accident Address (XAD) <b>optional </b>
    $data[] = null;
    
    $this->fill($data);
  }
}