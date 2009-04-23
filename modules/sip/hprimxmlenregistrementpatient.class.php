<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("sip", "hprimxmlevenementspatients");

class CHPrimXMLEnregistrementPatient extends CHPrimXMLEvenementsPatients { 
  function __construct() {            
    parent::__construct();
  }
  
  function generateFromOperation($mbPatient, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $enregistrementPatient = $this->addElement($evenementPatient, "enregistrementPatient");
    $actionConversion = array (
      "create" => "création",
      "store" => "modification",
      "delete" => "suppression"
    );
    $this->addAttribute($enregistrementPatient, "action", $actionConversion[$mbPatient->_ref_last_log->type]);

    // Ajout du patient   
    $this->addPatient($enregistrementPatient, $mbPatient, null, $referent);
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function generateEvenementsPatients($mbObject, $referent = null, $initiateur = null) {
    $echg_hprim = new CEchangeHprim();
    $this->_date_production = $echg_hprim->date_production = mbDateTime();
    $echg_hprim->emetteur = $this->_emetteur;
    $echg_hprim->destinataire = $this->_destinataire;
    $echg_hprim->type = "evenementsPatients";
    $echg_hprim->sous_type = "enregistrementPatient";
    $echg_hprim->message = utf8_encode($this->saveXML());
    if ($initiateur) {
      $echg_hprim->initiateur_id = $initiateur;
    }
    
    $echg_hprim->store();
    
    $this->_identifiant = str_pad($echg_hprim->_id, 6, '0', STR_PAD_LEFT);
            
    $this->generateEnteteMessageEvenementsPatients();
    $this->generateFromOperation($mbObject, $referent);
    
    $doc_valid = $this->schemaValidate();
    $echg_hprim->message_valide = $doc_valid ? 1 : 0;

    $this->saveTempFile();
    $messageEvtPatient = utf8_encode($this->saveXML()); 
    
    $echg_hprim->message = $messageEvtPatient;
    
    $echg_hprim->store();
    
    return $messageEvtPatient;
  }
  
  function getEvenementPatientXML() {
    global $m;

    $xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );

    $data['acquittement'] = $xpath->queryAttributNode("/hprim:evenementsPatients", null, "acquittementAttendu");

    $query = "/hprim:evenementsPatients/hprim:enteteMessage";

    $entete = $xpath->queryUniqueNode($query);

    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $entete);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $entete);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='système']", $agents);
    $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $enregistrementPatient = $xpath->queryUniqueNode("hprim:enregistrementPatient", $evenementPatient);

    $data['action'] = $this->getActionEvenement($evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $enregistrementPatient);
    $data['voletMedical'] = $xpath->queryUniqueNode("hprim:voletMedical", $enregistrementPatient);

    $data['idSource'] = $this->getIdSource($data['patient']);
    $data['idCible'] = $this->getIdCible($data['patient']);
    
    return $data;
  }
  
  function getIPPPatient() {
    $xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
    
    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $enregistrementPatient = $xpath->queryUniqueNode("hprim:enregistrementPatient", $evenementPatient);
    
    $patient = $xpath->queryUniqueNode("hprim:patient", $enregistrementPatient);

    return $this->getIdSource($patient);
  }
  
  function getActionEvenement($node) {
    $xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
    
    return $xpath->queryAttributNode("hprim:enregistrementPatient", $node, "action");    
  }
  
  /**
   * Recording a patient with an IPP in the system
   * @param CHPrimXMLAcquittementsPatients $domAcquittement
   * @param CEchangeHprim $echange_hprim
   * @param CPatient $newPatient
   * @param array $data
   * @return string acquittement 
   **/
  function enregistrementPatient($domAcquittement, $echange_hprim, $newPatient, $data) {
    // Traitement du message des erreurs
    $avertissement = $msgID400 = $msgIPP = "";
    
    $mutex = new CMbSemaphore("sip-ipp");
     mbTrace($data, "Tableau", true);
    // Si SIP
    if (CAppUI::conf('sip server')) {
      // Acquittement d'erreur : identifiants source et cible non fournis
      if (!$data['idSource'] && !$data['idCible']) {
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E05");
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
        $echange_hprim->message = $messagePatient;
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
          $newPatient = $this->createPatient($data['patient'], $newPatient);
          $newPatient->_IPP = $IPP->id400;
          $msgPatient = $newPatient->store();
          $newPatient->loadLogs();

          $modified_fields = "";
          if ($newPatient->_ref_last_log) {
            foreach ($newPatient->_ref_last_log->_fields as $field) {
              $modified_fields .= "$field \n";
            }
          } 
          
          $codes = array ($msgPatient ? "A03" : "I02", "I03");
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
            $newPatient = $this->createPatient($data['patient'], $newPatient);
            $newPatient->_no_ipp = 1;
            $msgPatient = $newPatient->store();
            
            $IPP->object_id = $newPatient->_id;

            $IPP->last_update = mbDateTime();
            $msgIPP = $IPP->store();
            
            $newPatient->_IPP = $IPP->id400;
            $newPatient->_no_ipp = 0;
            $msgPatient = $newPatient->store();
                      
            $codes = array ($msgPatient ? "A02" : "I01", $msgIPP ? "A05" : "A01");
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
            $newPatient = $this->createPatient($data['patient'], $newPatient);

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
            if ($newPatient->_ref_last_log) {
              foreach ($newPatient->_ref_last_log->_fields as $field) {
                $modified_fields .= "$field \n";
              }
            }
             
            $codes = array ($msgPatient ? "A03" : "I02", $msgIPP ? "A05" : $_IPP_create ? "I06" : "I08");
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
              $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E04", $commentaire);
              $doc_valid = $domAcquittement->schemaValidate();
              $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
              $echange_hprim->acquittement = $messageAcquittement;
              $echange_hprim->statut_acquittement = "erreur";
              $echange_hprim->store();
              
              return $messageAcquittement;
            } else {
              $newPatient->load($IPP->object_id);

              // Mapping du patient
              $newPatient = $this->createPatient($data['patient'], $newPatient);
              $newPatient->_IPP = $IPP->id400;
              $msgPatient = $newPatient->store();
              $newPatient->loadLogs();
               
              $modified_fields = "";
              if ($newPatient->_ref_last_log) {
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
            $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E03", $commentaire);
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
        $newPatient = $this->createPatient($data['patient'], $newPatient);

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
            $_code_IPP = "A01";
          }
          // idCible fourni connu
          else {
            $IPP->id400 = null;
            $IPP->loadMatchingObject("id400 DESC");

            // Incrementation de l'id400
            $IPP->id400++;
            $IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);
            $IPP->_id = null;
             
            $_code_IPP = "I09";
          }
        } else {
          $mutex->acquire();
           
          // Chargement du dernier id externe de prescription du praticien s'il existe
          $IPP->loadMatchingObject("id400 DESC");

          // Incrementation de l'id400
          $IPP->id400++;
          $IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);

          $IPP->_id = null;
           
          $_code_IPP = "I06";
        }

        $IPP->object_id = $newPatient->_id;

        $IPP->last_update = mbDateTime();
        $msgIPP = $IPP->store();

        $mutex->release();

        $newPatient->_IPP = $IPP->id400;
        $newPatient->_no_ipp = 0;
        $msgPatient = $newPatient->store();

        $codes = array ($msgPatient ? "A02" : "I01", $msgID400 ? "A04" : "I04", $msgIPP ? "A05" : $_code_IPP);
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
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E05");
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
        $echange_hprim->acquittement = $messageAcquittement;
        $echange_hprim->statut_acquittement = "erreur";
        $echange_hprim->date_echange = mbDateTime();
        $echange_hprim->store();
      
        return $messageAcquittement;
      }
      
      $IPP = new CIdSante400();
      //Paramétrage de l'id 400
      $IPP->object_class = "CPatient";
      $IPP->tag = $data['idClient'];
      $IPP->id400 = $data['idSource'];
      
      // idSource non connu
      if(!$IPP->loadMatchingObject()) {
        // idCible fourni
        if ($data['idCible']) {
          if ($newPatient->load($data['idCible'])) {
            // Mapping du patient
            $newPatient = $this->createPatient($data['patient'], $newPatient);
        
            // Evite de passer dans le sip handler
            $newPatient->_coms_from_hprim = 1;
            $msgPatient = $newPatient->store();
        
            $newPatient->loadLogs();
            $modified_fields = "";
            if ($newPatient->_ref_last_log) {
              foreach ($newPatient->_ref_last_log->_fields as $field) {
                $modified_fields .= "$field \n";
              }
            }
            $_code_IPP = "I21";
            $_code_Patient = true; 
            $commentaire = "Patient modifiée : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields.";
          } else {
            $_code_IPP = "I20";
          }
        } else {
          $_code_IPP = "I22";  
        }
        // Mapping du patient
        $newPatient = $this->createPatient($data['patient'], $newPatient);
           
        if (!$newPatient->_id) {
          if ($newPatient->loadMatchingPatient()) {
            // Evite de passer dans le sip handler
            $newPatient->_coms_from_hprim = 1;
            $msgPatient = $newPatient->store();
        
            $newPatient->loadLogs();
            $modified_fields = "";
            if ($newPatient->_ref_last_log) {
              foreach ($newPatient->_ref_last_log->_fields as $field) {
                $modified_fields .= "$field \n";
              }
            }
            $_code_IPP = "A21";
            $_code_Patient = true; 
            $commentaire = "Patient modifiée : $newPatient->_id.  Les champs mis à jour sont les suivants : $modified_fields.";           
          }
        }
                
        if (!$newPatient->_id) {
          // Mapping du patient
          $newPatient = $this->createPatient($data['patient'], $newPatient);
        
          // Evite de passer dans le sip handler
          $newPatient->_coms_from_hprim = 1;
          $msgPatient = $newPatient->store();
          
          $commentaire = "Patient créé : $newPatient->_id. ";
        }
          
        $IPP->object_id = $newPatient->_id;
        $IPP->last_update = mbDateTime();
        $msgIPP = $IPP->store();
        
        $codes = array ($msgPatient ? ($_code_Patient ? "A03" : "A02") : ($_code_Patient ? "I02" : "I01"), $msgIPP ? "A05" : $_code_IPP);
        
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
        $newPatient = $this->createPatient($data['patient'], $newPatient);
                        
        // idCible non fourni
        if (!$data['idCible']) {
          $_code_IPP = "I23"; 
        } else {
          $tmpPatient = new CPatient();
          // idCible connu
          if ($tmpPatient->load($data['idCible'])) {
            if ($tmpPatient->_id != $IPP->object_id) {
              $commentaire = "L'identifiant source fait référence au patient : $IPP->object_id et l'identifiant cible au patient : $tmpPatient->_id.";
              $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E04", $commentaire);
              $doc_valid = $domAcquittement->schemaValidate();
              $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
              $echange_hprim->acquittement = $messageAcquittement;
              $echange_hprim->statut_acquittement = "erreur";
              $echange_hprim->store();
              return $messageAcquittement;
            }
            $_code_IPP = "I24"; 
          }
          // idCible non connu
          else {
            $_code_IPP = "A20";
          }
        }
        // Evite de passer dans le sip handler
        $newPatient->_coms_from_hprim = 1;
        $msgPatient = $newPatient->store();
        
        $newPatient->loadLogs();
        $modified_fields = "";
        if ($newPatient->_ref_last_log) {
          foreach ($newPatient->_ref_last_log->_fields as $field) {
            $modified_fields .= "$field \n";
          }
        }
        $codes = array ($msgPatient ? "A03" : "I02", $_code_IPP);
        
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