<?php
/**
 *  @package Mediboard
 *  @subpackage dPfiles
 *  @version $Revision$
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The CMedinetSender class
 */
class CMedinetSender extends CDocumentSender {
  public static $tag = "medinet transaction";
  public static $cpamConversion = array (
                        2 => 5,
                        3 => 2,
                        5 => 7,
                        7 => 12,
                        8 => 9,
                        10 => 16,
                        11 => 17,
                        12 => 19,
                        13 => 20,
                        14 => 22,
                        15 => 17,
                        16 => 15,
                        21 => 21,
                        30 => 6,
                        32 => 16,
                        33 => 21,
                        35 => 15,
                        41 => 18,
                        42 => 8,
                        43 => 19,
                        47 => 2,
                        48 => 2,
                        70 => 12,
                        71 => 13,
                        73 => 1,
                        74 => 1,
                        75 => 21,
                        77 => 12 
                      );
  public static $civiliteConversion = array (
                        "m" => "monsieur",
                        "f" => "madame",
                      );

  public static $sexeConversion = array (
                        "m" => "M",
                        "f" => "F",
                      );  
                       
  public static $descriptifStatus = array (
                            10 => "Données reçues non traitées.",
                            11 => "Données reçues traitées mais en erreur.",
                            12 => "Données reçues traitées correctement, message non créé.",
                            21 => "Message non créé car erreur.",
                            22 => "Message créé correctement, non envoyé. Fichier peut être supprimé.",
                            31 => "Erreur à l'envoi du message. Fichier peut être supprimé.",
                            32 => "Message envoyé correctement. Fichier peut être supprimé.",
                          );
                                                               
  var $clientSOAP = null;
  
  function initClientSOAP () {
    if ($this->clientSOAP instanceof SoapClient) {
      return;
    }
    try {
      $this->clientSOAP = new SoapClient(CAppUI::conf('dPfiles rooturl'));
    } catch (Exception $e) {
      trigger_error("Instanciation du SoapClient impossible : ".$e);
    }
  }
  
