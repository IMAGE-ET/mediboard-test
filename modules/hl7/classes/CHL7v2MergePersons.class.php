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
  function getContentNodes() {
    $data = array();
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;
    $sender->loadConfigValues();

    foreach ($this->queryNodes("ADT_A40.PATIENT") as $_patient_group) {
      $sub_data["PID"] = $PID = $this->queryNode("PID", $_patient_group);
    
      $sub_data["personIdentifiers"] = $this->getPersonIdentifiers("PID.3", $PID, $sender);
      
      $sub_data["PD1"] = $this->queryNode("PD1", $_patient_group);
      
      $sub_data["MRG"] = $MRG = $this->queryNode("MRG", $_patient_group);
      
      $sub_data["personElimineIdentifiers"] = $this->getPersonIdentifiers("MRG.1", $MRG, $sender);  
      
      $data["merge"][] = $sub_data;
    }

    return $data;
  }
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    // Traitement du message des erreurs
    $comment = $warning = "";
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $exchange_ihe->_ref_sender->loadConfigValues();
    $sender       = $exchange_ihe->_ref_sender;
    
    foreach ($data["merge"] as $_data_merge) {
      $data = $_data_merge;

      $mbPatient        = new CPatient();
      $mbPatientElimine = new CPatient();
        
      $patientPI        = CValue::read($data['personIdentifiers'], "PI");  
      $patientRI        = CValue::read($data['personIdentifiers'], "RI");
      
      $patientEliminePI = CValue::read($data['personElimineIdentifiers'], "PI");
      $patientElimineRI = CValue::read($data['personElimineIdentifiers'], "RI");
     
      // Acquittement d'erreur : identifiants RI et PI non fournis
      if (!$patientRI && !$patientPI || 
          !$patientElimineRI && !$patientEliminePI) {
        return $exchange_ihe->setAckAR($ack, "E100", null, $newPatient);
      }
              
      $id400Patient = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientPI);
      if ($mbPatient->load($patientRI)) {
        if ($mbPatient->_id != $id400Patient->object_id) {
          $comment = "L'identifiant source fait référence au patient : $id400Patient->object_id et l'identifiant cible au patient : $mbPatient->_id.";
          return $exchange_ihe->setAckAR($ack, "E130", $comment, $newPatient);
        }
      } 
      if (!$mbPatient->_id) {
        $mbPatient->load($id400Patient->object_id);
      }
      
      $id400PatientElimine = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientEliminePI);
      if ($mbPatientElimine->load($patientElimineRI)) {
        if ($mbPatientElimine->_id != $id400PatientElimine->object_id) {
          $comment = "L'identifiant source fait référence au patient : $id400PatientElimine->object_id et l'identifiant cible au patient : $mbPatientElimine->_id.";
          return $exchange_ihe->setAckAR($ack, "E131", $comment, $newPatient);
        }
      }
      if (!$mbPatientElimine->_id) {
        $mbPatientElimine->load($id400PatientElimine->object_id);
      }
      
      if (!$mbPatient->_id || !$mbPatientElimine->_id) {
        $comment = !$mbPatient->_id ? "Le patient $mbPatient->_id est inconnu dans Mediboard." : "Le patient $mbPatientElimine->_id est inconnu dans Mediboard.";
        return $exchange_ihe->setAckAR($ack, "E120", $comment, $newPatient);
      }
  
      // Passage en trash de l'IPP du patient a éliminer
      $id400PatientElimine->tag = CAppUI::conf('dPpatients CPatient tag_ipp_trash').$sender->_tag_patient;
      $id400PatientElimine->store();
      
      $messages = array();
            
      $patientsElimine_array = array($mbPatientElimine);
      $first_patient_id = $mbPatient->_id;
  
      $checkMerge = $mbPatient->checkMerge($patientsElimine_array);
      // Erreur sur le check du merge
      if ($checkMerge) {
        $comment = "La fusion de ces deux patients n'est pas possible à cause des problèmes suivants : $checkMerge";
        return $exchange_ihe->setAckAR($ack, "E121", $comment, $newPatient);
      }

      $mbPatientElimine_id = $mbPatientElimine->_id;
      
      /** @todo mergePlainFields resets the _id */
      $mbPatient->_id = $first_patient_id;
      
      // Notifier les autres destinataires
      $mbPatient->_eai_initiateur_group_id = $sender->group_id;
      $mbPatient->_merging = CMbArray::pluck($patientsElimine_array, "_id");
      if ($msg = $mbPatient->merge($patientsElimine_array)) {
        return $exchange_ihe->setAckAR($ack, "E103", $msg, $mbPatient);
      }
  
      $mbPatient->_mbPatientElimine_id = $mbPatientElimine_id;
      
      $comment = CEAIPatient::getComment($mbPatient, $mbPatientElimine);
    }
    
    return $exchange_ihe->setAckAA($ack, "I103", $comment, $mbPatient);
  }
}

?>