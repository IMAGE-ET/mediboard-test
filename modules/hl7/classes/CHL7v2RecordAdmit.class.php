<?php /* $Id:$ */

/**
 * Record admit, message XML
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2RecordAdmit 
 * Record admit, message XML HL7
 */

class CHL7v2RecordAdmit extends CHL7v2MessageXML {
  function getContentNodes() {
    $data  = parent::getContentNodes();
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;
    $sender->loadConfigValues();
    
    $this->queryNodes("NK1", null, $data, true);
    
    $this->queryNodes("ROL", null, $data, true);    
    
    $PV1 = $this->queryNode("PV1", null, $data, true);

    $data["admitIdentifiers"] = $this->getAdmitIdentifiers($PV1, $sender);

    $this->queryNode("PV2", null, $data, true);
    
    $this->queryNode("ZBE", null, $data, true);
    
    $this->queryNode("ZFP", null, $data, true);
    
    $this->queryNode("ZFV", null, $data, true);
    
    $this->queryNode("ZFM", null, $data, true);
    
    $this->queryNode("ZFD", null, $data, true);
    
    return $data;
  }
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    $event_temp = $ack->event;

    $exchange_ihe = $this->_ref_exchange_ihe;
    $exchange_ihe->_ref_sender->loadConfigValues();
    $sender       = $exchange_ihe->_ref_sender;
  
    // Acquittement d'erreur : identifiants RI et NA non fournis
    if (!$data['admitIdentifiers']) {
      return $exchange_ihe->setAckAR($ack, "E200", null, $newPatient);
    }

    // Traitement du patient
    $hl7v2_record_person = new CHL7v2RecordPerson();
    $hl7v2_record_person->_ref_exchange_ihe = $exchange_ihe;
    $msg_ack = $hl7v2_record_person->handle($ack, $newPatient, $data);
    
    // Retour de l'acquittement si erreur sur le traitement du patient
    if ($exchange_ihe->statut_acquittement == "AR") {
      return $msg_ack;
    }
        
    // Traitement du séjour
    $ack = new CHL7v2Acknowledgment($event_temp);
    $ack->message_control_id = $data['identifiantMessage'];
    $ack->_ref_exchange_ihe  = $exchange_ihe;
   
    $newVenue = new CSejour();
    
    // Affectation du patient
    $newVenue->patient_id = $newPatient->_id; 
    // Affectation de l'établissement
    $newVenue->group_id = $sender->group_id;
    
    $function_handle = "handle$exchange_ihe->code";
    if (!method_exists($this, $function_handle)) {
      return $exchange_ihe->setAckAR($ack, "E006", null, $newVenue);
    }
    