  function send($docItem) {
    global $AppUI;
    
    $this->initClientSOAP();
    
    $docItem->loadTargetObject();
    $object = $docItem->_ref_object;
    
    if ($object instanceof CConsultAnesth) {
      $object->loadRefConsultation();
      $object = $object->_ref_consultation;
    }
    
    $object->loadRefPraticien();
    $object->loadRefPatient();
    $object->_ref_praticien->loadRefSpecCPAM();
    
    if ($object instanceof CSejour) {
      $object->loadRefEtablissement();  
      
      $sej_id = $object->_id;
      
      $doc_type = 29;
      
      $act_dateActe = mbDate($object->entree_reelle);
      $act_dateValidationActe = mbDate($object->entree_reelle);
        
      $etab_id = $object->_ref_group->_id;
      $etab_nom = $object->_ref_group->text;
    }
    
    if ($object instanceof COperation) {
      $object->_ref_sejour->loadRefEtablissement();
      $object->loadRefPlageOp();
      
      $sej_id = $object->sejour_id;
      
      $doc_type = 8;
      
      $act_dateActe = mbDate($object->_datetime);
      $act_dateValidationActe = mbDate($object->_datetime);
        
      $etab_id = $object->_ref_sejour->_ref_group->_id;
      $etab_nom = $object->_ref_sejour->_ref_group->text;
    }
       
    if ($object instanceof CConsultation) {
    	$object->loadRefConsultAnesth();
    	
    	$act_dateActe = mbDate($object->_ref_plageconsult->date);
      $act_dateValidationActe = mbDate($object->_ref_plageconsult->date);
      
    	if ($object->_ref_consult_anesth instanceof CConsultAnesth) {
    		$object->_ref_consult_anesth->loadRefSejour();
    		$sejour = $object->_ref_consult_anesth->_ref_sejour;
    		
	      $doc_type = 67;	      
	    } else { 
	    	$object->loadRefSejour();   
	    	$sejour = $object->_ref_sejour;
	    	  
	      $doc_type = 7;
	    }
	    
      if ($sejour->sejour_id) {
        $sej_id = $sejour->sejour_id;
        
        $sejour->loadRefEtablissement();
          
        $etab_id = $sejour->_ref_group->_id;
        $etab_nom = $sejour->_ref_group->text;
      } else {
        $sej_id = -1;
        
        $object->_ref_praticien->loadRefFunction();
        $object->_ref_praticien->_ref_function->loadRefGroup();
      
        $etab_id = $object->_ref_praticien->_ref_function->_ref_group->_id;
        $etab_nom = $object->_ref_praticien->_ref_function->_ref_group->text;
      }
    }    
       
    $praticien = $object->_ref_praticien;
        
    $aut_id = $praticien->_id;
    $aut_nom = $praticien->_user_last_name;
    $aut_prenom = $praticien->_user_first_name;
    $aut_numOrdre = ($praticien->adeli) ? $praticien->adeli : "";
    
    $patient = $object->_ref_patient;
    
    $pat_id = $patient->_id;
    $pat_civilite = CMedinetSender::$civiliteConversion[$patient->sexe];
    $pat_nomNaissance = ($patient->nom_jeune_fille) ? $patient->nom_jeune_fille : $patient->nom; 
    $pat_nomUsuel = ($patient->nom_jeune_fille) ? $patient->nom : ""; 
    $pat_prenom = $patient->prenom;
    $pat_sexe = CMedinetSender::$sexeConversion[$patient->sexe];
    $pat_dateNaissance = $patient->naissance;
    $pat_cpNaissance = $patient->cp_naissance;
    $pat_villeNaissance = ($patient->lieu_naissance) ? $patient->lieu_naissance : "";
    $pat_cinseePaysNaissance = ($patient->pays_naissance_insee) ? $patient->pays_naissance_insee : -1 ;
    $pat_adresseVie = $patient->adresse;
    $pat_cpVie = $patient->cp;
    $pat_villeVie = $patient->ville;
    $pat_cinseePaysVie = ($patient->pays_insee) ? $patient->pays_insee : -1 ;
    $pat_telephone1 = $patient->tel;
    $pat_telephone2 = $patient->tel2;
    
    $act_id = $object->_id;
    
    $doc_id = $docItem->_id;
     
    $spec_cpam_id = $praticien->_ref_spec_cpam->spec_cpam_id;
    $act_pathologie = isset(CMedinetSender::$cpamConversion[$spec_cpam_id]) ? CMedinetSender::$cpamConversion[$spec_cpam_id] : 0;

    if ($docItem instanceof CCompteRendu) {
      $doc_nom = $docItem->nom;
      $doc_titre = $docItem->nom;
      $doc_nomReel = $docItem->nom;
      $doc_typeMime = "text/html";
      
      $log = new CUserLog();
      $log->type = "create";
      $log->object_id = $docItem->_id;    
      $log->object_class = $docItem->_class_name;
      $log->loadMatchingObject();
      
      $act_dateCreationActe = mbDate($log->date); 
      $fichier = base64_encode($docItem->getContent);
    }
    
    if ($docItem instanceof CFile) {
      $doc_nom = $docItem->file_name;
      $doc_titre = $docItem->file_name;
      $doc_nomReel = $docItem->file_real_filename;
      $doc_typeMime = $docItem->file_type;
      
      $act_dateCreationActe = mbDate($docItem->file_date);
      $fichier = base64_encode($docItem->getContent);
    }
    $doc_commentaire = "";
                        
    $invalidation = 0;

    if ($messages = $this->checkParameters($object)) {
      $AppUI->setMsg($messages, UI_MSG_ERROR);
      return;
    }
    
    $parameters = array ( "sej_id" => $sej_id,
                          "aut_id" => $aut_id,
                          "aut_nom" => $aut_nom,
                          "aut_prenom" => $aut_prenom,
                          "aut_numOrdre" => $aut_numOrdre,
                          "pat_id" => $pat_id,
                          "pat_civilite" => $pat_civilite,
                          "pat_nomNaissance" => $pat_nomNaissance,
                          "pat_nomUsuel" => $pat_nomUsuel,
                          "pat_prenom" => $pat_prenom,
                          "pat_sexe" => $pat_sexe,
                          "pat_dateNaissance" => $pat_dateNaissance,
                          "pat_cpNaissance" => $pat_cpNaissance,
                          "pat_villeNaissance" => $pat_villeNaissance,
                          "pat_cinseePaysNaissance" => $pat_cinseePaysNaissance,
                          "pat_adresseVie" => $pat_adresseVie,
                          "pat_cpVie" => $pat_cpVie,
                          "pat_villeVie" => $pat_villeVie,
                          "pat_cinseePaysVie" =>$pat_cinseePaysVie,
                          "pat_telephone1" => $pat_telephone1,
                          "pat_telephone2" => $pat_telephone2,
                          "doc_id" => $doc_id,
                          "doc_nom" => $doc_nom,
                          "doc_titre" => $doc_titre,
                          "doc_commentaire" => $doc_commentaire,
                          "doc_type" => $doc_type,
                          "doc_nomReel" => $doc_nomReel,
                          "doc_typeMime" => $doc_typeMime,
                          "act_id" => $act_id,
                          "act_pathologie" => $act_pathologie,
                          "act_dateActe" => $act_dateActe,
                          "act_dateCreationActe" => $act_dateCreationActe,
                          "act_dateValidationActe" => $act_dateValidationActe,
                          "etab_id" => $etab_id,
                          "etab_nom" => $etab_nom,
                          "invalidation" => $invalidation,
                          "fichier" => $fichier);
    
    $parameters = array_map("utf8_encode", $parameters);

    // Identifiant de la transaction
    if (null == $transactionId = $this->clientSOAP->saveNewDocument_withStringFile($parameters)) {
      return;
    }
    
    $transactionId = $transactionId->saveNewDocument_withStringFileResult;
    
    $parameters = array ( "transactionId" => $transactionId);
    
    // Statut de la transaction
    if (null == $status = $this->clientSOAP->getStatus($parameters)) {
      return;
    }
    
    $status = $status->getStatusResult;
    
    if(isset(CMedinetSender::$descriptifStatus[$status])) {
      $AppUI->setMsg(CMedinetSender::$descriptifStatus[$status]);
    } else {
      $AppUI->setMsg("Aucun statut n'a été transmis", UI_MSG_ALERT);
    }
    
    // Création de l'identifiant externe 
    $id400 = new CIdSante400();
    //Paramétrage de l'id 400
    $id400->object_class = $docItem->_class_name;
    $id400->tag = CMedinetSender::$tag;
            
    // Affectation de l'id400 a la transaction
    $id400->id400 = $transactionId;
      
    $id400->object_id = $docItem->_id;
    $id400->_id = null;
    $id400->last_update = mbDateTime(); 
    
    $id400->store();

    // Change l'etat du document
    $docItem->etat_envoi = "oui";
                
    return true; 
  }
  
