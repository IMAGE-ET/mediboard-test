<?php

/**
 * Represents an HL7 QAK message segment (Query Parameter Definition) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentQAK
 * QAK - Represents an HL7 QAK message segment (Query Parameter Definition)
 */

class CHL7v2SegmentQAK extends CHL7v2Segment {
  /**
   * @var string
   */
  var $name   = "QAK";

  /**
   * @var array
   */
  var $objects = array();

  /**
   * Build QPD segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    $objects = $this->objects;

    // QAK-1: Query Tag (ST) (optional)
    $data[] = null;
    
    // QAK-2: Query Response Status (ID) (optional)
    $data[] = (!$objects || count($objects) == 0) ? "NF" : "OK";
    
    // QPD-3: User Parameters (in successive fields) (Varies) (optional)
    $data[] = null;

    // QAK-4: Hit Count (NM) (optional)
    $data[] = null;

    // QAK-5: This payload (NM) (optional)
    $data[] = null;

    // QAK-6: Hits remaining (NM) (optional)
    $data[] = null;

    $this->fill($data);
  }

  /**
   * Add PID in QPD segment
   *
   * @param CPatient $patient Person
   *
   * @return array
   */
  function addQPD3PID(CPatient $patient) {
    return array(
      // Patient Name
      $this->setDemographicsFields($patient, "nom", "5.1.1"),
      $this->setDemographicsFields($patient, "prenom", "5.2"),

      // Maiden name
      $this->setDemographicsFields($patient, "nom_jeune_fille", "6.1.1"),

      // Date of birth
      $this->setDemographicsFields($patient, "naissance", "7.1"),

      // Patient Adress
      $this->setDemographicsFields($patient, "ville", "11.3"),
      $this->setDemographicsFields($patient, "cp", "11.5")
    );
  }

  /**
   * Add PV1 in QPD segment
   *
   * @param CSejour      $sejour      Visit
   * @param CAffectation $affectation Location
   *
   * @return array
   */
  function addQPD3PV1(CSejour $sejour, CAffectation $affectation = null) {
    return array(
      // Patient class
      $this->setDemographicsFields($sejour, "type", "2.1", "4"),
    );
  }

  /**
   * Populating QPD-3 demographics fields
   *
   * @param CMbObject $object Object
   * @param string    $spec   Field spec
   * @param string    $field  The number of a field
   * @param null      $mapTo  Map to table HL7
   *
   * @return array
   */
  function setDemographicsFields(CMbObject $object, $spec, $field, $mapTo = null) {
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