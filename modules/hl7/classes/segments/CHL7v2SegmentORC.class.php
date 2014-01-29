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
    /** @var CPrescriptionLineElement $object */
    $object = $this->object;

    // ORC-1: Order Control (ID)
    //NW - add; CA - delete; xo - modify;
    //@todo voir pour suppression
    $count = $object->countExchanges($event->profil);
    $orc1 = "NW";
    if ($count > 0) {
      $orc1 = "xo";
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
    //@todo a voir
    $data[] = array(array("", "", $object->duree, $this->getDateTime(null, $object->_debut_reel), $this->getDateTime(null, $object->_fin)));

    // ORC-8: Parent (CM) (optional)
    //shall be valued only if the current order is a child order (i.e., if the field ORC 1 Order Control has a value of CH).
    $data[] = null;

    // ORC-9: date/time od Transaction (TS)
    $data[] = $this->getDateTime($object->debut);

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
    //@todo a voir
    $data[] = $object->_ref_prescription->_ref_object->loadRefUFHebergement()->libelle;

    // ORC-18: Entering Device (CE) (optional)
    $data[] = null;

    // ORC-19: Action By (XCN) (optional)
    $data[] = null;

    $this->fill($data);
  }
}