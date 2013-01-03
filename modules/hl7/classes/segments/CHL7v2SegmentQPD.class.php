<?php

/**
 * Represents an HL7 QPD message segment (Query Parameter Definition) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentQPD
 * QPD - Represents an HL7 QPD message segment (Query Parameter Definition)
 */

class CHL7v2SegmentQPD extends CHL7v2Segment {
  var $name   = "QPD";

  /**
   * @var CPatient
   */
  var $patient = null;

  /**
   * @var CSejour
   */
  var $sejour = null;

  /**
   * @var CAffectation
   */
  var $affectation = null;

  function build(CHL7v2Event $event) {
    parent::build($event);

    $patient = $this->patient;
    $sejour  = $this->sejour;

    // QPD-1: Message Query Name (CE)
    $data[] = "IHE PDQ Query";
    
    // QPD-2: Query Tag (ST)
    $data[] = "PDQPDC.".CHL7v2::getDateTime();
    
    // QPD-3: User Parameters (in successive fields) (Varies)

    $QPD3 = array();
    // PID
    if ($patient) {
      $QPD3 = array_merge($QPD3, $this->addQPD3PID($patient));
    }
    // PV1
    if ($sejour) {
      $QPD3 = array_merge($QPD3, $this->addQPD3PV1($sejour));
    }

    CMbArray::removeValue("", $QPD3);
    $data[] = $QPD3;

    $this->fill($data);
  }

  function addQPD3PID(CPatient $patient) {
    return array(
      // Patient Name
      $this->addParameters($patient, "nom", "5.1.1"),
      $this->addParameters($patient, "prenom", "5.2"),

      // Maiden name
      $this->addParameters($patient, "nom_jeune_fille", "6.1.1"),

      // Date of birth
      $this->addParameters($patient, "naissance", "7.1"),

      // Patient Adress
      $this->addParameters($patient, "ville", "11.3"),
      $this->addParameters($patient, "cp", "11.5")
    );
  }

  function addQPD3PV1(CSejour $sejour, CAffectation $affectation = null) {
    return array(
      // Patient class
      $this->addParameters($sejour, "type", "2.1", "4"),
    );
  }

  function addParameters(CMbObject $object, $spec, $field, $mapTo = null) {
    if (!$object->$spec) {
      return;
    }

    $seg = null;
    switch ($object->_class) {
      case "CPatient" :
        $seg = "PID";
        break;
      case "CSejour" :
        $seg = "PV1";
        break;
    }

    if (!$seg) {
      return;
    }

    return array(
      "@$seg.$field",
      $mapTo ? CHL7v2TableEntry::mapTo($mapTo, $object->$spec) : $object->$spec
    );
  }
}

?>