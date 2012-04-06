<?php /* $Id:$ */

/**
 * Change patient identifier list, message XML
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2ChangePatientIdentifierList 
 * Change patient identifier list, message XML HL7
 */

class CHL7v2ChangePatientIdentifierList extends CHL7v2MessageXML {
  function getContentNodes() {
    $data = parent::getContentNodes();

    $this->queryNode("MRG", null, $data, true);
       
    return $data;
  }
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    // Traitement du message des erreurs
    $comment = $warning = "";
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;
    $sender->loadConfigValues();
   
    $this->_ref_sender = $sender;
    
    // Acquittement d'erreur : identifiants RI et PI non fournis
    if (!$data['personIdentifiers']) {
      return $exchange_ihe->setAckAR($ack, "E100", null, $newPatient);
    }
    
    $incorrect_identifier = null;
    $MRG_1 = $this->queryNode("MRG.1", $data["MRG"]);
    if (CHL7v2Message::$handle_mode == "simple") {
      /* @todo En mode simple, est-ce toujours le PI ? */
    } 
    else {
      if ($this->queryTextNode("CX.5", $MRG_1) == "PI") {
        $incorrect_identifier = $this->queryTextNode("CX.1", $MRG_1);
      }
    }
    
    // Chargement de l'IPP   
    $IPP_incorrect = new CIdSante400();
    if ($incorrect_identifier) {
      $IPP_incorrect = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $incorrect_identifier);
    }
    
    // PI non connu (non fourni ou non retrouvé)
    if (!$incorrect_identifier || !$IPP_incorrect->_id) {
      return $exchange_ihe->setAckAR($ack, "E141", null, $newPatient);
    }
    
    $newPatient->load($IPP_incorrect->object_id);

    // Passage en trash de l'IPP du patient a éliminer
    $IPP_incorrect->tag = CAppUI::conf('dPpatients CPatient tag_ipp_trash').$sender->_tag_patient;
    $IPP_incorrect->last_update = mbDateTime();
    if ($msg = $IPP_incorrect->store()) {
      return $exchange_ihe->setAckAR($ack, "E140", $msg, $newPatient);
    }  
    
    // Sauvegarde du nouvel IPP
    $IPP = new CIdSante400();
    $IPP->object_id    = $newPatient->_id;
    $IPP->object_class = "CPatient";
    $IPP->id400        = $data['personIdentifiers']["PI"];
    $IPP->tag          = $sender->_tag_patient;
    $IPP->last_update  = mbDateTime();
    
    if ($msg = $IPP->store()) {
      return $exchange_ihe->setAckAR($ack, "E140", $msg, $newPatient);
    }  
    
    return $exchange_ihe->setAckAA($ack, "I140", null, $newPatient);
  }
}

?>