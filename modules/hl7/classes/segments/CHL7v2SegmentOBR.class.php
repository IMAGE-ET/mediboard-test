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
 * Class CHL7v2SegmentOBR
 * OBR - Represents an HL7 OBR message segment (Observation Request)
 */
class CHL7v2SegmentOBR extends CHL7v2Segment {

  /** @var string */
  public $name = "OBR";

  public $object;

  /**
   * BuildOBR segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return void
   */
  function build(CHL7v2Event $event) {
    parent::build($event);
    /** @var CPrescriptionLineElement $object */
    $object = $this->object;

    // OBR-1: Set ID - Observation Request (SI) (optional)
    $data[] = "1";

    // OBR-2: Placer Order Number (EI)
    $data[] = $object->_id;

    // OBR-3: Filler Order Number (EI) (optional)
    $data[] = null;

    // OBR-4: Universal Service ID (CE)
    //@todo a voir
    $data[] = $object->_view;

    // OBR-5: Priority (ID) (optional)
    $data[] = null;

    // OBR-6: Request Date/Time (TS) (optional)
    $data[] = null;

    // OBR-7: Observation Date/Time (TS) (optional)
    $data[] = null;

    // OBR-8: Observation End Date/Time (TS) (optional)
    $data[] = null;

    // OBR-9: Collection Volume (CQ) (optional)
    $data[] = null;

    // OBR-10: Collection Identifier (XCN) (optional repeating)
    $data[] = null;

    // OBR-11: Specimen Action Code (ID) (optional table 0065)
    $data[] = null;

    // OBR-12: Danger Code (CE) (optional)
    $data[] = null;

    // OBR-13: Relevant Clinical Info (ST) (optional)
    $data[] = null;

    // OBR-14: Specimen Rcv'd Date/Time (TS) (c)
    $data[] = null;

    // OBR-15: Specimen Source (CM) (optional)
    $data[] = null;

    // OBR-16: Ordering Provider (XCN)
    $object->loadRefPraticien();
    $obr16 = $this->getXCN($object->_ref_praticien, $event->_receiver);
    $data[] = $obr16;

    // OBR-17: Order Callback Phone Number (XTN) (optional repeating)
    $data[] = null;

    // OBR-18: Placer Field 1 (ST) (optional)
    $data[] = null;

    // OBR-19: Placer Field 2 (ST) (optional)
    $data[] = null;

    // OBR-20: Filler Field 1 (ST) (optional)
    $data[] = null;

    // OBR-21: Filler Field 2 (ST) (optional)
    $data[] = null;

    // OBR-22: Result Rpt./Status Change (TS) (c)
    $data[] = null;

    // OBR-23: Charge to Pratice (CM) (optional)
    $data[] = null;

    // OBR-24: Diagnostic Service Sect ID (ID) (optional table 0074)
    $data[] = null;

    // OBR-25: Result Status (ID) (c table 0123)
    $data[] = null;

    // OBR-26: Parent Result (CM) (optional)
    $data[] = null;

    // OBR-27: Quantity/Timing (TQ)
    $data[] = array(
      null,
      null,
      $object->duree,
      $object->_debut_reel,
      $object->_fin);

    // OBR-28: Result Copies to (CN) (optional)
    $data[] = null;

    // OBR-29: Parent Number (CM) (optional)
    $data[] = null;

    // OBR-30: Transportation Mode (ID) (optional table 0124)
    $data[] = null;

    // OBR-31: reason for Study (CE) (optional)
    $data[] = null;

    // OBR-32: Principal Result Interpreter (CM) (optional)
    $data[] = null;

    // OBR-33: Assitant Result Interpreter (CM) (optional)
    $data[] = null;

    // OBR-34: Technician (CM) (optional)
    $data[] = null;

    // OBR-35: Transcriptionist (CM) (optional)
    $data[] = null;

    // OBR-36: Scheduled Date/time (TS) (optional)
    $data[] = null;

    // OBR-37: Number of Sample Containers (NM) (optional)
    $data[] = null;

    // OBR-38: Transport Logistics of Collected Sample (CE) (optional)
    $data[] = null;

    // OBR-39: Collector's Comment (CE) (optional)
    $data[] = null;

    // OBR-40: Transport Arrangement responsibility (CE) (optional)
    $data[] = null;

    // OBR-41: Transport Arranged (ID) (optional table 0224)
    $data[] = null;

    // OBR-42: Escort Required (ID) (optional table 0225)
    $data[] = null;

    // OBR-43: Planned Patient Transport Comment (CE) (optional)
    $data[] = null;

    $this->fill($data);
  }
}