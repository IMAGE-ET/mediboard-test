<?php

/**
 * $Id$
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Class CHL7v2SegmentZDS
 * ZDS - Represents an HL7 ZDS message segment (Study Instance UID)
 */
class CHL7v2SegmentZDS extends CHL7v2Segment {

  /** @var string */
  public $name = "ZDS";

  /**
   * BuildORC segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return void
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    // ZDS-1: Study Instance UID (RP)
    // 1 reference pointer^2 Application ID^3 Type of Data^4 Subtype
    $data[] = null;

    $this->fill($data);
  }
}