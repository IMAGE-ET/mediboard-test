<?php

/**
 * Patient Administration HL7v3
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v3EventPRPA
 * Patient Administration HL7v3
 */
class CHL7v3EventPRPA extends CHL7v3Event implements CHL7EventPRPA {
  /**
   * Construct
   *
   * @return \CHL7v3EventPRPA
   */
  function __construct() {
    parent::__construct();

    $this->event_type = "PRPA";
  }

  /**
   * Build event
   *
   * @param CMbObject $object Object
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($object) {
    parent::build($object);

    // Header
    $this->addHeader();

    // Receiver
    $this->addReceiver();

    // Sender
    $this->addSender();
  }

  /**
   * Get interaction
   *
   * @return string|void
   */
  function getInteractionID() {
  }

  /**
   * Add header
   *
   * @return void
   */
  function addHeader() {
    $dom            = $this->dom;
    $exchange_hl7v3 = $this->_exchange_hl7v3;

    $root = $dom->addElement($dom, $this->getInteractionID());
    $dom->addNameSpaces();

    // id
    $id = $dom->addElement($root, "id");
    $this->setII($id, "ID", "OID");

    // creationTime
    $creationTime = $dom->addElement($root, "creationTime");
    $dom->addAttribute($creationTime, "value", CHL7v3MessageXML::dateTime());

    // interactionId
    $interactionId = $dom->addElement($root, "interactionId");
    $this->setII($interactionId, $this->getInteractionID(), "2.16.840.1.113883.1.6");

    // processingCode
    $processingCode = $dom->addElement($root, "processingCode");
    $instance_role  = CAppUI::conf("instance_role") == "prod" ? "P" : "D";
    $dom->addAttribute($processingCode, "code", $instance_role);

    // processingModeCode
    $processingModeCode = $dom->addElement($root, "processingModeCode");
    $dom->addAttribute($processingModeCode, "code", "T");

    // acceptAckCode
    $acceptAckCode = $dom->addElement($root, "acceptAckCode");
    $dom->addAttribute($acceptAckCode, "code", "AL");
  }

  /**
   * Add receiver
   *
   * @return void
   */
  function addReceiver() {
    $dom  = $this->dom;
    $root = $dom->documentElement;

    $receiver = $dom->addElement($root, "receiver");
    $this->setTypeCode($receiver, "RCV");

    $this->addDevice($receiver, $this->_receiver);
  }

  /**
   * Add sender
   *
   * @return void
   */
  function addSender() {
    $dom  = $this->dom;
    $root = $dom->documentElement;

    $sender = $dom->addElement($root, "sender");
    $this->setTypeCode($sender, "SND");

    $this->addDevice($sender);
  }

  /**
   * Add device
   *
   * @param DOMNode       $elParent Parent element
   * @param CInteropActor $actor    Actor
   *
   * @return void
   */
  function addDevice(DOMNode $elParent, CInteropActor $actor = null) {
    $dom = $this->dom;

    // device
    $device = $dom->addElement($elParent, "device");
    $dom->addAttribute($device, "classCode", "DEV");
    $dom->addAttribute($device, "determinerCode", "INSTANCE");

    // id
    $id = $dom->addElement($device, "id");
    $dom->addAttribute($id, "root", $actor ? $actor->OID : CAppUI::conf("mb_oid"));

    // softwareName
    $dom->addElement($device, "softwareName", $actor ? $actor->nom : CAppUI::conf("mb_id"));
  }

  /**
   * Add control act process
   *
   * @return DOMElement
   */
  function addControlActProcess() {
    $dom  = $this->dom;
    $root = $dom->documentElement;

    $controlActProcess = $dom->addElement($root, "controlActProcess");
    $dom->addAttribute($controlActProcess, "classCode", "CACT");
    $dom->addAttribute($controlActProcess, "moodCode", "EVN");

    return $controlActProcess;
  }