    return $this->$function_handle($ack, $newVenue, $data); 
  } 
 
  function handleA01(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création possible
    
    return $this->handleA05($ack, $newVenue, $data);
  }
  
  function handleA02(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue)) {
      return $exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    // Récupérer données de la mutation
  }
  
  function handleA03(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue)) {
      return $exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    // Récupérer données de la sortie
  }
  
  function handleA04(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création possible
    
    return $this->handleA05($ack, $newVenue, $data);
  }
  
  function handleA05(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création possible
    
    // Traitement du message des erreurs
    $comment = $warning = "";
    $_modif_sejour = false; 
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $exchange_ihe->_ref_sender->loadConfigValues();
    $sender       = $exchange_ihe->_ref_sender;
    
    $venueRI       = CValue::read($data['admitIdentifiers'], "RI");
    $venueRISender = CValue::read($data['admitIdentifiers'], "RI_Sender");
    $venueNPA      = CValue::read($data['admitIdentifiers'], "NPA");
    $venueNA       = CValue::read($data['personIdentifiers'], "NA");
    
    $NDA = new CIdSante400();
    if ($venueNA) {
      $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $venueNA);
    }
    
    // NDA non connu (non fourni ou non retrouvé)
    if (!$venueNA || !$NDA->_id) {
      // NPA fourni
      if ($venueNPA) {
        /* @todo Gérer ce cas */
      }
      
      // RI fourni
      if ($venueRI) {
        // Recherche du séjour par son RI
        if ($newVenue->load($venueRI)) {
          $recoveredSejour = clone $newVenue;
          
          // Mapping du séjour
          $this->mappingVenue($data, $newVenue);
          
          // Le séjour retrouvé est-il différent que celui du message ?
          /* @todo voir comment faire (même patient, même praticien, même date ?) */
          
          // Notifier les autres destinataires autre que le sender
          $newVenue->_eai_initiateur_group_id = $sender->group_id;
          if ($msgVenue = $newVenue->store()) {
            return $exchange_ihe->setAckAR($ack, "E201", $msgPatient, $newVenue);
          }
                    
          $code_NDA      = "I221";
          $_modif_sejour = true; 
        }
        // Séjour non retrouvé par son RI
        else {
          $code_NDA = "I220";
        }  
      }
      else {
        // Aucun NDA fourni
        if (!$venueNA) {
          $code_NDA = "I225";
        } 
        // Association de l'IPP
        else {
          $code_NDA = "I222";
        }  
      }
      
      if (!$newVenue->_id) {
        // Mapping du patient
        $this->mappingVenue($data, $newVenue);
      
        // Séjour retrouvé ?
        if (CAppUI::conf("hl7 strictSejourMatch")) {
          // Recherche d'un num dossier déjà existant pour cette venue 
          if ($newVenue->loadMatchingSejour(null, true)) {                 
            $_code_NDA     = "A221";
            $_modif_sejour = true;
          }
        } else {
          $collision = $newVenue->getCollisions();

          if (count($collision) == 1) {
            $newVenue = reset($collision);
            
            $_code_NDA     = "A222";
            $_modif_sejour = true;
          }
        }
        
        // Mapping du séjour
        $newVenue = $this->mappingVenue($data, $newVenue);
        
        // Notifier les autres destinataires autre que le sender
        $newVenue->_eai_initiateur_group_id = $sender->group_id;
        if ($msgVenue = $newVenue->store()) {
          return $exchange_ihe->setAckAR($ack, "E201", $msgVenue, $newVenue);
        }
      }
      
      if ($msgNDA = CEAISejour::storeNDA($NDA, $newVenue, $sender)) {
        return $exchange_ihe->setAckAR($ack, "E202", $msgNDA, $newVenue);
      }
      
      $codes = array (($_modif_sejour ? "I202" : "I201"), $_code_NDA);
      
      $comment  = CEAISejour::getComment($newVenue);
      $comment .= CEAISejour::getComment($NDA);
    }
    // NDA connu
    else {
      $newVenue->load($NDA->object_id);
      
      $recoveredVenue = clone $newVenue;
          
      // Mapping de la venue
      $this->mappingVenue($data, $newVenue);
      
      // RI non fourni
      if (!$venueRI) {
        $code_IPP = "I223"; 
      } else {
        $tmpVenue = new CSejour();
        // RI connu
        if ($tmpVenue->load($venueRI)) {
          if ($tmpVenue->_id != $NDA->object_id) {
            $comment = "L'identifiant source fait référence au séjour : $NDA->object_id et l'identifiant cible au séjour : $tmpVenue->_id.";
            return $exchange_ihe->setAckAR($ack, "E201", $comment, $newVenue);
          }
          $code_NDA = "I224"; 
        }
        // RI non connu
        else {
          $code_NDA = "A220";
        }
      }
      
      // Notifier les autres destinataires autre que le sender
      $newVenue->_eai_initiateur_group_id = $sender->group_id;
      if ($msgVenue = $newVenue->store()) {
        return $exchange_ihe->setAckAR($ack, "E201", $msgVenue, $newVenue);
      }
            
      $codes = array ("I202", $code_NDA);
      
      $comment = CEAISejour::getComment($newVenue);
    }
    
    return $exchange_ihe->setAckAA($ack, $codes, $comment, $newVenue);
  }
  
  function handleA06(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue)) {
      return $exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
  }
  
  function handleA07(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue)) {
      return $exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
  }
  
  function handleA11(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue)) {
      return $exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
  }
  
  function handleA12(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue)) {
      return $exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
  }
  
  function handleA13(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
  }
  
  function handleA38(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue)) {
      return $exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
  }
  
  function handleA44(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue)) {
      return $exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
  }
  
  function handleA45(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
  }
  
  function handleA54(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue)) {
      return $exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
  }
  
  function handleA55(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue)) {
      return $exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
  }
  
  function handleZ99(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue)) {
      return $exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
  }
  
  function trashNDA(CSejour $newVenue, CInteropSender $sender) {
    return true;
  }
  
  function admitFound(CSejour $venue) {
    $exchange_ihe = $this->_ref_exchange_ihe;
    $exchange_ihe->_ref_sender->loadConfigValues();
    $sender       = $exchange_ihe->_ref_sender;
    
    $venueRI       = CValue::read($data['admitIdentifiers'], "RI");
    $venueRISender = CValue::read($data['admitIdentifiers'], "RI_Sender");
    $venueNPA      = CValue::read($data['admitIdentifiers'], "NPA");
    $venueNA       = CValue::read($data['personIdentifiers'], "NA");
    
    $NDA = new CIdSante400();
    if ($venueNA) {
      $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $venueNA);
    }
    
    if ($NDA->_id) {
      return true;
    }
    
    if ($newVenue->load($venueRI)) {
      return true;
    }
    
    return false;
  }
  
  function mappingVenue($data, CSejour $newVenue) {
    // Segment PV1
    $this->getPV1($data["PV1"], $newVenue);
    
    // Segment PV2
    $this->getPV2($data["PV2"], $newVenue);
    
    // Segment ZBE
    $this->getZBE($data["ZBE"], $newVenue);
    
    // Segment ZFD
    $this->getZFD($data["ZFD"], $newVenue);
    
    // Segment ZFM
    $this->getZFM($data["ZFM"], $newVenue);
    
    // Segment ZFP
    $this->getZFP($data["ZFP"], $newVenue);
    
    // Segment ZFV
    $this->getZFV($data["ZFV"], $newVenue);

    /* TODO Supprimer ceci après l'ajout des times picker */
    $newVenue->_hour_entree_prevue = null;
    $newVenue->_min_entree_prevue = null;
    $newVenue->_hour_sortie_prevue = null;
    $newVenue->_min_sortie_prevue = null;
    
    return $newVenue;
  }
  
  
  function getPV1(DOMNode $node, CSejour $newVenue) {    
    // Classe de patient
    $this->getPatientClass($node, $newVenue);

    // Hébergement du patient
    $this->getPL($node, $newVenue);
    
    // Type de l'admission
    $this->getAdmissionType($node, $newVenue);
  }
  
  function getPatientClass(DOMNode $node, CSejour $newVenue) {
    $patient_class = CHL7v2TableEntry::mapFrom("4", $this->queryTextNode("PV1.2", $node));
 
    $newVenue->type = $patient_class ? $patient_class : "comp";
  }
  
  function getPL(DOMNode $node, CSejour $newVenue) {
    
  }
  
  function getAdmissionType(DOMNode $node, CSejour $newVenue) {
    /* @todo Gérer par la suite avec les naissances */
  }
  
  function getMedecinResponsable(DOMNode $node, CSejour $newVenue) {
    $PV17 = $this->query("PID.7", $node);
    foreach ($PV17 as $_PV17) {
      
    }
  }
  
  function getMedecin() {
    
  }
  
  function getPV2(DOMNode $node, CSejour $newVenue) {    
    
  }
  
  function getZBE(DOMNode $node, CSejour $newVenue) {    
    
  }
  
  function getZFD(DOMNode $node, CSejour $newVenue) {    
    
  }
  
  function getZFM(DOMNode $node, CSejour $newVenue) {    
    
  }
  
  function getZFP(DOMNode $node, CSejour $newVenue) {    
    
  }
  
  function getZFV(DOMNode $node, CSejour $newVenue) {    
    
  }
}
?>