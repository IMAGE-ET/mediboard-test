<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CHL7v2RecordAdmit 
 * Record admit, message XML HL7
 */
class CHL7v2RecordAdmit extends CHL7v2MessageXML {
  static $event_codes = "A01 A02 A03 A04 A05 A06 A07 A08 A11 A12 A13 A14 A16 A25 A38 A44 A54 A55 Z80 Z81 Z84 Z85 Z99";
  
  var $_object_found_by_vn = null;

  /**
   * Get data nodes
   *
   * @return array Get nodes
   */
  function getContentNodes() {
    $data  = parent::getContentNodes();

    $sender = $this->_ref_sender;
    
    $this->queryNodes("NK1", null, $data, true);
    
    $this->queryNodes("ROL", null, $data, true);    
    
    $PV1 = $this->queryNode("PV1", null, $data, true);

    $data["admitIdentifiers"] = $this->getAdmitIdentifiers($PV1, $sender);
    
    $this->queryNode("PV2", null, $data, true);
    
    // Traitement des segments spécifiques extension française PAM
    if ($this->_is_i18n == "FR") {
      $this->queryNode("ZBE", null, $data, true);
    
      $this->queryNode("ZFP", null, $data, true);
      
      $this->queryNode("ZFV", null, $data, true);
      
      $this->queryNode("ZFM", null, $data, true);
      
      $this->queryNode("ZFD", null, $data, true);
    }
    
    $this->queryNodes("OBX", null, $data, true);
    
    $this->queryNodes("GT1", null, $data, true);
    
    return $data;
  }
  
