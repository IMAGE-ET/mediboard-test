<?php

/**
 * Represents an HL7 QID message segment (Query Identification) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentQID
 * QID - Represents an HL7 QID message segment (Query Identification)
 */

class CHL7v2SegmentQID extends CHL7v2Segment {

  /** @var string */
  public $name    = "QID";


  /** @var CPatient */
  public $patient;

  /**
   * Build QID segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    // QID-1: Query Tag (ST) (optional)
    $data[] = null;
    
    // QID-2: Message Query Name (CE) (optional)
    $data[] =  null;

    $this->fill($data);
  }
}