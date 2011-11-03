<?php /* $Id:$ */

/**
 * Merge persons, message XML
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2MergePersons 
 * Merge persons, message XML HL7
 */

class CHL7v2MergePersons extends CHL7v2MessageXML {
  function getContentsXML() {
    $data  = array();
    $xpath = new CHL7v2MessageXPath($this);
    
    $data["PID"] = $PID = $xpath->queryUniqueNode("//PID");
    
    $data["patientIdentifiers"] = $this->getPatientIdentifiers($PID);
    
    $data["PD1"] = $xpath->queryUniqueNode("//PD1");
    
    $data["MRG"] = $MRG = $xpath->queryUniqueNode("//MRG");
    
    $data["patientElmineIdentifiers"] = $this->getPatientIdentifiers($MRG);
    
    return $data;
  }
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    // Traitement du message des erreurs
    $comment = $warning = "";
    
    $mbPatient        = new CPatient();
    $mbPatientElimine = new CPatient();
   
    $exchange_ihe = $this->_ref_exchange_ihe;
    $exchange_ihe->_ref_sender->loadConfigValues();
    $sender       = $exchange_ihe->_ref_sender;
    
    $patientRI = CValue::read($data['patientIdentifiers'], "RI");
    $patientPI = CValue::read($data['patientIdentifiers'], "PI");
    
    $patientElimineRI = CValue::read($data['patientElimineIdentifiers'], "RI");
    $patientEliminePI = CValue::read($data['patientElimineIdentifiers'], "PI");
    
    // Acquittement d'erreur : identifiants RI et PI non fournis
    if (!$patientRI && !$patientPI && 
        !$patientElimineRI && !$patientEliminePI) {
      return $exchange_ihe->setAckAR($ack, "E003", null, $newPatient);
    }
            
    $id400Patient = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientRI);
    if ($mbPatient->load($patientPI)) {
      if ($mbPatient->_id != $id400Patient->object_id) {
        $comment = "L'identifiant source fait r�f�rence au patient : $id400Patient->object_id et l'identifiant cible au patient : $mbPatient->_id.";
        return $exchange_ihe->setAckAR($ack, "E004", $comment, $newPatient);
      }
    } 
    if (!$mbPatient->_id) {
      $mbPatient->load($id400Patient->object_id);
    }
    
    $id400PatientElimine = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $data['idSourcePatientElimine']);
    if ($mbPatientElimine->load($data['idCiblePatientElimine'])) {
      if ($mbPatientElimine->_id != $id400PatientElimine->object_id) {
        $comment = "L'identifiant source fait r�f�rence au patient : $id400PatientElimine->object_id et l'identifiant cible au patient : $mbPatientElimine->_id.";
        return $exchange_ihe->setAckAR($ack, "E041", $comment, $newPatient);
      }
    }
    if (!$mbPatientElimine->_id) {
      $mbPatientElimine->load($id400PatientElimine->object_id);
    }
    
    if (!$mbPatient->_id || !$mbPatientElimine->_id) {
      $comment = !$mbPatient->_id ? "Le patient $mbPatient->_id est inconnu dans Mediboard." : "Le patient $mbPatientElimine->_id est inconnu dans Mediboard.";
      return $exchange_ihe->setAckAR($ack, "E012", $comment, $newPatient);
    }

    // Passage en trash de l'IPP du patient a �liminer
    $id400PatientElimine->tag = CAppUI::conf('dPpatients CPatient tag_ipp_trash').$sender->_tag_patient;
    $id400PatientElimine->store();
    
    $messages = array();
          
    $patientsElimine_array = array($mbPatientElimine);
    $first_patient_id = $mbPatient->_id;

    $checkMerge = $mbPatient->checkMerge($patientsElimine_array);
    // Erreur sur le check du merge
    if ($checkMerge) {
      $comment = "La fusion de ces deux patients n'est pas possible � cause des probl�mes suivants : $checkMerge";
      return $exchange_ihe->setAckAR($ack, "E010", $comment, $newPatient);
    }
    
    if ($msg = $mbPatient->mergePlainFields($patientsElimine_array)) {
      $comment = "La fusion des donn�es des patients a �chou� : $msg";
      return $exchange_ihe->setAckAR($ack, "E011", $comment, $newPatient);
    }
    
    $mbPatientElimine_id = $mbPatientElimine->_id;
    
    /** @todo mergePlainFields resets the _id */
    $mbPatient->_id = $first_patient_id;
    
    // Notifier les autres destinataires
    $mbPatient->_eai_initiateur_group_id = $sender->group_id;
    $mbPatient->_merging = CMbArray::pluck($patientsElimine_array, "_id");
    $msg = $mbPatient->merge($patientsElimine_array);
    
    $codes = array ($msg ? "A010" : "I010");
      
    if ($msg) {
      $avertissement = $msg." ";
    } else {
      $comment = "Le patient $mbPatient->_id a �t� fusionn� avec le patient $mbPatientElimine_id.";
    }
    
    return $exchange_ihe->setAckAA($ack, $codes, $avertissement, $comment, $newPatient);
  }
}

?>