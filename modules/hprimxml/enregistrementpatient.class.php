<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementspatients");

class CHPrimXMLEnregistrementPatient extends CHPrimXMLEvenementsPatients { 
  var $actions = array(
    'création' => "création",
    'remplacement' => "remplacement",
    'modification' => "modification",
    'fusion' => "fusion"
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
    $this->addAttribute($enregistrementPatient, "action", $actionConversion[$mbPatient->_ref_last_log->type]);
    
    $patient = $this->addElement($enregistrementPatient, "patient");
    // Ajout du patient   
    $this->addPatient($patient, $mbPatient, null, $referent);
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getEnregistrementPatientXML() {
    $xpath = new CMbXPath($this, true);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $enregistrementPatient = $xpath->queryUniqueNode("hprim:enregistrementPatient", $evenementPatient);

    $data['action'] = $this->getActionEvenement("hprim:enregistrementPatient", $evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $enregistrementPatient);

    $data['idSource'] = $this->getIdSource($data['patient']);
    $data['idCible'] = $this->getIdCible($data['patient']);
    
    return $data;
  }
  
  /**
   * Recording a patient with an IPP in the system
   * @param CHPrimXMLAcquittementsPatients $domAcquittement
   * @param CEchangeHprim $echange_hprim
   * @param CPatient $newPatient
   * @param array $data
   * @return CHPrimXMLAcquittementsPatients $messageAcquittement 
   **/
  function enregistrementPatient($domAcquittement, &$echange_hprim, &$newPatient, $data) {        
    if ($messageAcquittement = $this->isActionValide($data['action'], $domAcquittement, $echange_hprim)) {
      return $messageAcquittement;
    }

    // Traitement du message des erreurs
    $avertissement = $msgID400 = $msgIPP = "";
    $_IPP_create = $_code_Patient = false;
    $mutex = new CMbSemaphore("sip-ipp");

    // Si SIP
    if (CAppUI::conf('sip server')) {
      // Acquittement d'erreur : identifiants source et cible non fournis
      if (!$data['idSource'] && !$data['idCible']) {
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E05");
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
        $echange_hprim->acquittement = $messageAcquittement;
        $echange_hprim->statut_acquittement = "erreur";
        $echange_hprim->store();
      
        return $messageAcquittement;
      }
        
      // Identifiant source non fourni et identifiant cible fourni
      if (!$data['idSource'] && $data['idCible']) {
        $IPP = new CIdSante400();
        //Paramétrage de l'id 400
        $IPP->object_class = "CPatient";
        $IPP->tag = CAppUI::conf("mb_id");

        $IPP->id400 = str_pad($data['idCible'], 6, '0', STR_PAD_LEFT);

        // idCible connu
        if ($IPP->loadMatchingObject()) {
          $newPatient->load($IPP->object_id);
          
          if (!$this->checkSimilarPatient($newPatient, $data['patient'])) {
            $commentaire = "Le nom et/ou le prénom sont très différents. "; 
          }
          
          // Mapping du patient
          $newPatient = $this->mappingPatient($data['patient'], $newPatient);
          $newPatient->_IPP = $IPP->id400;
          $msgPatient = $newPatient->store();
          $newPatient->loadLogs();

          $modified_fields = "";
          if (is_array($newPatient->_ref_last_log->_fields)) {
            foreach ($newPatient->_ref_last_log->_fields as $field) {
              $modified_fields .= "$field \n";
            }
          } 
          
          $codes = array ($msgPatient ? "A003" : "I002", "I003");
          if ($msgPatient) {
            $avertissement = $msgPatient." ";
          } else {
            $commentaire .= "Patient modifiée : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields. IPP associé : $IPP->id400.";
          }
        }
        // idCible non connu
        else {          
          if (is_numeric($IPP->id400) && (strlen($IPP->id400) <= 6)) {
            // Mapping du patient
            $newPatient = $this->mappingPatient($data['patient'], $newPatient);
            $newPatient->_no_ipp = 1;
            $msgPatient = $newPatient->store();
            
            $IPP->object_id = $newPatient->_id;

            $IPP->last_update = mbDateTime();
            $msgIPP = $IPP->store();
            
            $newPatient->_IPP = $IPP->id400;
            $newPatient->_no_ipp = 0;
            $msgPatient = $newPatient->store();
                      
            $codes = array ($msgPatient ? "A002" : "I01", $msgIPP ? "A005" : "A001");
            if ($msgPatient) {
              $avertissement = $msgPatient." ";
            } else {
              $commentaire = substr("Patient créé : $newPatient->_id. IPP créé : $IPP->id400.", 0, 4000);
            }
          }
        }
        
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : substr($commentaire, 0, 4000));
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
          
        $echange_hprim->acquittement = $messageAcquittement;
        $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
        $echange_hprim->store();
        
        return $messageAcquittement;
      }
        
      $id400 = new CIdSante400();
      //Paramétrage de l'id 400
      $id400->object_class = "CPatient";
      $id400->tag = $data['idClient'];
      $id400->id400 = $data['idSource'];
    
      // Cas 1 : Patient existe sur le SIP
      if($id400->loadMatchingObject()) {
        // Identifiant du patient sur le SIP
        $idPatientSIP = $id400->object_id;
        // Cas 1.1 : Pas d'identifiant cible
        if(!$data['idCible']) {
          if ($newPatient->load($idPatientSIP)) {
            // Mapping du patient
            $newPatient = $this->mappingPatient($data['patient'], $newPatient);

            // Création de l'IPP
            $IPP = new CIdSante400();
            //Paramétrage de l'id 400
            $IPP->object_class = "CPatient";
            $IPP->tag = CAppUI::conf("mb_id");
            $IPP->object_id = $idPatientSIP;

            $mutex->acquire();
            // Chargement du dernier IPP s'il existe
            if (!$IPP->loadMatchingObject("id400 DESC")) {
              // Incrementation de l'id400
              $IPP->id400++;
              $IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);

              $IPP->_id = null;
              $IPP->last_update = mbDateTime();
              $msgIPP = $IPP->store();
                
              $_IPP_create = true;
            }
            $mutex->release();

            $newPatient->_IPP = $IPP->_id;
            $msgPatient = $newPatient->store();
            $newPatient->loadLogs();

            $modified_fields = "";
            if (is_array($newPatient->_ref_last_log->_fields)) {
              foreach ($newPatient->_ref_last_log->_fields as $field) {
                $modified_fields .= "$field \n";
              }
            }
             
            $codes = array ($msgPatient ? "A003" : "I002", $msgIPP ? "A005" : $_IPP_create ? "I006" : "I008");
            if ($msgPatient || $msgIPP) {
              $avertissement = $msgPatient." ".$msgIPP;
            } else {
              $commentaire = substr("Patient modifiée : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields. IPP créé : $IPP->id400.", 0, 4000);
            }
            $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
            $doc_valid = $domAcquittement->schemaValidate();
            $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
              
            $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
          }
        }
        // Cas 1.2 : Identifiant cible envoyé
        else {
          $IPP = new CIdSante400();
          //Paramétrage de l'id 400
          $IPP->object_class = "CPatient";
          $IPP->tag = CAppUI::conf("mb_id");

          $IPP->id400 = $data['idCible'];
          
          // Id cible connu
          if ($IPP->loadMatchingObject()) {
            // Acquittement d'erreur idSource et idCible incohérent
            if ($idPatientSIP != $IPP->object_id) {
              $commentaire = "L'identifiant source fait référence au patient : $idPatientSIP et l'identifiant cible au patient : $IPP->object_id.";
              $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E004", $commentaire);
              $doc_valid = $domAcquittement->schemaValidate();
              $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
              $echange_hprim->acquittement = $messageAcquittement;
              $echange_hprim->statut_acquittement = "erreur";
              $echange_hprim->store();
              
              return $messageAcquittement;
            } else {
              $newPatient->load($IPP->object_id);

              // Mapping du patient
              $newPatient = $this->mappingPatient($data['patient'], $newPatient);
              $newPatient->_IPP = $IPP->id400;
              $msgPatient = $newPatient->store();
              $newPatient->loadLogs();
               
              $modified_fields = "";
              if (is_array($newPatient->_ref_last_log->_fields)) {
                foreach ($newPatient->_ref_last_log->_fields as $field) {
                  $modified_fields .= "$field \n";
                }
              }
               
              if ($msgPatient) {
                $avertissement = $msgPatient." ";
              } else {
                $commentaire = substr("Patient modifiée : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields.", 0, 4000);
              }
              $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $msgPatient ? "A03" : "I02", $avertissement ? $avertissement : $commentaire);
              
              $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
            }
          } 
          // Id cible non connu
          else {
            $commentaire = "L'identifiant source fait référence au patient : $idPatientSIP et l'identifiant cible n'est pas connu.";
            $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E003", $commentaire);
            $doc_valid = $domAcquittement->schemaValidate();
            $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
            $echange_hprim->statut_acquittement = "erreur";
            $echange_hprim->acquittement = $messageAcquittement;
            $echange_hprim->date_echange = mbDateTime();
            $echange_hprim->store();
    
            return $messageAcquittement; 
          }
        }
      }
      // Cas 2 : Patient n'existe pas sur le SIP
      else {
        // Mapping du patient
        $newPatient = $this->mappingPatient($data['patient'], $newPatient);

        $newPatient->_no_ipp = 1;
        $msgPatient = $newPatient->store();

        // Création de l'identifiant externe TAG CIP + idSource
        $id400Patient = new CIdSante400();
        //Paramétrage de l'id 400
        $id400Patient->object_class = "CPatient";
        $id400Patient->tag = $data['idClient'];

        $id400Patient->id400 = $data['idSource'];
        
        $id400Patient->object_id = $newPatient->_id;
        $id400Patient->_id = null;
        $id400Patient->last_update = mbDateTime();
        $msgID400 = $id400Patient->store();
        
        // Création de l'IPP
        $IPP = new CIdSante400();
        //Paramétrage de l'id 400
        $IPP->object_class = "CPatient";
        $IPP->tag = CAppUI::conf("mb_id");

        // Cas idCible fourni
        if ($data['idCible']) {
          $IPP->id400 = str_pad($data['idCible'], 6, '0', STR_PAD_LEFT);

          $mutex->acquire();
           
          // idCible fourni non connu
          if (!$IPP->loadMatchingObject() && is_numeric($IPP->id400) && (strlen($IPP->id400) <= 6)) {
            $_code_IPP = "A001";
          }
          // idCible fourni connu
          else {
            $IPP->id400 = null;
            $IPP->loadMatchingObject("id400 DESC");

            // Incrementation de l'id400
            $IPP->id400++;
            $IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);
            $IPP->_id = null;
             
            $_code_IPP = "I009";
          }
        } else {
          $mutex->acquire();
           
          // Chargement du dernier id externe de prescription du praticien s'il existe
          $IPP->loadMatchingObject("id400 DESC");

          // Incrementation de l'id400
          $IPP->id400++;
          $IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);

