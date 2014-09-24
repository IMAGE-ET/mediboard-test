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
 * Class CHL7v2SegmentORC
 * ORC - Represents an HL7 ORC message segment (Common Order)
 */
class CHL7v2SegmentORC extends CHL7v2Segment {

  /** @var string */
  public $name = "ORC";

  public $object;

  /**
   * BuildORC segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return void
   */
  function build(CHL7v2Event $event) {
    parent::build($event);
    /** @var CConsultation $object */
    $object = $this->object;

    // ORC-1: Order Control (ID)
    //NW - add; CA - delete; xo - modify;

    $log = $object->_ref_last_log;
    switch ($log->type) {
      case "create":
        $orc1 = "NW";
        break;
      case "store":
        $orc1 = "XO";
        if ($object->fieldModified("annule", "1")) {
          $orc1 = "CA";
        }
        if ($object->fieldModified("annule", "0")) {
          $orc1 = "NW";
        }
        break;
      case "delete":
        $orc1 = "CA";
        break;
      default:
        $orc1 = null;
    }

    $data[] = $orc1;

    // ORC-2: Placer Order Number (EI)
    $data[] = $object->_id;

    // ORC-3: Filler Order Number (EI) (optional)
    $data[] = null;

    // ORC-4: Placer Group Number (EI) (optional)
    $data[] = null;

    // ORC-5: Order Status (ID) (optional table 0038)
    $data[] = "SC";

    // ORC-6: Response Flag (ID) (optional table 0121)
    $data[] = null;

    // ORC-7: Quantity/Timing (TQ)
    $data[] = array(
      array(
        "1",
        null,
        null,
        $object->_datetime
      )
    );

    // ORC-8: Parent (CM) (optional)
    //shall be valued only if the current order is a child order (i.e., if the field ORC 1 Order Control has a value of CH).
    $data[] = null;

    // ORC-9: date/time od Transaction (TS)
    $data[] = CMbDT::dateTime();

    // ORC-10: Entered By (XCN) (optional)
    $data[] = null;

    // ORC-11: Verified By (XCN) (optional)
    $data[] = null;

    // ORC-12: Ordering Provider (XCN)
    $object->loadRefPraticien();
    $orc12 = $this->getXCN($object->_ref_praticien, $event->_receiver);
    $data[] = $orc12;

    // ORC-13: Enterer's Location (PL) (optional)
    $data[] = null;

    // ORC-14: Call Back Phone Number (XTN) (optional repeating)
    $data[] = null;

    // ORC-15: Order Effective Date/Time (TS) (optional)
    $data[] = null;

    // ORC-16: Order Control Code reason (CE) (optional)
    $data[] = null;

    // ORC-17: Entering Organization (CE)
    $group = $event->_receiver->_ref_group;
    $orc17 = array(
      array(
        $group->_id,
        $group->raison_sociale,
      )
    );
    $data[] = $orc17;

    // ORC-18: Entering Device (CE) (optional)
    $data[] = null;

    // ORC-19: Action By (XCN) (optional)
    $data[] = null;

    $this->fill($data);
  }
}