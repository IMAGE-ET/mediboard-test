<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLEnregistrementPatient extends CHPrimXMLEvenementsPatients { 
  var $actions = array(
    'création' => "création",
    'remplacement' => "remplacement",
    'modification' => "modification",
  );
  
  function __construct() {   
    $this->sous_type = "enregistrementPatient";
             
    parent::__construct();
  }
  
  function generateFromOperation($mbPatient, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient   = $this->addElement($evenementsPatients, "evenementPatient");

    $enregistrementPatient = $this->addElement($evenementPatient, "enregistrementPatient");
    $actionConversion = array (
      "create" => "création",
      "store"  => "modification",
      "delete" => "suppression"
    );
    $action = (!$mbPatient->_ref_last_log) ? "modification" : $actionConversion[$mbPatient->_ref_last_log->type];

    $this->addAttribute($enregistrementPatient, "action", $action);
    
    $patient = $this->addElement($enregistrementPatient, "patient");
    // Ajout du patient   
    $this->addPatient($patient, $mbPatient, $referent);
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getContentsXML() {
    $xpath = new CHPrimXPath($this);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $enregistrementPatient = $xpath->queryUniqueNode("hprim:enregistrementPatient", $evenementPatient);

    $data['action'] = $this->getActionEvenement("hprim:enregistrementPatient", $evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $enregistrementPatient);

    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    return $data;
  }
  
  /**
   * Recording a patient with an IPP in the system
   * @param CHPrimXMLAcquittementsPatients $dom_acq
   * @param CEchangeHprim $echg_hprim
   * @param CPatient $newPatient
   * @param array $data
   * @return CHPrimXMLAcquittementsPatients $msgAcq 
   **/
  function enregistrementPatient($dom_acq, &$newPatient, $data) {
    // Traitement du message des erreurs
    $commentaire = $avertissement = $msgID400 = $msgIPP = "";
    $_IPP_create   = $_modif_patient = false;
    
    $echg_hprim = $this->_ref_echange_hprim; 
    $echg_hprim->_ref_sender->loadConfigValues();
    $sender     = $echg_hprim->_ref_sender;
    
    $idSourcePatient = $data['idSourcePatient'];
    $idCiblePatient  = $data['idCiblePatient'];
    
    // Acquittement d'erreur : identifiants source et cible non fournis
    if (!$idCiblePatient && !$idSourcePatient) {
      return $echg_hprim->setAckError($dom_acq, "E005", $commentaire, $newPatient);
    }    
    
    // Si SIP
    if (CAppUI::conf('sip server')) {      
      // Cas 1 : Identifiant source (IC) non fourni et identifiant cible fourni (IPP)
      if (!$idSourcePatient && $idCiblePatient) {
        $IPP = CIdSante400::getMatch("CPatient", CAppUI::conf("sip tag_ipp"), str_pad($idCiblePatient, 6, '0', STR_PAD_LEFT));
        // Cas 1.1 : idCible connu
        if ($IPP->_id) {
          $newPatient->load($IPP->object_id);
          
          if (!$this->checkSimilarPatient($newPatient, $data['patient'])) {
            $commentaire = "Le nom et/ou le prénom sont très différents."; 
            return $echg_hprim->setAckError($dom_acq, "E016", $commentaire, $newPatient);
          }
          
          // Mapping du patient
          $newPatient = $this->mappingPatient($data['patient'], $newPatient);
          // Store du patient
          $msgPatient = CEAIPatient::storePatient($newPatient, $IPP->id400);
          
          $modified_fields = CEAIPatient::getModifiedFields($newPatient);
          
          $codes = array ($msgPatient ? "A003" : "I002", "I003");
          if ($msgPatient) {
            $avertissement = $msgPatient." ";
          } else {
            $commentaire .= "Patient : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields. IPP : $IPP->id400.";
          }
        }
        // Cas 1.2 : idCible non connu
        else {          
          if (is_numeric($IPP->id400) && (strlen($IPP->id400) <= 6)) {
            // Mapping du patient
            $newPatient = $this->mappingPatient($data['patient'], $newPatient);
             // Si serveur et pas d'IPP sur le patient
            $newPatient->_no_ipp = 1;
            $msgPatient = $newPatient->store();
            
            $msgIPP = CEAIPatient::storeIPP($IPP, $newPatient, $sender);
            
            $newPatient->_IPP = $IPP->id400;
            // Si serveur et on a un IPP sur le patient
            $newPatient->_no_ipp = 0;
            $msgPatient = $newPatient->store();
                      
            $codes = array ($msgPatient ? "A002" : "I001", $msgIPP ? "A005" : "A001");
            if ($msgPatient) {
              $avertissement = $msgPatient." ";
            } else {
              $commentaire = "Patient créé : $newPatient->_id. IPP créé : $IPP->id400.";
            }
          }
        }
        
        return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $commentaire, $newPatient);
      }
      
      $id400 = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $idSourcePatient);
      // Cas 2 : Patient existe sur le SIP
      if ($id400->_id) {
        // Identifiant du patient sur le SIP
        $idPatientSIP = $id400->object_id;
        // Cas 2.1 : Pas d'idCible
        if (!$idCiblePatient) {
          if ($newPatient->load($idPatientSIP)) {
            // Mapping du patient
            $newPatient = $this->mappingPatient($data['patient'], $newPatient);
            
            // Création de l'IPP
            $IPP = new CIdSante400();
            //Paramétrage de l'id 400
            CEAIPatient::IPPSIPSetting($IPP, $idPatientSIP);
            
            $mutex = new CMbSemaphore("sip-ipp");
            $mutex->acquire();
            // Chargement du dernier IPP s'il existe
            if (!$IPP->loadMatchingObject("id400 DESC")) {
              // Incrementation de l'id400
              CEAIPatient::IPPSIPIncrement($IPP);
              
              $msgIPP = CEAIPatient::storeIPP($IPP);
                
              $_IPP_create = true;
            }
            $mutex->release();
            
            // Store du patient
            $msgPatient = CEAIPatient::storePatient($newPatient, $IPP->_id);
            
            $modified_fields = CEAIPatient::getModifiedFields($newPatient);
             
            $codes = array ($msgPatient ? "A003" : "I002", $msgIPP ? "A005" : $_IPP_create ? "I006" : "I008");
            if ($msgPatient || $msgIPP) {
              $avertissement = $msgPatient." ".$msgIPP;
            } else {
              $commentaire = "Patient : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields. IPP : $IPP->id400.";
            }

            return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $commentaire, $newPatient);
          }
        }
        // Cas 2.2 : idCible envoyé
        else {
          $IPP = CIdSante400::getMatch("CPatient", CAppUI::conf("sip tag_ipp"), $idCiblePatient);
          // Cas 2.2.1 : idCible connu
          if ($IPP->_id) {
            // Acquittement d'erreur idSource et idCible incohérent
            if ($idPatientSIP != $IPP->object_id) {
              $commentaire = "L'identifiant source fait référence au patient : $idPatientSIP et l'identifiant cible au patient : $IPP->object_id.";
              return $echg_hprim->setAckError($dom_acq, "E004", $commentaire, $newPatient);
            } else {
              $newPatient->load($IPP->object_id);

              // Mapping du patient
              $newPatient = $this->mappingPatient($data['patient'], $newPatient);
              // Store du patient
              $msgPatient = CEAIPatient::storePatient($newPatient, $IPP->id400);
              
              $modified_fields = CEAIPatient::getModifiedFields($newPatient);
              
              if ($msgPatient) {
                $avertissement = $msgPatient." ";
              } else {
                $commentaire = "Patient : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields.";
              }
              
              return $echg_hprim->setAck($dom_acq, $msgPatient ? "A003" : "I002", $avertissement, $commentaire, $newPatient);
            }
          } 
          // Cas 2.2.2 : idCible non connu
          else {
            $commentaire = "L'identifiant source fait référence au patient : $idPatientSIP et l'identifiant cible n'est pas connu.";
            return $echg_hprim->setAckError($dom_acq, "E003", $commentaire, $newPatient);
          }
        }
      }
      // Cas 3 : Patient n'existe pas sur le SIP
      else {
        // Mapping du patient
        $newPatient = $this->mappingPatient($data['patient'], $newPatient);
        // Cas 3.1 : Patient retrouvé  (nom, prénom et date de naissance)      
        if ($newPatient->loadMatchingPatient()) {
          // Cas où le patient a déjà un identifiant externe pour ce même destinataire
          $idex = new CIdSante400();
          $idex->loadLatestFor($newPatient, $sender->_tag_patient);
          if ($idex->id400 != $idSourcePatient) {
            $commentaire = "Le patient possède déjà un identifiant dans notre base ('$idex->id400')";
            return $echg_hprim->setAckError($dom_acq, "E017", $commentaire, $newPatient);
          }
          
          // Mapping du patient
          $newPatient = $this->mappingPatient($data['patient'], $newPatient);
          // Si serveur et pas d'IPP sur le patient
          $newPatient->_no_ipp = 1;
          $msgPatient = $newPatient->store();
      
          $modified_fields = CEAIPatient::getModifiedFields($newPatient);
          
          $_modif_patient = true; 
          $commentaire = "Patient : $newPatient->_id.  Les champs mis à jour sont les suivants : $modified_fields.";           
        } 
        // Cas 3.2 : Patient non retrouvé
        else {
          // Si serveur et pas d'IPP sur le patient
          $newPatient->_no_ipp = 1;
          $msgPatient = $newPatient->store();
        
          $commentaire = "Patient créé : $newPatient->_id. ";
        }
        
        // Création de l'identifiant externe TAG CIP + idSource
        $id400Patient = new CIdSante400();
        CEAIPatient::storeID400CIP($id400Patient, $sender, $idSourcePatient, $newPatient);        
        
        // Création de l'IPP
        $IPP = new CIdSante400();
        //Paramétrage de l'id 400
        CEAIPatient::IPPSIPSetting($IPP);
        
        $mutex = new CMbSemaphore("sip-ipp");
        $mutex->acquire();
        // Cas IPP fourni
        if ($idCiblePatient) {
          $IPP->id400 = str_pad($idCiblePatient, 6, '0', STR_PAD_LEFT);

          // IPP fourni non connu
          if (!$IPP->loadMatchingObject() && is_numeric($IPP->id400) && (strlen($IPP->id400) <= 6)) {
            $_code_IPP = "A001";
          }
          // IPP fourni connu
          else {  
            // Si IPP est identique au patient retrouvé
            if ($IPP->object_id == $newPatient->_id) {
              $_code_IPP = "I025";
            } else {
              // Annule l'IPP envoyé          
              $IPP->id400 = null;
              $IPP->loadMatchingObject("id400 DESC");
  
              // Incrementation de l'id400
              CEAIPatient::IPPSIPIncrement($IPP);
               
              $_code_IPP = "I009";
            }
          }
        } else { 
          // Si le patient a été retrouvé on a déjà l'IPP
          if ($_modif_patient) {
            $IPP->object_id = $newPatient->_id;
            $IPP->loadMatchingObject();
            $_code_IPP = "I026";
          } else {
            $IPP->loadMatchingObject("id400 DESC");
  
            // Incrementation de l'id400
            CEAIPatient::IPPSIPIncrement($IPP);
  
            $_code_IPP = "I006";
          }          
        }
        $mutex->release();

        $msgIPP = CEAIPatient::storeIPP($IPP, $newPatient);
        
        $newPatient->_IPP = $IPP->id400;
        // Si serveur et on a un IPP sur le patient
        $newPatient->_no_ipp = 0;
        $msgPatient = $newPatient->store();
        
        $codes = array ($msgPatient ? ($_modif_patient ? "A003" : "A002") : "I001", $msgID400 ? "A004" : "I004", $msgIPP ? "A005" : $_code_IPP);
        if ($msgPatient || $msgID400 || $msgIPP) {
          $avertissement = $msgPatient." ".$msgID400." ".$msgIPP;
        } else {
          $commentaire = "Patient : $newPatient->_id. Identifiant externe : $id400Patient->id400. IPP : $IPP->id400.";
        }
      }
      
      return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $commentaire, $newPatient);
    } 
    // Si CIP
    else {      
      $IPP = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $idSourcePatient);
      // idSource non connu
      if (!$IPP->_id) {
        // idCible fourni
        if ($idCiblePatient) {
          if ($newPatient->load($idCiblePatient)) {
            // Le patient trouvé est-il différent ?
            /*if (!$this->checkSimilarPatient($newPatient, $data['patient'])) {
              $commentaire = "Le nom et/ou le prénom sont très différents."; 
              $msgAcq = $dom_acq->generateAcquittements("erreur", "E016", $commentaire);
              $doc_valid = $dom_acq->schemaValidate();
              $echg_hprim->setObjectIdClass("CPatient", $newPatient->_id);
              $echg_hprim->setAckError($doc_valid, $msgAcq, "erreur");
              return $msgAcq;
            }*/
        
            // Mapping du patient
            $newPatient = $this->mappingPatient($data['patient'], $newPatient);
          
            // Notifier les autres destinataires autre que le sender
            $newPatient->_eai_initiateur_group_id = $sender->group_id;
            $msgPatient = $newPatient->store();
        
            $modified_fields = CEAIPatient::getModifiedFields($newPatient);
            
            $_code_IPP      = "I021";
            $_modif_patient = true; 
            $commentaire    = "Patient modifié : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields.";
          } else {
            $_code_IPP = "I020";
          }
        } else {
          $_code_IPP = "I022";  
        }
        // Mapping du patient
        $newPatient = $this->mappingPatient($data['patient'], $newPatient);
        // Notifier les autres destinataires autre que le sender
        $newPatient->_eai_initiateur_group_id = $sender->group_id;
              
        if (!$newPatient->_id) {
          // Patient retrouvé
          if ($newPatient->loadMatchingPatient()) {
            // Mapping du patient
            $newPatient = $this->mappingPatient($data['patient'], $newPatient);
            
            $msgPatient = $newPatient->store();
        
            $modified_fields = CEAIPatient::getModifiedFields($newPatient);
            
            $_code_IPP      = "A021";
            $_modif_patient = true; 
            $commentaire    = "Patient modifié : $newPatient->_id.  Les champs mis à jour sont les suivants : $modified_fields.";           
          } else {
            $msgPatient = $newPatient->store();
          
            $commentaire = "Patient créé : $newPatient->_id. ";
          }
        }

        $msgIPP = CEAIPatient::storeIPP($IPP, $newPatient, $sender);
        
        $codes = array ($msgPatient ? ($_modif_patient ? "A003" : "A002") : ($_modif_patient ? "I002" : "I001"), $msgIPP ? "A005" : $_code_IPP);
        
        if ($msgPatient || $msgIPP) {
          $avertissement = $msgPatient." ".$msgIPP;
        } else {
          $commentaire .= "IPP créé : $IPP->id400.";
        }
      } 
      // idSource connu
      else {
        $newPatient->load($IPP->object_id);
        /*if (!$this->checkSimilarPatient($newPatient, $data['patient'])) {
          $commentaire = "Le nom et/ou le prénom sont très différents."; 
          $msgAcq = $dom_acq->generateAcquittements("erreur", "E016", $commentaire);
          $doc_valid = $dom_acq->schemaValidate();
          $echg_hprim->setObjectIdClass("CPatient", $newPatient->_id);
          $echg_hprim->setAckError($doc_valid, $msgAcq, "erreur");
          return $msgAcq;
        }*/
            
        // Mapping du patient
        $newPatient = $this->mappingPatient($data['patient'], $newPatient);
                        
        // idCible non fourni
        if (!$idCiblePatient) {
          $_code_IPP = "I023"; 
        } else {
          $tmpPatient = new CPatient();
          // idCible connu
          if ($tmpPatient->load($idCiblePatient)) {
            if ($tmpPatient->_id != $IPP->object_id) {
              $commentaire = "L'identifiant source fait référence au patient : $IPP->object_id et l'identifiant cible au patient : $tmpPatient->_id.";
              return $echg_hprim->setAckError($dom_acq, "E004", $commentaire, $newPatient);
            }
            $_code_IPP = "I024"; 
          }
          // idCible non connu
          else {
            $_code_IPP = "A020";
          }
        }
        // Notifier les autres destinataires autre que le sender
        $newPatient->_eai_initiateur_group_id = $sender->group_id;
        $msgPatient = $newPatient->store();
        
        $modified_fields = CEAIPatient::getModifiedFields($newPatient);
        
        $codes = array ($msgPatient ? "A003" : "I002", $_code_IPP);
        
        if ($msgPatient) {
          $avertissement = $msgPatient." ";
        } else {
          $commentaire = "Patient modifié : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields. IPP associé : $IPP->id400.";
        }
      }
      
      return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $commentaire, $newPatient);
    }    
  }
}
?>