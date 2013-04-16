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
  public $name   = "QPD";

  /**
   * @var CPatient
   */
  public $patient;

  /**
   * @var CSejour
   */
  public $sejour;

  /**
   * @var CAffectation
   */
  public $affectation;

  /**
   * Build QPD segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    $patient = $this->patient;
    $sejour  = $this->sejour;

    // QPD-1: Message Query Name (CE)
    $data[] = "IHE PDQ Query";
    
    // QPD-2: Query Tag (ST)
    $data[] = str_replace(".", "", uniqid("", true));
    
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

    // QPD-4 :
    $data[] = null;

    // QPD-5 :
    $data[] = null;

    // QPD-6 :
    $data[] = null;

    // QPD-7 :
    $data[] = null;

    // QPD-8 : What domains returned
    if (isset($patient->_domains_returned)) {
      $domains_returned = $patient->_domains_returned;
      $QPD8 = array (
        CMbArray::get($domains_returned, "domains_returned_namespace_id"),
        CMbArray::get($domains_returned, "domains_returned_universal_id"),
        CMbArray::get($domains_returned, "domains_returned_universal_id_type")
      );
      CMbArray::removeValue(null, $QPD8);

      if (empty($QPD8)) {
        $data[] = null;
      }
      else {
        $data[] = array(
          array(
            null,
            null,
            null,
            $QPD8
          )
        );
      }
    }
    else {
      $data[] = null;
    }

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
    $qpd3pid = array();

    // PID-3 : Patient Identifier List
    if (isset($patient->_patient_identifier_list)) {
      $patient_identifier_list = $patient->_patient_identifier_list;

      $qpd3pid = array_merge(
        $qpd3pid, array(
          $this->setDemographicsValues($patient, CMbArray::get($patient_identifier_list, "person_id_number")           , "3.1"),
          $this->setDemographicsValues($patient, CMbArray::get($patient_identifier_list, "person_namespace_id")        , "3.4.1"),
          $this->setDemographicsValues($patient, CMbArray::get($patient_identifier_list, "person_universal_id")        , "3.4.2"),
          $this->setDemographicsValues($patient, CMbArray::get($patient_identifier_list, "person_universal_id_type")   , "3.4.3"),
          $this->setDemographicsValues($patient, CMbArray::get($patient_identifier_list, "person_identifier_type_code"), "3.5")
        )
      );
    }

    return array_merge(
      $qpd3pid, array(
        // PID-5 : Patient Name
        $this->setDemographicsFields($patient, "nom", "5.1.1"),
        $this->setDemographicsFields($patient, "prenom", "5.2"),

        // PID-6 : Maiden name
        $this->setDemographicsFields($patient, "nom_jeune_fille", "6.1.1"),

        // PID-7 : Date of birth
        $this->setDemographicsFields($patient, "naissance", "7.1", null, true),

        // PID-8: Administrative Sex
        $this->setDemographicsFields($patient, "sexe", "8", "1"),

        // PID-11 : Patient Adress
        $this->setDemographicsFields($patient, "ville", "11.3"),
        // $this->setDemographicsValues($patient, "", "11.4"),
        $this->setDemographicsFields($patient, "cp", "11.5"),

        // PID-13 : Phone Number
        // $this->setDemographicsValues($patient, "", "13.6"),
        //  $this->setDemographicsValues($patient, "", "13.7"),
      )
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
   * @param CMbObject $object    Object
   * @param string    $mb_field  Field spec
   * @param string    $hl7_field The number of a field
   * @param null      $mapTo     Map to table HL7
   *
   * @return array
   */
  function setDemographicsFields(CMbObject $object, $mb_field, $hl7_field, $mapTo = null) {
    if (!$object->$mb_field) {
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

    $value = $mapTo ? CHL7v2TableEntry::mapTo($mapTo, $object->$mb_field) : $object->$mb_field;

    $spec = $object->_specs[$mb_field];
    if ($spec instanceof CDateSpec || $spec instanceof CBirthDateSpec) {
      $value = CHL7v2::getDate($value);
    }

    return array(
      "@$seg.$hl7_field",
      $value
    );
  }

  /**
   * Populating QPD-3 demographics value
   *
   * @param CMbObject $object Object
   * @param string    $value  Value
   * @param string    $field  The number of a field
   *
   * @return array
   */
  function setDemographicsValues(CMbObject $object, $value, $field) {
    if (!$value) {
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

    return array(
      "@$seg.$field",
      $value
    );
  }
}