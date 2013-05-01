<?php
/**
 * $Id: CHL7v2MoveAccountInformation.class.php 18255 2013-02-28 12:02:26Z lryo $
 *
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 18255 $
 */

/**
 * Class CHL7v2MoveAccountInformation
 * Move account information, message XML HL7
 */
class CHL7v2MoveAccountInformation extends CHL7v2MessageXML {
  static $event_codes = array ("A44");

  /**
   * Get data nodes
   *
   * @return array Get nodes
   */
  function getContentNodes() {
    $data = array();

    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;
    $sender->loadConfigValues();

    foreach ($this->queryNodes("ADT_A44.PATIENT") as $_patient_group) {
      $sub_data["PID"] = $PID = $this->queryNode("PID", $_patient_group);

      $sub_data["personIdentifiers"] = $this->getPersonIdentifiers("PID.3", $PID, $sender);
      $sub_data["admitIdentifiers"]  = $this->getAdmitIdentifiers($PID, $sender);

      $sub_data["PD1"] = $this->queryNode("PD1", $_patient_group);

      $sub_data["MRG"] = $MRG = $this->queryNode("MRG", $_patient_group);

      $sub_data["personChangeIdentifiers"] = $this->getPersonIdentifiers("MRG.1", $MRG, $sender);
      $sub_data["admitChangeIdentifiers"]  = $this->getAdmitIdentifiers($MRG, $sender);

      $data["merge"][] = $sub_data;
    }

    return $data;
  }

  /**
   * Handle event
   *
   * @param CHL7Acknowledgment $ack        Acknowledgement
   * @param CPatient           $newPatient Person
   * @param array              $data       Nodes data
   *
   * @return null|string
   */
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    // Traitement du message des erreurs
    $comment = "";

    $exchange_ihe = $this->_ref_exchange_ihe;
    $exchange_ihe->_ref_sender->loadConfigValues();
    $sender       = $exchange_ihe->_ref_sender;

    // Impossibilité dans Mediboard de modifier le patient d'un séjour
    if (CAppUI::conf("dPplanningOp CSejour patient_id") == 0) {
      return $exchange_ihe->setAckAR($ack, "E600", null, $newPatient);
    }

    $venue = new CSejour();

    // On considère que l'on a qu'un changement à faire
    $data  = $data["merge"][0];

    $mbPatient       = new CPatient();
    $mbPatientChange = new CPatient();

    $patientPI        = CValue::read($data['personIdentifiers'], "PI");
    $patientRI        = CValue::read($data['personIdentifiers'], "RI");

    $patientChangePI = CValue::read($data['personChangeIdentifiers'], "PI");
    $patientChangeRI = CValue::read($data['personChangeIdentifiers'], "RI");

    // Acquittement d'erreur : identifiants RI et PI non fournis
    if (!$patientRI && !$patientPI || !$patientChangeRI && !$patientChangePI) {
      return $exchange_ihe->setAckAR($ack, "E100", null, $newPatient);
    }

    $idexPatient = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientPI);
    if ($mbPatient->load($patientRI)) {
      if ($mbPatient->_id != $idexPatient->object_id) {
        $comment  = "L'identifiant source fait référence au patient : $idexPatient->object_id";
        $comment .= " et l'identifiant cible au patient : $mbPatient->_id.";
        return $exchange_ihe->setAckAR($ack, "E601", $comment, $newPatient);
      }
    }
    if (!$mbPatient->_id) {
      $mbPatient->load($idexPatient->object_id);
    }

    $idexPatientChange = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientChangePI);
    if ($mbPatientChange->load($patientChangeRI)) {
      if ($mbPatientChange->_id != $idexPatientChange->object_id) {
        $comment  = "L'identifiant source fait référence au patient : $idexPatientChange->object_id";
        $comment .= "et l'identifiant cible au patient : $mbPatientChange->_id.";
        return $exchange_ihe->setAckAR($ack, "E602", $comment, $newPatient);
      }
    }
    if (!$mbPatientChange->_id) {
      $mbPatientChange->load($idexPatientChange->object_id);
    }

    if (!$mbPatient->_id || !$mbPatientChange->_id) {
      $comment = !$mbPatient->_id ?
        "Le patient $mbPatient->_id est inconnu dans Mediboard." : "Le patient $mbPatientChange->_id est inconnu dans Mediboard.";
      return $exchange_ihe->setAckAR($ack, "E603", $comment, $newPatient);
    }

    $venueAN = $this->getVenueAN($sender, $data);
    $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $venueAN);

    if (!$venueAN && !$NDA->_id) {
      return $exchange_ihe->setAckAR($ack, "E604", $comment, $mbPatient);
    }

    $venue->load($NDA->object_id);

    // Impossibilité dans Mediboard de modifier le patient d'un séjour ayant une entrée réelle
    if (CAppUI::conf("dPplanningOp CSejour patient_id") == 2 && $venue->entree_reelle) {
      return $exchange_ihe->setAckAR($ack, "E605", null, $venue);
    }

    if ($venue->patient_id != $mbPatientChange->_id) {
      return $exchange_ihe->setAckAR($ack, "E606", null, $venue);
    }

    $venue->patient_id = $mbPatient->_id;
    if ($msg = $venue->store()) {
      return $exchange_ihe->setAckAR($ack, "E607", $msg, $venue);
    }

    $comment = CEAISejour::getComment($venue);

    return $exchange_ihe->setAckAA($ack, "I600", $comment, $venue);
  }
}