          $IPP->_id = null;
           
          $_code_IPP = "I006";
        }

        $IPP->object_id = $newPatient->_id;

        $IPP->last_update = mbDateTime();
        $msgIPP = $IPP->store();

        $mutex->release();

        $newPatient->_IPP = $IPP->id400;
        $newPatient->_no_ipp = 0;
        $msgPatient = $newPatient->store();

        $codes = array ($msgPatient ? "A002" : "I001", $msgID400 ? "A004" : "I004", $msgIPP ? "A005" : $_code_IPP);
        if ($msgPatient || $msgID400 || $msgIPP) {
          $avertissement = $msgPatient." ".$msgID400." ".$msgIPP;
        } else {
          $commentaire = "Patient créé : $newPatient->_id. Identifiant externe créé : $id400Patient->id400. IPP créé : $IPP->id400.";
        }
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
        $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
      }
    } 
    // Si CIP
    else {
      // Acquittement d'erreur : identifiants source et cible non fournis
      if (!$data['idCible'] && !$data['idSource']) {
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E005");
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
        $echange_hprim->acquittement = $messageAcquittement;
        $echange_hprim->statut_acquittement = "erreur";
        $echange_hprim->date_echange = mbDateTime();
        $echange_hprim->store();
      
        return $messageAcquittement;
      }
      
      $dest_hprim = new CDestinataireHprim();
      $dest_hprim->nom = $data['idClient'];
      $dest_hprim->loadMatchingObject();
      
      $IPP = new CIdSante400();
      //Paramétrage de l'id 400
      $IPP->object_class = "CPatient";
      $IPP->tag = $dest_hprim->_tag;
      $IPP->id400 = $data['idSource'];
      
      // idSource non connu
      if(!$IPP->loadMatchingObject()) {
        // idCible fourni
        if ($data['idCible']) {
          if ($newPatient->load($data['idCible'])) {
            // Mapping du patient
            $newPatient = $this->mappingPatient($data['patient'], $newPatient);
        
            // Evite de passer dans le sip handler
            $newPatient->_coms_from_hprim = 1;
            $msgPatient = $newPatient->store();
        
            $newPatient->loadLogs();
            $modified_fields = "";
            if (is_array($newPatient->_ref_last_log->_fields)) {
              foreach ($newPatient->_ref_last_log->_fields as $field) {
                $modified_fields .= "$field \n";
              }
            }
            $_code_IPP = "I021";
            $_code_Patient = true; 
            $commentaire = "Patient modifiée : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields.";
          } else {
            $_code_IPP = "I020";
          }
        } else {
          $_code_IPP = "I022";  
        }
        // Mapping du patient
        $newPatient = $this->mappingPatient($data['patient'], $newPatient);
        // Evite de passer dans le sip handler
        $newPatient->_coms_from_hprim = 1;
        
        // Patient retrouvé      
        if (!$newPatient->_id) {
          if ($newPatient->loadMatchingPatient()) {
            $msgPatient = $newPatient->store();
        
            $newPatient->loadLogs();
            $modified_fields = "";
            if (is_array($newPatient->_ref_last_log->_fields)) {
              foreach ($newPatient->_ref_last_log->_fields as $field) {
                $modified_fields .= "$field \n";
              }
            }
            $_code_IPP = "A021";
            $_code_Patient = true; 
            $commentaire = "Patient modifiée : $newPatient->_id.  Les champs mis à jour sont les suivants : $modified_fields.";           
          } else {
            $msgPatient = $newPatient->store();
          
            $commentaire = "Patient créé : $newPatient->_id. ";
          }
        }
          
        $IPP->object_id = $newPatient->_id;
        $IPP->last_update = mbDateTime();
        $msgIPP = $IPP->store();
        
        $codes = array ($msgPatient ? ($_code_Patient ? "A003" : "A002") : ($_code_Patient ? "I002" : "I001"), $msgIPP ? "A005" : $_code_IPP);
        
        if ($msgPatient || $msgIPP) {
          $avertissement = $msgPatient." ".$msgIPP;
        } else {
          $commentaire .= "IPP créé : $IPP->id400.";
        }
      } 
      // idSource connu
      else {
        $newPatient->load($IPP->object_id);
        // Mapping du patient
        $newPatient = $this->mappingPatient($data['patient'], $newPatient);
                        
        // idCible non fourni
        if (!$data['idCible']) {
          $_code_IPP = "I023"; 
        } else {
          $tmpPatient = new CPatient();
          // idCible connu
          if ($tmpPatient->load($data['idCible'])) {
            if ($tmpPatient->_id != $IPP->object_id) {
              $commentaire = "L'identifiant source fait référence au patient : $IPP->object_id et l'identifiant cible au patient : $tmpPatient->_id.";
              $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E004", $commentaire);
              $doc_valid = $domAcquittement->schemaValidate();
              $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
              $echange_hprim->acquittement = $messageAcquittement;
              $echange_hprim->statut_acquittement = "erreur";
              $echange_hprim->store();
              return $messageAcquittement;
            }
            $_code_IPP = "I024"; 
          }
          // idCible non connu
          else {
            $_code_IPP = "A020";
          }
        }
        // Evite de passer dans le sip handler
        $newPatient->_coms_from_hprim = 1;
        $msgPatient = $newPatient->store();
        
        $newPatient->loadLogs();
        $modified_fields = "";
        if (is_array($newPatient->_ref_last_log->_fields)) {
          foreach ($newPatient->_ref_last_log->_fields as $field) {
            $modified_fields .= "$field \n";
          }
        }
        $codes = array ($msgPatient ? "A003" : "I002", $_code_IPP);
        
        if ($msgPatient) {
          $avertissement = $msgPatient." ";
        } else {
          $commentaire = "Patient modifiée : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields. IPP associé : $IPP->id400.";
        }
      }
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : substr($commentaire, 0, 4000)); 
      $doc_valid = $domAcquittement->schemaValidate();
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
      $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
    }
    $echange_hprim->acquittement = $messageAcquittement;
    $echange_hprim->date_echange = mbDateTime();
    $echange_hprim->store();

    return $messageAcquittement;
  }
}
?>