  function getVenueAN($sender, $data) {
    switch ($sender->_configs["handle_NDA"]) {
      case 'PV1_19':
        return CValue::read($data['admitIdentifiers'], "AN");
      default :
        return CValue::read($data['personIdentifiers'], "AN");
    }
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
    $event_temp = $ack->event;

    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;
    $sender->loadConfigValues();

    $this->_ref_sender = $sender;

    // Acquittement d'erreur : identifiants RI et NA, VN non fournis
    if (!$data['admitIdentifiers'] && !$this->getVenueAN($sender, $data)) {
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
    $newVenue->loadRefPatient();
    
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
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    // Récupérer données de la mutation
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA03(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    // Récupérer données de la sortie
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
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
    $sender       = $this->_ref_sender;
    
    $venueRI       = CValue::read($data['admitIdentifiers'], "RI");
    $venueRISender = CValue::read($data['admitIdentifiers'], "RI_Sender");
    $venueNPA      = CValue::read($data['admitIdentifiers'], "NPA");
    $venueVN       = CValue::read($data['admitIdentifiers'], "VN");
    $venueAN       = $this->getVenueAN($sender, $data);

    $NDA = new CIdSante400();
    
    $sender_purge_idex_movements = $sender->_configs["purge_idex_movements"];
    if ($venueAN) {
      $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $venueAN);
    }
  
    // NDA non connu (non fourni ou non retrouvé)
    if (!$NDA->_id) {
      // Aucun NDA fourni / Association du NDA
      $code_NDA = !$venueAN ? "I225" : "I222";
      
      $found = false;
      
      // NPA fourni
      if (!$found && $venueNPA) {
        /* @todo Gérer ce cas */
      }
      
      // VN fourni
      if (!$found && $venueVN && !$sender_purge_idex_movements) {
        // Le champ PV1.2 conditionne le remplissage et l'interprétation de PV1.19
        $this->getSejourByVisitNumber($newVenue, $data);
        if ($newVenue->_id) {
          $found = true;
          
          // Mapping du séjour
          $this->mappingVenue($data, $newVenue);
          
          // Notifier les autres destinataires autre que le sender
          $newVenue->_eai_initiateur_group_id = $sender->group_id;
          // Pas de génération de NDA
          $newVenue->_generate_NDA = false;
          // On ne check pas la cohérence des dates des consults/intervs
          $newVenue->_skip_date_consistencies = true;
          if ($msgVenue = $newVenue->store()) {
            if ($newVenue->_collisions) {
              return $exchange_ihe->setAckAR($ack, "E213", $msgVenue, reset($newVenue->_collisions));
            }
            
            return $exchange_ihe->setAckAR($ack, "E201", $msgVenue, $newVenue);
          }
                    
          $code_NDA      = "A222";
          $_modif_sejour = true;
        }
      }
      
      // RI fourni
      if (!$found && $venueRI) {
        // Recherche du séjour par son RI
        if ($newVenue->load($venueRI)) {
          $recoveredSejour = clone $newVenue;
          
          // Mapping du séjour
          $this->mappingVenue($data, $newVenue);
          
          // Le séjour retrouvé est-il différent que celui du message ?
          /* @todo voir comment faire (même patient, même praticien, même date ?) */
          
          // Notifier les autres destinataires autre que le sender
          $newVenue->_eai_initiateur_group_id = $sender->group_id;
          // Pas de génération de NDA
          $newVenue->_generate_NDA = false;
          // On ne check pas la cohérence des dates des consults/intervs
          $newVenue->_skip_date_consistencies = true;
          if ($msgVenue = $newVenue->store()) {
            if ($newVenue->_collisions) {
              return $exchange_ihe->setAckAR($ack, "E213", $msgVenue, reset($newVenue->_collisions));
            }
            
            return $exchange_ihe->setAckAR($ack, "E201", $msgVenue, $newVenue);
          }
                    
          $code_NDA      = "I221";
          $_modif_sejour = true; 
        }
        // Séjour non retrouvé par son RI
        else {
          $code_NDA = "I220";
        }  
      }
      
      if (!$newVenue->_id) {
        // Mapping du séjour
        $this->mappingVenue($data, $newVenue);

        // Séjour retrouvé ?
        if (CAppUI::conf("hl7 strictSejourMatch")) {
          // Recherche d'un num dossier déjà existant pour cette venue 
          if ($newVenue->loadMatchingSejour(null, true, false)) {                 
            $code_NDA     = "A221";
            $_modif_sejour = true;
          }
        } 
        else {
          // Valuer "entree" et "sortie" 
          $newVenue->updatePlainFields();
          
          $collision = $newVenue->getCollisions();

          if (count($collision) == 1) {
            $newVenue = reset($collision);

            $code_NDA     = "A222";
            $_modif_sejour = true;
          }
        }

        // Mapping du séjour
        $newVenue = $this->mappingVenue($data, $newVenue);
        
        // Notifier les autres destinataires autre que le sender
        $newVenue->_eai_initiateur_group_id = $sender->group_id;
        // Pas de génération de NDA
        $newVenue->_generate_NDA = false;
        // On ne check pas la cohérence des dates des consults/intervs
        $newVenue->_skip_date_consistencies = true;
        if ($msgVenue = $newVenue->store()) {
          if ($newVenue->_collisions) {
            return $exchange_ihe->setAckAR($ack, "E213", $msgVenue, reset($newVenue->_collisions));
          }
          
          return $exchange_ihe->setAckAR($ack, "E201", $msgVenue, $newVenue);
        }
      }
      
      if ($msgNDA = CEAISejour::storeNDA($NDA, $newVenue, $sender)) {
        return $exchange_ihe->setAckAR($ack, "E202", $msgNDA, $newVenue);
      }
      
      // Création du VN, voir de l'objet
      if ($msgVN = $this->createObjectByVisitNumber($newVenue, $data)) {
        return $exchange_ihe->setAckAR($ack, "E210", $msgVN, $newVenue);
      }
      
      $codes = array (($_modif_sejour ? "I202" : "I201"), $code_NDA);
      
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
        $code_NDA = "I223"; 
      }
      else {
        $tmpVenue = new CSejour();
        // RI connu
        if ($tmpVenue->load($venueRI)) {
          if ($tmpVenue->_id != $NDA->object_id) {
            $comment = "L'identifiant source fait référence au séjour : $NDA->object_id et l'identifiant cible au séjour : $tmpVenue->_id.";
            
            return $exchange_ihe->setAckAR($ack, "E230", $comment, $newVenue);
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
      // On ne check pas la cohérence des dates des consults/intervs
      $newVenue->_skip_date_consistencies = true;
      if ($msgVenue = $newVenue->store()) {
        if ($newVenue->_collisions) {
          return $exchange_ihe->setAckAR($ack, "E213", $msgVenue, reset($newVenue->_collisions));
        }
        
        return $exchange_ihe->setAckAR($ack, "E201", $msgVenue, $newVenue);
      }
      
      // Création du VN, voir de l'objet
      if ($msgVN = $this->createObjectByVisitNumber($newVenue, $data)) {
        return $exchange_ihe->setAckAR($ack, "E210", $msgVN, $newVenue);
      }
            
      $codes = array ("I202", $code_NDA);
      
      $comment = CEAISejour::getComment($newVenue);
    }
    
    // Mapping du mouvement
    if ($sender_purge_idex_movements) {
      // On recherche un mouvement de l'event (A05/A01/A04)
      $movement                        = new CMovement();
      $movement->sejour_id             = $newVenue->_id;
      $movement->original_trigger_code = $this->_ref_exchange_ihe->code;
      $movement->cancel                = 0;
      $movement->loadMatchingObject();
      
      // Si on a un mouvement alors on annule tous les autres
      if ($movement->_id) {
        foreach ($newVenue->loadRefsMovements() as $_movement) {
          // On passe en trash l'idex associé
          $_movement->loadLastId400();
          $last_id400 = $_movement->_ref_last_id400;
          if ($last_id400->_id) {
            $last_id400->tag = "trash_".$last_id400->tag;
            $last_id400->last_update = mbDateTime();
            $last_id400->store();
          }
          
          // On annule le mouvement
          $_movement->cancel = 1;
          $_movement->store();
        }
      }      
    }
    
    $return_movement = $this->mapAndStoreMovement($ack, $newVenue, $data);
    if (is_string($return_movement)) {
      return $exchange_ihe->setAckAR($ack, "E206", $return_movement, $newVenue);
    }
    $movement = $return_movement;
    
    // Mapping de l'affectation
    $return_affectation = $this->mapAndStoreAffectation($ack, $newVenue, $data, $return_movement);
    if (is_string($return_affectation)) {
      return $exchange_ihe->setAckAR($ack, "E208", $return_affectation, $newVenue);
    }
    $affectation = $return_affectation;
    
    // Affectation de l'affectation au mouvement
    if ($movement && $affectation && $affectation->_id) {
      $movement->affectation_id = $affectation->_id;
      $movement->store();
    }
    
    // Dans le cas d'une grossesse
    if ($return_grossesse = $this->storeGrossesse($newVenue, $data)) {
      return $exchange_ihe->setAckAR($ack, "E211", $return_grossesse, $newVenue);
    }
    
    // Dans le cas d'une naissance
    if ($return_naissance = $this->mapAndStoreNaissance($newVenue, $data)) {
      return $exchange_ihe->setAckAR($ack, "E212", $return_naissance, $newVenue);
    }
    
    return $exchange_ihe->setAckAA($ack, $codes, $comment, $newVenue);
  }
  
  function handleA06(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA07(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA08(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA11(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    // Suppression de l'entrée réelle / mode d'entrée
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA12(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    // Suppression de l'affectation
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA13(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    // Suppression sortie réelle, mode de sortie, ...
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA14(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA16(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA25(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA38(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA44(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA54(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA55(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleZ80(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleZ81(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleZ84(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleZ85(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleZ99(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - création impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function trashNDA(CSejour $newVenue, CInteropSender $sender) {
    return true;
  }
  
  function getSejourByVisitNumber(CSejour $newVenue, $data) {
    $sender  = $this->_ref_sender;
    $venueVN = CValue::read($data['admitIdentifiers'], "VN");
        
    $where = $ljoin = array();
    $where["id_sante400.tag"]   = " = '$sender->_tag_visit_number'";
    $where["id_sante400.id400"] = " = '$venueVN'";
           
    switch ($this->queryTextNode("PV1.2", $data["PV1"])) {
      // Identifie la venue pour actes et consultation externe
      case 'O':
        $consultation = new CConsultation();
                
        $ljoin["id_sante400"]              = "id_sante400.object_id = consultation.consultation_id";
        $where["id_sante400.object_class"] = " = 'CConsultation'";
        $where["consultation.type"]        = " != 'chimio'";
        
        $consultation->loadObject($where, null, null, $ljoin);
        // Nécessaire pour savoir quel objet créé en cas de besoin
        $this->_object_found_by_vn = $consultation;
        
        if (!$consultation->_id) {
          return false;
        }
        
        $newVenue->load($consultation->sejour_id);
        
        return true;
      // Identifie une séance 
      case 'R':
        $consultation = new CConsultation();
                
        $ljoin["id_sante400"]              = "id_sante400.object_id = consultation.consultation_id";
        $where["id_sante400.object_class"] = " = 'CConsultation'"; 
        $where["consultation.type"]        = " = 'chimio'";
        
        $consultation->loadObject($where, null, null, $ljoin);
        // Nécessaire pour savoir quel objet créé en cas de besoin
        $this->_object_found_by_vn = $consultation;
        
        if (!$consultation->_id) {
          return false;
        }
        
        $newVenue->load($consultation->sejour_id);
        
        return true;
      // Identifie le n° de passage aux urgences
      case 'E':
        $rpu = new CRPU();
        
        $ljoin["id_sante400"]              = "id_sante400.object_id = rpu.rpu_id";
        $where["id_sante400.object_class"] = " = 'CRPU'"; 
        
        $rpu->loadObject($where, null, null, $ljoin);
        // Nécessaire pour savoir quel objet créé en cas de besoin
        $this->_object_found_by_vn = $rpu;
        
        if (!$rpu->_id) {
          return false;
        }
        
        $newVenue->load($rpu->sejour_id);
        
        return true;
      // Identifie le séjour ou hospitalisation à domicile
      default:      
        $idexVisitNumber = CIdSante400::getMatch("CSejour", $sender->_tag_visit_number, $venueVN);  
        $this->_object_found_by_vn = $newVenue;
        if (!$idexVisitNumber->_id) {
          return false;
        }
        
        $newVenue->load($idexVisitNumber->object_id);
        $this->_object_found_by_vn = $newVenue;
        
        return true;
    }
    
    return false;
  }
  
  function createObjectByVisitNumber(CSejour $newVenue, $data) {
    $venueVN = CValue::read($data['admitIdentifiers'], "VN");
    if (!$venueVN) {
      return;
    }
    
    $this->getSejourByVisitNumber($newVenue, $data);
    if (!$this->_object_found_by_vn) {
      return; 
    }
    
    $object_found_by_vn = $this->_object_found_by_vn;
    // Création de l'objet ? 
    if (!$object_found_by_vn->_id) {
      if (!CAppUI::conf("smp create_object_by_vn")) {
        return;
      }
            
      $where = array();
      $where["sejour_id"] = " = '$newVenue->_id'";
      $object_found_by_vn->sejour_id = $newVenue->_id;

      // On va rechercher l'objet en fonction de son type, où le créer
      switch ($this->queryTextNode("PV1.2", $data["PV1"])) {
        // Identifie la venue pour actes et consultation externe (CConsultation && type != chimio)
        case 'O':
          $where["type"] = " != 'chimio'";
          break;
        // Identifie une séance (CConsultation && type == chimio)
        case 'R':
          $where["type"] = " = 'chimio'";
          $object_found_by_vn->type = "chimio";
          break;
        // Identifie le n° de passage aux urgences
        case 'E':
          $object_found_by_vn->_patient_id = $newVenue->patient_id;
          $object_found_by_vn->_entree     = $newVenue->entree;
          $object_found_by_vn->_group_id   = $newVenue->group_id;
          
          break;
      }  
      
      $count_list = $object_found_by_vn->countList($where);
      if ($count_list > 1) {
        /* @todo voir comment gérer ceci ! */
        return;
      }
      
      if ($object_found_by_vn instanceof CConsultation) {        
        $datetime = $this->queryTextNode("EVN.6/TS.1", $data["EVN"]);
        
        if ($data["PV2"]) {
          $object_found_by_vn->motif = $this->queryTextNode("PV2.12", $data["PV2"]);
        }
        
        // Création de la consultation
        if ($msg = $object_found_by_vn->createByDatetime($datetime, $newVenue->praticien_id, $newVenue->patient_id)) {
          return $msg;
        }
      }
      
      // Dans le cas où l'on doit créer l'objet
      if (!$object_found_by_vn->_id) {
        if ($msg = $object_found_by_vn->store()) {
          return $msg;
        }
      }
    }
    
    // On affecte le VN
    $sender       = $this->_ref_sender;
    $object_class = $object_found_by_vn->_class;
    $object_id    = $object_found_by_vn->_id;
      
    $idexVN = CIdSante400::getMatch($object_class, $sender->_tag_visit_number, $venueVN, $object_id);
    // L'idex est déjà associé sur notre objet
    if ($idexVN->_id) {
      return;
    }
    
    // Création de l'idex
    $idexVN->last_update = mbDateTime();

    return $idexVN->store();
  } 
  
  function admitFound(CSejour $newVenue, $data) {
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $this->_ref_sender;
    
    $venueRI       = CValue::read($data['admitIdentifiers'], "RI");
    $venueRISender = CValue::read($data['admitIdentifiers'], "RI_Sender");
    $venueVN       = CValue::read($data['admitIdentifiers'], "VN");
    $venueNPA      = CValue::read($data['admitIdentifiers'], "NPA");
    $venueAN       = $this->getVenueAN($sender, $data);
    
    $NDA = new CIdSante400();
    if ($venueAN) {
      $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $venueAN);
    }
    
    if ($NDA->_id) {
      $newVenue->load($NDA->object_id);
      
      return true;
    }

    if ($newVenue->load($venueRI)) {
      return true;
    }
    
    if ($venueVN) {
      return $this->getSejourByVisitNumber($newVenue, $data);
    }
    
    return false;
  }
  
  function mapAndStoreVenue(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $this->_ref_sender;
    
    // Mapping du séjour
    $this->mappingVenue($data, $newVenue);
    
    // Notifier les autres destinataires autre que le sender
    $newVenue->_eai_initiateur_group_id = $sender->group_id;
    // On ne check pas la cohérence des dates des consults/intervs
    $newVenue->_skip_date_consistencies = true;
    if ($msgVenue = $newVenue->store()) {
      return $exchange_ihe->setAckAR($ack, "E201", $msgVenue, $newVenue);
    }
    
    // Mapping du mouvement
    $return_movement = $this->mapAndStoreMovement($ack, $newVenue, $data);
    if (is_string($return_movement)) {
      return $exchange_ihe->setAckAR($ack, "E206", $return_movement, $newVenue);
    }
    $movement = $return_movement;
    
    // Mapping de l'affectation
    $return_affectation = $this->mapAndStoreAffectation($ack, $newVenue, $data, $movement);
    if (is_string($return_affectation)) {
      return $exchange_ihe->setAckAR($ack, "E208", $return_affectation, $newVenue);
    }
    $affectation = $return_affectation;
    
    // Attribution de l'affectation au mouvement
    if ($movement && $affectation && $affectation->_id) {
      $movement->affectation_id = $affectation->_id;
      $movement->store();
    }
    
    // Dans le cas d'une grossesse
    if ($return_grossesse = $this->storeGrossesse($newVenue, $data)) {
      return $exchange_ihe->setAckAR($ack, "E211", $return_grossesse, $newVenue);
    }
    
    // Création du VN, voir de l'objet
    if ($msgVN = $this->createObjectByVisitNumber($newVenue, $data)) {
      return $exchange_ihe->setAckAR($ack, "E210", $msgVN, $newVenue);
    }
    
    $codes   = array ("I202", "I226");
    $comment = CEAISejour::getComment($newVenue);
    
    return $exchange_ihe->setAckAA($ack, $codes, $comment, $newVenue);
  }
  
  function mappingVenue($data, CSejour $newVenue) {
    $event_code = $this->_ref_exchange_ihe->code;
    // Cas spécifique de certains segments 
    // A38 : Annulation du séjour
    if ($event_code == "A38") {
      $newVenue->annule = 1;
    }
    
    // Segment PV1
    $this->getSegment("PV1", $data, $newVenue);
    
    // Segment PV2
    $this->getSegment("PV2", $data, $newVenue);
    
    // Segment ZFD
    $this->getSegment("ZFD", $data, $newVenue);
    
    // Segment ZFM
    $this->getSegment("ZFM", $data, $newVenue);
    
    // Segment ZFP
    $this->getSegment("ZFP", $data, $newVenue);
    
    // Segment ZFV
    $this->getSegment("ZFV", $data, $newVenue);

    // Débiteurs
    if (array_key_exists("GT1", $data)) {
      foreach ($data["GT1"] as $_GT1) {
        $this->getGT1($_GT1, $newVenue);
      }
    }
    
    // Constantes
    if (array_key_exists("OBX", $data)) {
      foreach ($data["OBX"] as $_OBX) {
        $this->getOBX($_OBX, $newVenue, $data);
      }
    }

    /* TODO Supprimer ceci après l'ajout des times picker */
    $newVenue->_hour_entree_prevue = null;
    $newVenue->_min_entree_prevue = null;
    $newVenue->_hour_sortie_prevue = null;
    $newVenue->_min_sortie_prevue = null;
    
    return $newVenue;
  }
  
  function mapAndStoreMovement(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    if (!array_key_exists("ZBE", $data)) {
      return;
    }
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    
    $movement = new CMovement();
    if (!$movement = $this->mappingMovement($data, $newVenue, $movement)) {
      return $exchange_ihe->setAckAR($ack, "E206", null, $newVenue);
    }

    return $movement;
  }
  
  function mapAndStoreAffectation(CHL7Acknowledgment $ack, CSejour $newVenue, $data, CMovement $movement = null) {
    $PV1_3 = $this->queryNode("PV1.3", $data["PV1"]);
        
    $affectation = new CAffectation();
    $affectation->sejour_id = $newVenue->_id;
    
    // Récupération de la date de réalisation de l'évènement
    $datetime = $this->queryTextNode("EVN.6/TS.1", $data["EVN"]);
    
    $event_code = $this->_ref_exchange_ihe->code;

    switch ($event_code) {
      // Cas d'une suppression de mutation
      case "A12" :
        if (!$movement) {
          return;
        }

        $affectation->load($movement->affectation_id);
        if (!$affectation->_id) {
          return "Le mouvement '$movement->_id' n'est pas lié à une affectation dans Mediboard";
        }

        // Pas de synchronisation
        $affectation->_no_synchro = true;
        if ($msgAffectation = $affectation->delete()) {
          return $msgAffectation;
        }

        return null;

      // Annulation admission
      case "A11" :
        if (!$movement) {
          return;
        }

        $affectation =  $newVenue->getCurrAffectation($datetime);

        // Si on le mouvement n'a pas d'affectation associée, et que l'on a déjà une affectation dans MB
        if (!$movement->affectation_id && $affectation->_id) {
          return "Le mouvement '$movement->_id' n'est pas lié à une affectation dans Mediboard";
        }

        // Si on a une affectation associée, alors on charge celle-ci
        if ($movement->affectation_id) {
          $affectation = $movement->loadRefAffectation();
        }

        if ($msg = $affectation->delete()) {
          return $msg;
        }

        return null;

      // Cas mutation
      case "A02" :
        $affectation->entree = $datetime;
        $affectation->loadMatchingObject();

        // Si on ne retrouve pas une affectation
        // Création de l'affectation
        // et mettre à 'effectuee' la précédente si elle existe sinon création de celle-ci
        if (!$affectation->_id) {
          // Récupération du Lit et UFs
          $this->getPL($PV1_3, $affectation);

          $return_affectation = $newVenue->forceAffectation($affectation);
          //$datetime, $affectation->lit_id, $affectation->service_id);
          if (is_string($return_affectation)) {
            return $return_affectation;
          }

          $affectation = $return_affectation;
        }

        break;

      // Cas modification
      case "Z99" :
        if (!$movement) {
          return;
        }

        $affectation =  $newVenue->getCurrAffectation($datetime);
        // Si on le mouvement n'a pas d'affectation associée, et que l'on a déjà une affectation dans MB
        if (!$movement->affectation_id && $affectation->_id) {
          return "Le mouvement '$movement->_id' n'est pas lié à une affectation dans Mediboard";
        }

        // Si on a une affectation associée, alors on charge celle-ci
        if ($movement->affectation_id) {
          $affectation = $movement->loadRefAffectation();
        }
        // Sinon on récupère et on met à jour la première affectation
        else {
          $affectation->sejour_id = $newVenue->_id;
          $affectation->entree    = $newVenue->entree;
          $affectation->sortie    = $newVenue->sortie;
        }

        break;

      // Tous les autres cas on récupère et on met à jour la première affectation
      default :
        $newVenue->loadRefsAffectations();
        $affectation = $newVenue->_ref_first_affectation;
        if (!$affectation->_id) {
          $affectation->sejour_id = $newVenue->_id;
          $affectation->entree    = $newVenue->entree;
          $affectation->sortie    = $newVenue->sortie;
        }

        break;
    }

    // Si pas de lit on retourne une affectation vide/couloir
    if (!$PV1_3) {
      return $affectation;
    }

    if (!$this->queryTextNode("PL.3", $PV1_3)) {
      // On essaye de récupérer le service dans ce cas depuis l'UF d'hébergement
      $uf           = new CUniteFonctionnelle();
      $uf->group_id = $newVenue->group_id;
      $uf->code     = $this->queryTextNode("PL.1", $PV1_3);
      if (!$uf->loadMatchingObject()) {
        return $affectation;
      }

      $affectation_uf               = new CAffectationUniteFonctionnelle();
      $affectation_uf->uf_id        = $uf->_id;
      $affectation_uf->object_class = "CService";
      $affectation_uf->loadMatchingObject();
      // Dans le cas où l'on retrouve un service associé à l'UF d'hébergement
      if ($affectation_uf->_id) {
        $newVenue->service_id        = $affectation_uf->object_id;
        $newVenue->uf_hebergement_id = $affectation_uf->uf_id;
      }

      $newVenue->uf_medicale_id    = $this->mappingUFMedicale($data);
      $newVenue->uf_soins_id       = $this->mappingUFSoins($data);

      // On ne check pas la cohérence des dates des consults/intervs
      $newVenue->_skip_date_consistencies = true;
      if ($msgVenue = $newVenue->store()) {
        return $msgVenue;
      }

      return $affectation;
    }

    // Récupération du Lit et UFs
    $this->getPL($PV1_3, $affectation);
    $affectation->uf_medicale_id = $this->mappingUFMedicale($data);
    $affectation->uf_soins_id    = $this->mappingUFSoins($data);

    if ($msg = $affectation->store()) {
      return $msg;
    }
    
    return $affectation;
  }

  function storeGrossesse(CSejour $newVenue, $data) {
    if ($newVenue->type_pec != "O") {
      return;
    }
    
    $grossesse                 = new CGrossesse();
    $grossesse->parturiente_id = $newVenue->patient_id;
    $grossesse->loadMatchingObject("terme_prevu desc"); 
    
    // Dans le cas où l'on a déjà une grossesse pour la patiente
    if ($grossesse->_id) {
      // On recherche si la grossesse a déjà un séjour avec des naissances OU le nbre de jours entre le terme et l'entrée du
      // séjour est inférieur à 294 jours (42 semaines) - 42*7
      if (count($grossesse->loadRefsNaissances()) || (abs(mbDaysRelative($grossesse->terme_prevu, $newVenue->entree)) > 294)) {
        $grossesse                 = new CGrossesse();
        $grossesse->parturiente_id = $newVenue->patient_id;
      }
    }
    
    if (!$grossesse->_id) {
      $grossesse->terme_prevu = mbDate($newVenue->sortie);
      if ($msg = $grossesse->store()) {
        return $msg;
      }
    }
     
    $newVenue->grossesse_id = $grossesse->_id;
    // On ne check pas la cohérence des dates des consults/intervs
    $newVenue->_skip_date_consistencies = true;
    if ($msg = $newVenue->store()) {
      return $msg;
    }   
  }
  
  function mapAndStoreNaissance(CSejour $newVenue, $data) {
    if ($this->queryTextNode("PV1.4", $data["PV1"]) != "N") {
      return;
    }
    
    // Récupération du séjour de la maman
    if (!$mother_AN = $this->getANMotherIdentifier($data["PID"])) {
      return CAppUI::tr("CHL7Event-AA-E227");
    }
    
    $sender = $this->_ref_sender;
    $idex_mother = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $mother_AN);
    if (!$idex_mother->_id) {
      return CAppUI::tr("CHL7Event-AA-E228");
    }
    
    $sejour_mother = new CSejour();
    $sejour_mother->load($idex_mother->object_id);
    
    // Récupération de l'IPP de la maman
    if (!$mother_PI = $this->getPIMotherIdentifier($data["PID"])) {
      return CAppUI::tr("CHL7Event-AA-E229");
    }
    
    if (CIdSante400::getMatch("CPatient", $sender->_tag_patient, $mother_PI)->object_id != $sejour_mother->patient_id) {
      return CAppUI::tr("CHL7Event-AA-E230");
    }
    
    $naissance                   = new CNaissance();
    $naissance->sejour_enfant_id = $newVenue->_id;    
    $naissance->sejour_maman_id  = $sejour_mother->_id;
    $naissance->grossesse_id     = $sejour_mother->grossesse_id;
    $naissance->loadMatchingObject();
    
    $naissance->rang = $this->queryTextNode("PID.25", $data["PID"]);
        
    // On récupère l'entrée réelle ssi msg A01 pour indiquer l'heure de la naissance 
    if ($this->_ref_exchange_ihe->code == "A01") {
      $naissance->heure = mbTime($this->queryTextNode("PV1.44", $data["PV1"]));
    }
    
    // Notifier les autres destinataires autre que le sender
    $naissance->_eai_initiateur_group_id = $sender->group_id;
    
    return $naissance->store();
  }  
  
  function mappingUFMedicale($data) {
    if (!array_key_exists("ZBE", $data)) {
      return;
    }
  
  if (!($ZBE_7 = $this->queryNode("ZBE.7", $data["ZBE"]))) {
    return;
  }
    
    return CUniteFonctionnelle::getUF($this->queryTextNode("XON.10", $ZBE_7), "medicale")->_id;
  }
  
  function mappingUFSoins($data) {
    if (!array_key_exists("ZBE", $data)) {
      return;
    }
  
    if (!($ZBE_8 = $this->queryNode("ZBE.8", $data["ZBE"]))) {
      return;
    }

    return CUniteFonctionnelle::getUF($this->queryTextNode("XON.10", $ZBE_8), "soins")->_id;
  }  
  
  function mappingMovement($data, CSejour $newVenue, CMovement $movement) {
    // Segment ZBE    
    return $this->getZBE($data["ZBE"], $newVenue, $movement);
  }
  
  function getPV1(DOMNode $node, CSejour $newVenue) {    
    // Classe de patient
    $this->getPatientClass($node, $newVenue);
    
    // Type de l'admission
    $this->getAdmissionType($node, $newVenue);
    
    // Médecin responsable
    $this->getAttendingDoctor($node, $newVenue);
    
    // Médecin adressant
    $this->getReferringDoctor($node, $newVenue);
    
    // Discipline médico-tarifaire
    $this->getHospitalService($node, $newVenue);
    
    // Mode d'entrée
    $this->getAdmitSource($node, $newVenue);
    
    // Code tarif su séjour
    $this->getFinancialClass($node, $newVenue);
    
    // Type d'activité, mode de traitement
    $this->getChargePriceIndicator($node, $newVenue);
    
    // Demande de chambre particulière
    $this->getCourtesyCode($node, $newVenue);

    // Mode d'entrée personnalisable - Combinaison du ZFM
    $this->getDischargeDisposition($node, $newVenue);

    // Etablissement de destination
    $this->getDischargedToLocation($node, $newVenue);
    
    // Entrée / Sortie réelle du séjour
    $this->getAdmitDischarge($node, $newVenue);
    
    // Numéro de rang
    $this->getAlternateVisitID($node, $newVenue);
  }
  
  function getPatientClass(DOMNode $node, CSejour $newVenue) {
    $patient_class = CHL7v2TableEntry::mapFrom("4", $this->queryTextNode("PV1.2", $node));
 
    $newVenue->type = $patient_class ? $patient_class : "comp";
  }
  
  function getPL(DOMNode $node, CAffectation $affectation) {
    $sender = $this->_ref_sender;

    // Récupération de la chambre
    $nom_chambre = $this->queryTextNode("PL.2", $node);
    $chambre     = new CChambre();

    // Récupération du lit
    $nom_lit = $this->queryTextNode("PL.3", $node);
    $lit     = new CLit();

    switch ($sender->_configs["handle_PV1_3"]) {
      // idex du service
      case 'idex':
        $chambre_id = CIdSante400::getMatch("CChambre", $sender->_tag_chambre, $nom_chambre)->object_id;
        $chambre->load($chambre_id);

        $lit_id = CIdSante400::getMatch("CLit", $sender->_tag_lit, $nom_lit)->object_id;
        $lit->load($lit_id);

        break;
      // Dans tous les cas le nom du lit est celui que l'on reçoit du flux
      default:
        $chambre->nom = $nom_chambre;
        $chambre->loadMatchingObjectEsc();

        $lit->nom = $nom_lit;
        if ($chambre->_id) {
          $lit->chambre_id = $chambre->_id;
        }
        $lit->loadMatchingObjectEsc();
        
        break; 
    }

    // Affectation du lit
    $affectation->lit_id = $lit->_id;
    
    // Affectation du service
    if (!$affectation->service_id && $lit->_id) {
      $affectation->service_id = $lit->loadRefService()->_id;
    }

    $code_uf     = $this->queryTextNode("PL.1", $node);
    // Affectation de l'UF hébergement
    $uf = CUniteFonctionnelle::getUF($code_uf);
    $affectation->uf_hebergement_id = $uf->_id;
    
    // Affectation du service (couloir)
    if (!$affectation->service_id) {
      $affectation_uf               = new CAffectationUniteFonctionnelle();
      $affectation_uf->uf_id        = $uf->_id;
      $affectation_uf->object_class = "CService";
      $affectation_uf->loadMatchingObject();
      
      $affectation->service_id = $affectation_uf->object_id;
    }
  }
  
  function getAdmissionType(DOMNode $node, CSejour $newVenue) {
    $admission_type = $this->queryTextNode("PV1.4", $node);
    
    // Gestion de l'accouchement maternité
    if ($admission_type == "L") {
      $newVenue->type_pec = "O";
    }
  }
  
  function getAttendingDoctor(DOMNode $node, CSejour $newVenue) {
    $PV17 = $this->query("PV1.7", $node);

    // On ne récupère pas le praticien dans le cas où l'on a un séjour d'urgences et que la config est à non
    if ($newVenue->type == "urg" && !$this->_ref_sender->_configs["handle_PV1_7"]) {
      return;
    }
    
    $mediuser = new CMediusers();
    foreach ($PV17 as $_PV17) {
      $newVenue->praticien_id = $this->getDoctor($_PV17, $mediuser);
    }
    
    // Dans le cas ou la venue ne contient pas de medecin responsable
    // Attribution d'un medecin indeterminé
    if (!$newVenue->praticien_id) {
      $newVenue->praticien_id = $this->createIndeterminateDoctor();
    }
  }
  
  function getDoctor(DOMNode $node, CMbObject $object) {
    $type_id    = $this->queryTextNode("XCN.13", $node);
    $id         = $this->queryTextNode("XCN.1", $node);
    $last_name  = $this->queryTextNode("XCN.2/FN.1", $node);
    $first_name = $this->queryTextNode("XCN.3", $node);
    
    switch ($type_id) {
      case "RPPS" :
        $object->rpps = $id;
        break;
      case "ADELI" :
        $object->adeli = $id;
        break;
      case "RI" :
        // Notre propre RI
        if (($this->queryTextNode("XCN.9/HD.2", $node) == CAppUI::conf("hl7 assigning_authority_universal_id"))) {
          return $id;
        }
      default :
        // Recherche du praticien par son idex
        $id400  = CIdSante400::getMatch($object->_class, $this->_ref_sender->_tag_mediuser, $id);
        if ($id400->_id) {
          return $id400->object_id;
        }

        if ($object instanceof CMediusers) {
          $object->_user_first_name = $first_name;
          $object->_user_last_name  = $last_name;
        }
        if ($object instanceof CMedecin) {
          $object->prenom = $first_name;
          $object->nom    = $last_name;
        }
        
        break;
    }

    // Cas où l'on a aucune information sur le médecin
    if (!$object->rpps && !$object->adeli && !$object->_id &&
        (($object instanceof CMediusers && !$object->_user_last_name) ||
        ($object instanceof CMedecin && !$object->nom))) {
      return null;      
    }
    
    if ($object instanceof CMedecin && $object->loadMatchingObjectEsc()) {
      return $object->_id;
    }
      
    if ($object instanceof CMediusers) {
      if (($object->rpps || $object->adeli) && $object->loadMatchingObjectEsc()) {
        return $object->_id;
      }
      
      $user = new CUser;
      $user->user_first_name = $first_name;
      $user->user_last_name  = $last_name;
      if ($user->loadMatchingObjectEsc()) {
        return $user->_id;
      }
      
      $object->_user_first_name = $first_name;
      $object->_user_last_name  = $last_name;
      
      return $this->createDoctor($object);
    }
  }
  
  function createDoctor(CMediusers $mediuser) {
    $sender = $this->_ref_sender;

    $function = new CFunctions();
    $function->text = CAppUI::conf("hl7 importFunctionName");
    $function->group_id = $sender->group_id;
    $function->loadMatchingObjectEsc();
    if (!$function->_id) {
      $function->type = "cabinet";
      $function->compta_partagee = 0;
      $function->store();
    }
    $mediuser->function_id = $function->_id;
    $mediuser->makeUsernamePassword($mediuser->_user_first_name, $mediuser->_user_last_name, null, true);
    $mediuser->_user_type = 13; // Medecin
    $mediuser->actif = CAppUI::conf("hl7 doctorActif") ? 1 : 0; 
    
    $user = new CUser();
    $user->user_last_name   = $mediuser->_user_last_name;
    $user->user_first_name  = $mediuser->_user_first_name;
    // On recherche par le seek
    $users                  = $user->seek("$user->user_last_name $user->user_first_name");
    if (count($users) == 1) {
      $user = reset($users);
      $user->loadRefMediuser();
      $mediuser = $user->_ref_mediuser;
    } else {
      // Dernière recherche si le login est déjà existant
      $user = new CUser();
      $user->user_username = $mediuser->_user_username;
      if ($user->loadMatchingObject()) {
        // On affecte un username aléatoire
        $mediuser->_user_username .= rand(1, 10);    
      }
      
      $mediuser->store();
    }
    
    return $mediuser->_id;
  }
  
  function createIndeterminateDoctor() {
    $sender = $this->_ref_sender;
    
    $user    = new CUser();
    $user->user_last_name = CAppUI::conf("hl7 indeterminateDoctor")." $sender->group_id";
    if (!$user->loadMatchingObjectEsc()) {
      $mediuser = new CMediusers();
      $mediuser->_user_last_name = $user->user_last_name;
      
      return $this->createDoctor($mediuser);
    } 
      
    return $user->loadRefMediuser()->_id;
  }
  
  function getReferringDoctor(DOMNode $node, CSejour $newVenue) {
    $PV1_8 = $this->query("PV1.8", $node);
    
    $medecin = new CMedecin();
    foreach ($PV1_8 as $_PV1_8) {
      $newVenue->adresse_par_prat_id = $this->getDoctor($_PV1_8, $medecin);
    }
  }
  
  function getHospitalService(DOMNode $node, CSejour $newVenue) {
    $sender = $this->_ref_sender;
    $PV1_10 = $this->queryTextNode("PV1.10", $node);
    
    if (!$PV1_10) {
      return;
    }
    
    // Hospital Service
    switch ($sender->_configs["handle_PV1_10"]) {
      // idex du service
      case 'service':
        $newVenue->service_id = CIdSante400::getMatch("CService", $sender->_tag_service, $PV1_10)->object_id;
        break;

      // Discipline médico-tarifaire
      default:
        $discipline = new CDiscipline();
        $discipline->load($PV1_10);

        $newVenue->discipline_id = $discipline->_id;
        break;
    }
  }
  
  function getAdmitSource(DOMNode $node, CSejour $newVenue) {
    if (!($admit_source = $this->queryTextNode("PV1.14", $node))) {
      return;
    }
    
    $sender = $this->_ref_sender;

    // Mode d'entrée personnalisable
    if (CAppUI::conf("dPplanningOp CSejour use_custom_mode_entree")) {
      $mode_entree       = new CModeEntreeSejour();
      $mode_entree->code = $admit_source;
      $mode_entree->loadMatchingObject();

      $newVenue->mode_entree_id = $mode_entree->_id;
    }
    
    // Admit source
    switch ($sender->_configs["handle_PV1_14"]) {
      // Combinaison du ZFM
      // ZFM.1 + ZFM.3
      case 'ZFM':
        $newVenue->mode_entree = $admit_source[0];        
        if (strlen($admit_source) == 2) {
          $newVenue->provenance = $admit_source[1];
        }
        
        break;
    }       
  }
  
  function getFinancialClass(DOMNode $node, CSejour $newVenue) {
    /* @todo Voir comment gérer */
  }
  
  function getChargePriceIndicator(DOMNode $node, CSejour $newVenue) {
    $PV1_21 = $this->queryTextNode("PV1.21", $node);
    
    $sender = $this->_ref_sender;
    
    $charge           = new CChargePriceIndicator();
    $charge->code     = $PV1_21;
    $charge->actif    = 1;
    $charge->group_id = $sender->group_id;
    $charge->loadMatchingObject();
    
    // On affecte le type d'activité reçu sur le séjour
    $newVenue->charge_id = $charge->_id;
    
    // Type PEC
    $newVenue->type_pec = $charge->type_pec;

    // Si le type du séjour est différent de celui du type d'activité on modifie son type
    if ($charge->type && $charge->type != $newVenue->type) {
      $newVenue->type = $charge->type;
    }
  }
  
  function getCourtesyCode(DOMNode $node, CSejour $newVenue) {
    $newVenue->chambre_seule = $this->getBoolean($this->queryTextNode("PV1.22", $node));
  }
  
  function getDischargeDisposition(DOMNode $node, CSejour $newVenue) {
    // Gestion des circonstances de sortie
    if (!($discharge_disposition = $this->queryTextNode("PV1.36", $node))) {
      return;
    }

    // Mode d'entrée personnalisable
    if (CAppUI::conf("dPplanningOp CSejour use_custom_mode_sortie")) {
      $mode_sortie       = new CModeSortieSejour();
      $mode_sortie->code = $discharge_disposition;
      $mode_sortie->loadMatchingObject();

      $newVenue->mode_sortie_id = $mode_sortie->_id;
    }

    $sender = $this->_ref_sender;

    // Admit source
    switch ($sender->_configs["handle_PV1_36"]) {
      // Combinaison du ZFM
      // ZFM.2 + ZFM.4
      case 'ZFM':
        $newVenue->provenance = $discharge_disposition[0];
        if (strlen($discharge_disposition) == 2) {
          $newVenue->destination = $discharge_disposition[1];
        }

        break;
    }
  }
  
  function getDischargedToLocation(DOMNode $node, CSejour $newVenue) {
    if (!$finess = $this->queryTextNode("PV1.37/DLD.1", $node)) {
      return;
    }
    
    $etab_ext = new CEtabExterne();
    $etab_ext->finess = $finess;
    if (!$etab_ext->loadMatchingObjectEsc()) {
      return;
    }
    
    $newVenue->etablissement_sortie_id = $etab_ext->_id;
  }
  
  function getAdmitDischarge(DOMNode $node, CSejour $newVenue) {
    $event_code = $this->_ref_exchange_ihe->code;
    
    // On récupère l'entrée réelle ssi msg !A05
    if ($event_code != "A05") {
      $newVenue->entree_reelle = $this->queryTextNode("PV1.44", $node);
    }
    
    // On récupére la sortie réelle ssi msg A03 / Z99
    if ($event_code == "A03" || $event_code == "Z99") {
      $newVenue->sortie_reelle = $this->queryTextNode("PV1.45", $node);
    }
      
    // Cas spécifique de certains segments 
    // A11 : on supprime la date d'entrée réelle 
    if ($event_code == "A11") {
      $newVenue->entree_reelle = "";
    }
    
    // A13 : on supprime la date de sortie réelle 
    if ($event_code == "A13") {
      $newVenue->sortie_reelle = "";
    }
  }
  
  function getAlternateVisitID(DOMNode $node, CSejour $newVenue) {
    if (!CAppUI::conf("dPplanningOp CSejour use_dossier_rang")) {
      return;
    }
    
    //Paramétrage de l'id 400
    $id400NRA               = new CIdSante400();
    $id400NRA->object_class = "CSejour";
    $id400NRA->object_id    = $newVenue->_id;
    $id400NRA->tag          = $newVenue->getTagNRA($newVenue->group_id);
    $id400NRA->id400        = $this->queryTextNode("PV1.50/CX.1", $node);
    $id400NRA->loadMatchingObject();
    $id400NRA->last_update  = mbDateTime();

    $id400NRA->store();
  }
  
  function getPV2(DOMNode $node, CSejour $newVenue) {    
    // Entrée / Sortie prévue du séjour
    $this->getExpectedAdmitDischarge($node, $newVenue);
    
    // Visit description
    $this->getVisitDescription($node, $newVenue);
    
    // Mode de transport d'entrée
    $this->getModeArrivalCode($node, $newVenue);
  }
  
  function getExpectedAdmitDischarge(DOMNode $node, CSejour $newVenue) {
    $entree_prevue = $this->queryTextNode("PV2.8", $node);
    $sortie_prevue = $this->queryTextNode("PV2.9", $node);
    
    if (!$entree_prevue) {
      $entree_prevue = $newVenue->entree_reelle ? $newVenue->entree_reelle : $newVenue->entree_prevue;
    }
    $newVenue->entree_prevue = $entree_prevue;
    
    if ($sortie_prevue) {
      $newVenue->sortie_prevue = $sortie_prevue;
    }
    elseif (!$sortie_prevue && !$newVenue->sortie_prevue) {
      $newVenue->sortie_prevue = mbAddDateTime(CAppUI::conf("dPplanningOp CSejour sortie_prevue ".$newVenue->type).":00:00", 
                    $newVenue->entree_reelle ? $newVenue->entree_reelle : $newVenue->entree_prevue);
    } 
    else {
      $newVenue->sortie_prevue = $newVenue->sortie_reelle ? $newVenue->sortie_reelle : $newVenue->sortie_prevue;
    }

    // On récupère l'entrée et sortie réelle ssi !entree_prevue && !sortie_prevue
    $parentNode = $node->parentNode;
    if (!$newVenue->entree_prevue) {
      $newVenue->entree_prevue = $this->queryTextNode("PV1.44", $this->queryNode("PV1", $parentNode));
    }
    
    if (!$newVenue->sortie_prevue) {
      $newVenue->sortie_prevue = $this->queryTextNode("PV1.45", $this->queryNode("PV1", $parentNode));
    }
    
    // Si les dates entrées/sorties sont incohérentes 
    $sender = $this->_ref_sender;
    if ($sender->_configs["control_date"] == "permissif") {
      $newVenue->entree_prevue = min($newVenue->entree_prevue, $newVenue->sortie_prevue);
      $newVenue->sortie_prevue = max($newVenue->entree_prevue, $newVenue->sortie_prevue);
    }
  }

  function getVisitDescription(DOMNode $node, CSejour $newVenue) {
    $sender = $this->_ref_sender;

    switch ($sender->_configs["handle_PV2_12"]) {
      case "none" :
        $newVenue->libelle = null;

        break;
      default :
        $newVenue->libelle = $this->queryTextNode("PV2.12", $node);

        break;
    }
  }
  
  function getModeArrivalCode(DOMNode $node, CSejour $newVenue) {
    $mode_arrival_code = $this->queryTextNode("PV2.38", $node);
    
    $newVenue->transport = CHL7v2TableEntry::mapFrom("0430", $mode_arrival_code);
  }
  
  function getZBE(DOMNode $node, CSejour $newVenue, CMovement $movement) {    
    $sender       = $this->_ref_sender;
    $id400_create = false;
    $event_code   = $this->_ref_exchange_ihe->code;
    
    $own_movement    = null;
    $sender_movement = null;
    foreach ($this->queryNodes("ZBE.1", $node) as $ZBE_1) {
      $EI_1 = $this->queryTextNode("EI.1", $ZBE_1);
      $EI_3 = $this->queryTextNode("EI.3", $ZBE_1);

      // Notre propre identifiant de mouvement
      if ($EI_3 == CAppUI::conf("hl7 assigning_authority_universal_id")) {
        $own_movement = $EI_1;
        break;
      }
      
      // L'identifiant de mouvement du sender
      if ($EI_3 == $sender->_configs["assigning_authority_universal_id"]) {
        $sender_movement = $EI_1;
        continue;
      }    
    }
    
    if (!$own_movement && !$sender_movement) {
      return "Impossible d'identifier le mouvement";
    }
    
    $movement_id = $own_movement ? $own_movement : $sender_movement;
    if (!$movement_id) {
      return null;
    }
        
    $start_movement_dt = $this->queryTextNode("ZBE.2/TS.1", $node);
    $action            = $this->queryTextNode("ZBE.4", $node);
    $original_trigger  = $this->queryTextNode("ZBE.6", $node);
    if (!$original_trigger) {
      $original_trigger = $event_code;
    }
    
    $movement->sejour_id = $newVenue->_id;
    $movement->original_trigger_code = $original_trigger;
    
    // Notre propre ID de mouvement
    if ($own_movement) {
      $movement_id_split       = explode("-", $movement_id);
      $movement->movement_type = $movement_id_split[0];
      $movement->_id           = $movement_id_split[1];
      $movement->loadMatchingObjectEsc();
      if (!$movement->_id) {
        return null;
      }
      
      if ($sender_movement) {
        $id400Movement = CIdSante400::getMatch("CMovement", $sender->_tag_movement, $sender_movement);
        if (!$id400Movement->_id) {
          $id400_create = true;
        }         
      }
    }
    // ID mouvement provenant d'un système tiers
    else {
      $id400Movement = CIdSante400::getMatch("CMovement", $sender->_tag_movement, $movement_id); 
      if ($id400Movement->_id) {
        $movement->load($id400Movement->object_id);
      }
      // Recherche d'un mouvement identique dans le cas ou il ne s'agit pas d'une mutation / absence
      else {
        $id400_create = true;
        if ($event_code != "A02" && $event_code != "A21") {
          $movement->cancel = 0;
          $movement->loadMatchingObjectEsc();
        }
      }
      
      $movement->movement_type = $newVenue->getMovementType($original_trigger);
    }
    
    // Erreur dans le cas où le type du mouvement est UPDATE ou CANCEL et que l'on a pas retrouvé le mvt
    if (($action == "UPDATE" || $action == "CANCEL") && !$movement->_id) {
      return null;
    }
    
    if ($action == "CANCEL") {
      $movement->cancel = true;
    }
    
    $movement->start_of_movement = $start_movement_dt;
    $movement->last_update = mbDateTime();
    if ($msg = $movement->store()) {
      return $msg;
    }
    
    if ($id400_create) {
      $id400Movement->last_update = mbDateTime();
      $id400Movement->object_id   = $movement->_id;
      if ($msg = $id400Movement->store()) {
        return $msg;
      } 
    }
    
    return $movement;
  }

  function getZFD(DOMNode $node, CSejour $newVenue) {  
    // Date lunaire
    if ($date_lunaire = $this->queryTextNode("ZFD.1", $node)) {
      $patient = $newVenue->_ref_patient;
      $patient->naissance = $date_lunaire;
      $patient->store();
    }
  }
  
  function getZFM(DOMNode $node, CSejour $newVenue) {   
    // Mode entrée PMSI 
    $this->getModeEntreePMSI($node, $newVenue);
    
    // Mode de sortie PMSI
    $this->getModeSortiePMSI($node, $newVenue);
    
    // Mode de provenance PMSI
    $this->getModeProvenancePMSI($node, $newVenue);
    
    // Mode de destination PMSI
    $this->getModeDestinationPMSI($node, $newVenue);
  }
  
  function getModeEntreePMSI(DOMNode $node, CSejour $newVenue) {
    $newVenue->mode_entree = $this->queryTextNode("ZFM.1", $node);
  }
  
  function getModeSortiePMSI(DOMNode $node, CSejour $newVenue) {
    $newVenue->mode_sortie = CHL7v2TableEntry::mapFrom("9001", $this->queryTextNode("ZFM.2", $node)); 
  }
  
  function getModeProvenancePMSI(DOMNode $node, CSejour $newVenue) {
    $ZFM_3 = $this->queryTextNode("ZFM.3", $node);
    if ($ZFM_3 == 0) {
      $ZFM_3 = null;  
    }
    $newVenue->provenance = $ZFM_3;
  }
  
  function getModeDestinationPMSI(DOMNode $node, CSejour $newVenue) {
    $ZFM_4 = $this->queryTextNode("ZFM.4", $node);
    if ($ZFM_4 == 0) {
      $ZFM_4 = null;  
    }
    $newVenue->destination = $ZFM_4;
  }
  
  function getZFP(DOMNode $node, CSejour $newVenue) {    
    // Catégorie socioprofessionnelle
    if ($csp = $this->queryTextNode("ZFP.2", $node)) {
      $patient = $newVenue->_ref_patient;
      $patient->csp = $csp;
      $patient->store();
    }  
  }
  
  function getZFV(DOMNode $node, CSejour $newVenue) {    
    // Etablissement de provenance
    $this->getEtablissementProvenance($node, $newVenue);
  }
  
  function getEtablissementProvenance(DOMNode $node, CSejour $newVenue) {
    if (!$finess = $this->queryTextNode("ZFV.1/DLD.1", $node)) {
      return;
    }
    
    $etab_ext = new CEtabExterne();
    $etab_ext->finess = $finess;
    if (!$etab_ext->loadMatchingObjectEsc()) {
      return;
    }
    
    $newVenue->etablissement_entree_id = $etab_ext->_id;
  }
  
  function getGT1(DOMNode $node, CSejour $newVenue) {
    
  }
}