  /**
   * Add subject of
   *
   * @param DOMNode $elParent      Parent element
   * @param string  $code          Code
   * @param string  $codeSystem    Code system
   * @param string  $displayName   Display name
   * @param string  $value         Value
   * @param bool    $effectiveTime Effective time
   *
   * @return void
   */
  function addSubjectOf(DOMNode $elParent, $code, $codeSystem, $displayName, $value, $effectiveTime = false) {
    $dom = $this->dom;

    $subjectOf = $dom->addElement($elParent, "subjectOf");
    $this->setTypeCode($subjectOf, "SBJ");

    $administrativeObservation = $dom->addElement($subjectOf, "administrativeObservation");
    $this->setClassMoodCode($administrativeObservation, "OBS", "EVN");

    $code_elt = $dom->addElement($administrativeObservation, "code");
    $this->setCode($code_elt, $code, $codeSystem, $displayName);

    if ($effectiveTime) {
      $date = $this->getTimeToUtc(CMbDT::date());
      $effectiveTime = $dom->addElement($administrativeObservation, "effectiveTime");
      $dom->addAttribute($effectiveTime, "value", $date);
    }

    $value_elt = $dom->addElement($administrativeObservation, "value");
    $value_elt->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $value_elt->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:type', 'BL');
    $dom->addAttribute($value_elt, "value", $value);
  }

  /**
   * Transforme une chaine date au format time CDA
   *
   * @param String $date String
   *
   * @return string
   */
  function getTimeToUtc($date) {
    $timezone = new DateTimeZone(CAppUI::conf("timezone"));
    $date     = new DateTime($date, $timezone);

    return $date->format("Ymd");
  }

  /**
   * Set class code
   *
   * @param DOMNode $elParent  Parent element
   * @param string  $classCode Class code
   *
   * @return void
   */
  function setClassCode(DOMNode $elParent, $classCode) {
    $dom = $this->dom;

    $dom->addAttribute($elParent, "classCode", $classCode);
  }

  /**
   * Set class code
   *
   * @param DOMNode $elParent Parent element
   * @param string  $typeCode Type code
   *
   * @return void
   */
  function setTypeCode(DOMNode $elParent, $typeCode) {
    $dom = $this->dom;

    $dom->addAttribute($elParent, "typeCode", $typeCode);
  }

  /**
   * Set class and determiner code
   *
   * @param DOMNode $elParent       Parent element
   * @param string  $classCode      Class code
   * @param string  $determinerCode Determiner code
   *
   * @return void
   */
  function setClassDeterminerCode(DOMNode $elParent, $classCode, $determinerCode) {
    $dom = $this->dom;

    $this->setClassCode($elParent, $classCode);
    $dom->addAttribute($elParent, "determinerCode", $determinerCode);
  }

  /**
   * Set II
   *
   * @param DOMNode $elParent  Parent element
   * @param string  $extension Extension
   * @param string  $root      Root
   *
   * @return void
   */
  function setII(DOMNode $elParent, $extension, $root) {
    $dom = $this->dom;

    $dom->addAttribute($elParent, "extension", $extension);
    $dom->addAttribute($elParent, "root", $root);
  }

  /**
   * Set code
   *
   * @param DOMNode $elParent    Parent element
   * @param string  $code        Code
   * @param string  $codeSystem  Code system
   * @param string  $displayName Display name
   *
   * @return void
   */
  function setCode(DOMNode $elParent, $code, $codeSystem, $displayName) {
    $dom = $this->dom;

    $dom->addAttribute($elParent, "code", $code);
    $dom->addAttribute($elParent, "codeSystem", $codeSystem);
    $dom->addAttribute($elParent, "displayName", $displayName);
  }

  /**
   * Set class and mood code
   *
   * @param DOMNode $elParent  Parent element
   * @param string  $classCode Class code
   * @param string  $moodCode  Mood code
   *
   * @return void
   */
  function setClassMoodCode(DOMNode $elParent, $classCode, $moodCode) {
    $dom = $this->dom;

    $dom->addAttribute($elParent, "classCode", $classCode);
    $dom->addAttribute($elParent, "moodCode", $moodCode);
  }

  /**
   * Set qualifier
   *
   * @param DOMNode $elParent  Parent element
   * @param string  $qualifier Qualifier
   *
   * @return void
   */
  function setQualifier(DOMNode $elParent, $qualifier) {
    $dom = $this->dom;

    $dom->addAttribute($elParent, "qualifier", $qualifier);
  }

  /**
   * Add value
   *
   * @param DOMNode $elParent Parent element
   * @param string  $value    Value
   * @param string  $use      Use value
   *
   * @return void
   */
  function addValue(DOMNode $elParent, $value = null, $use = null) {
    $dom = $this->dom;

    if (!$value) {
      return;
    }

    if ($use) {
      $dom->addAttribute($elParent, "use", $use);
    }

    $dom->addAttribute($elParent, "value", $value);
  }

