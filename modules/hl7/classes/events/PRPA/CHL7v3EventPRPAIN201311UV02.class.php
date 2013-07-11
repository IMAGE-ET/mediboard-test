<?php

/**
 * Patient Registry Get Demographics Query
 * A user initiates a query to a patient registry requesting demographic information for a specific patient
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v3EventPRPAIN201311UV02
 * Patient Registry Get Demographics Query
 */
class CHL7v3EventPRPAIN201311UV02 extends CHL7v3EventPRPA implements CHL7EventPRPAST201317UV02 {

  /** @var string */
  public $interaction_id = "IN201311UV02";

  /**
   * Get interaction
   *
   * @return string|void
   */
  function getInteractionID() {
    return "{$this->event_type}_{$this->interaction_id}";
  }

  /**
   * Build IN201307UV02 event
   *
   * @param CPatient $patient Person
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($patient) {
    parent::build($patient);

    $this->dom->dirschemaname = $this->getInteractionID();

    $this->addControlActProcess($patient);

    // Traitement final
    $this->dom->purgeEmptyElements();

    $this->message = $this->dom->saveXML();

    // Modification de l'échange
    $this->updateExchange();
  }

  /**
   * @see parent::addControlActProcess()
   */
  function addControlActProcess(CPatient $patient) {
    $dom = $this->dom;

    $controlActProcess = parent::addControlActProcess($patient);

    // reasonCode
    $reasonCode = $dom->addElement($controlActProcess, "reasonCode");
    $this->setCode($reasonCode, "CREA_RD", "1.2.250.1.213.1.1.4.11", "Création de dossier");

    // subject
    $subject = $dom->addElement($controlActProcess, "subject");
    $dom->addAttribute($subject, "contextConductionInd", "false");
    $this->setTypeCode($subject, "SUBJ");

    // registrationRequest
    $registrationRequest = $dom->addElement($subject, "registrationRequest");
    $this->setClassMoodCode($registrationRequest, "REG", "RQO");

    // statusCode
    $statusCode = $dom->addElement($registrationRequest, "statusCode");
    $dom->addAttribute($statusCode, "code", "active");

    // subject1
    $subject1 = $dom->addElement($registrationRequest, "subject1");
    $this->setTypeCode($subject1, "SBJ");

    // patient
    $patient_dom = $dom->addElement($subject1, "patient");
    $this->setClassCode($patient_dom, "PAT");

    $id = $dom->addElement($patient_dom, "id");
    $this->setII($id, $patient->INSC, "1.2.250.1.213.1.4.2");

    $statusCode = $dom->addElement($patient_dom, "statusCode");
    $dom->addAttribute($statusCode, "code", "active");

    // patientPerson
    $patientPerson = $dom->addElement($patient_dom, "patientPerson");
    $this->setClassDeterminerCode($patientPerson, "PSN", "INSTANCE");

    $this->addName($patientPerson, $patient);

    $this->addTelecom($patientPerson, $patient);

    $administrativeGenderCode = $dom->addElement($patientPerson, "administrativeGenderCode");
    $dom->addAttribute($administrativeGenderCode, "code", strtoupper($patient->sexe));

    $birthTime = $dom->addElement($patientPerson, "birthTime");
    $date = $this->getTimeToUtc($patient->_p_birth_date);
    $dom->addAttribute($birthTime, "value", $date);

    $this->addAdress($patientPerson, $patient);

    $this->addContactParty($patientPerson, $patient);

    $this->addBirthPlace($patientPerson, $patient);

    // providerOrganization
    $providerOrganization = $dom->addElement($patient_dom, "providerOrganization");
    $providerOrganization->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $providerOrganization->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:nil', 'true');
    $this->setClassDeterminerCode($providerOrganization, "ORG", "INSTANCE");

    // subjectOf
    $this->addSubjectOf(
      $patient_dom, "CONSENTEMENT_OUVERTURE_DMP", "1.2.250.1.213.4.1.2.3", "Consentement à l'ouverture du DMP", "true", true
    );

    // subjectOf
    $dmp_bris_de_glace = $patient->_dmp_bris_de_glace ? "true" : "false";
    $this->addSubjectOf(
      $patient_dom, "OPPOSITION_BRIS_DE_GLACE", "1.2.250.1.213.4.1.2.3", "opposition au mode bris de glace", $dmp_bris_de_glace
    );

    // subjectOf
    $dmp_acces_urgence = $patient->_dmp_acces_urgence ? "true" : "false";
    $this->addSubjectOf(
      $patient_dom, "OPPOSITION_ACCES_URGENCE", "1.2.250.1.213.4.1.2.3", "opposition à l'accès au mode urgence", $dmp_acces_urgence
    );

    // author
    $author = $dom->addElement($registrationRequest, "author");
    $this->setTypeCode($author, "AUT");

    // assignedEntity
    $assignedEntity = $dom->addElement($author, "assignedEntity");
    $this->setClassCode($assignedEntity, "ASSIGNED");

    $id = $dom->addElement($assignedEntity, "id");
    $mediuser = $patient->_dmp_mediuser;
    $this->setII($id, $mediuser->_common_name, "1.2.250.1.71.4.2.1");

    // assignedPerson
    $assignedPerson = $dom->addElement($assignedEntity, "assignedPerson");
    $this->setClassDeterminerCode($assignedPerson, "PSN", "INSTANCE");

    $name = $dom->addElement($assignedPerson, "name");
    $name->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $name->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:type', 'PN');
    $dom->addElement($name, "family", $mediuser->_p_last_name);
    $dom->addElement($name, "given", $mediuser->_p_first_name);

    // representedOrganization
    $group = $mediuser->loadRefFunction()->loadRefGroup();
    $this->addRepresentedOrganization($assignedEntity, $group);
  }
}