  function cancel($docItem) {
    $this->initClientSOAP();
    
    // Identifiant de la dernière transaction concernant le document
    if (null == $transactionId = $this->getTransactionId($docItem)) {
      return;
    }
        
    $parameters = array ( "idTransaction" => $transactionId);
    
    // Annulation de la transaction
    if (null == $transactionAnnulationId = $this->clientSOAP->cancelDocument($parameters)) {
      return;
    }
    
    $transactionAnnulationId = $transactionAnnulationId->cancelDocumentResult;
        
    // Création de l'identifiant externe 
    $id400 = new CIdSante400();
    //Paramétrage de l'id 400
    $id400->object_class = $docItem->_class_name;
    $id400->tag = CMedinetSender::$tag;
            
    // Affectation de l'id400 a la transaction
    $id400->id400 = $transactionAnnulationId;
      
    $id400->object_id = $docItem->_id;
    $id400->_id = null;
    $id400->last_update = mbDateTime();
    
    $id400->store();
    
    // Change l'etat du document
    $docItem->etat_envoi = "non"; 
        
    return true;
  }
  
  function resend($docItem) {
    $this->initClientSOAP();
    
    // Annulation de la transaction
    if (null == $this->cancel($docItem)) {
      return;
    }
    
    // Renvoi du document
    if (null == $this->send($docItem)) {
      return;
    }
    
    return true;
  }
  
  function isSendable(CDocumentItem $docItem) {
    $docItem->loadTargetObject();
    
    return($docItem->_ref_object instanceOf COperation || $docItem->_ref_object instanceOf CSejour 
            || $docItem->_ref_object instanceOf CConsultation || $docItem->_ref_object instanceOf CConsultAnesth);
  }
  
  function getTransactionId($docItem) {
    $id400 = new CIdSante400();
    $id400->loadLatestFor($docItem, CMedinetSender::$tag);
    
    $transactionId = $id400->id400;
    
    if(!$transactionId) {
      return;
    }
       
    return $transactionId;
  }
  
  function checkParameters($object) {
  	global $AppUI;
  	
  	$messages = null;
  	
    $patient = $object->_ref_patient;
    if (!$patient->naissance) {
    	$messages = "Date de naissance du patient manquante, ";
    }
    if (!$patient->sexe) {
      $messages .= "Sexe du patient manquant, ";
    }
    if (!$patient->lieu_naissance) {
      $messages .= "Lieu de naissance du patient manquant, ";
    }
    
    return $messages;
  }
}
?>