  /**
   * Add name
   *
   * @param DOMNode  $elParent Parent element
   * @param CPatient $patient  Patient
   *
   * @return void
   */
  function addName(DOMNode $elParent, CPatient $patient) {
    $dom  = $this->dom;

    $name = $dom->addElement($elParent, "name");
    $dom->addElement($name, "prefix", CAppUI::tr("CPatient.civilite.$patient->civilite"));

    if ($patient->_p_maiden_name) {
      $family = $dom->addElement($name, "family", $patient->_p_maiden_name);
      $this->setQualifier($family, "SP");
    }

    $family = $dom->addElement($name, "family", $patient->_p_last_name);
    $this->setQualifier($family, "BR");

    $dom->addElement($name, "given", $patient->_p_first_name);
    $dom->addElement($name, "given", $patient->prenom_2);
    $dom->addElement($name, "given", $patient->prenom_3);
    $dom->addElement($name, "given", $patient->prenom_4);
  }

  /**
   * Add Telecom
   *
   * @param DOMNode  $elParent Parent element
   * @param CPatient $patient  Patient
   *
   * @return void
   */
  function addTelecom(DOMNode $elParent, CPatient $patient) {
    $dom = $this->dom;

    $patientPhoneNumber       = $patient->_p_phone_number;
    $patientMobilePhoneNumber = $patient->_p_mobile_phone_number;
    $patientEmail             = $patient->_p_email;

    $telecom = $dom->addElement($elParent, "telecom");
    $this->addValue($telecom, ($patientPhoneNumber ? "tel:$patientPhoneNumber" : null), "HP");

    $telecom = $dom->addElement($elParent, "telecom");
    $this->addValue($telecom, ($patientPhoneNumber ? "tel:$patientMobilePhoneNumber" : null), "MC");

    $telecom = $dom->addElement($elParent, "telecom");
    $this->addValue($telecom, ($patientEmail ? "mailto:$patientEmail" : null));
  }

  /**
   * Add Adress
   *
   * @param DOMNode  $elParent Parent element
   * @param CPatient $patient  Patient
   *
   * @return void
   */
  function addAdress(DOMNode $elParent, CPatient $patient) {
    $dom = $this->dom;

    $addr = $dom->addElement($elParent, "addr");
    $dom->addElement($addr, "streetAddressLine", $patient->_p_street_address);
    $dom->addElement($addr, "postalCode", $patient->_p_postal_code);
    $dom->addElement($addr, "city", $patient->_p_city);
    $dom->addElement($addr, "country", $patient->_p_country);
  }

  /**
   * Add birthplace
   *
   * @param DOMNode  $elParent Parent element
   * @param CPatient $patient  Patient
   *
   * @return void
   */
  function addBirthPlace(DOMNode $elParent, CPatient $patient) {
    $dom = $this->dom;

    $birthplace = $dom->addElement($elParent, "birthPlace");
    $dom->addAttribute($birthplace, "classCode", "BIRTHPL");

    $patient->updateNomPaysInsee();

    $addr = $dom->addElement($birthplace, "addr");
    $dom->addElement($addr, "postalCode", $patient->cp_naissance);
    $dom->addElement($addr, "city", $patient->lieu_naissance);
    $dom->addElement($addr, "country", $patient->_pays_naissance_insee);
  }

  /**
   * Add represented organization
   *
   * @param DOMNode $elParent Parent element
   * @param CGroups $group    Group
   *
   * @return void
   */
  function addRepresentedOrganization(DOMNode $elParent, CGroups $group) {
    $dom = $this->dom;

    $representedOrganization = $dom->addElement($elParent, "representedOrganization");
    $this->setClassDeterminerCode($representedOrganization, "ORG", "INSTANCE");

    $id = $dom->addElement($representedOrganization, "id");
    if ($group->siret) {
      $this->setII($id, "3".$group->siret, "1.2.250.1.71.4.2.2");
    }

    if ($group->finess) {
      $this->setII($id, "1".$group->finess, "1.2.250.1.71.4.2.2");
    }

    $dom->addElement($representedOrganization, "name", $group->text);

    $contactParty = $dom->addElement($representedOrganization, "contactParty");
    $this->setClassCode($contactParty, "CON");
  }
}