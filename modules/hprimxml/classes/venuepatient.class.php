<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementspatients");

class CHPrimXMLVenuePatient extends CHPrimXMLEvenementsPatients { 
  var $actions = array(
    'création'     => "création",
    'remplacement' => "remplacement",
    'modification' => "modification",
    'suppression'   => "suppression"
  );
  
  function __construct() {    
    $this->sous_type = "venuePatient";
            
    parent::__construct();
  }
  
  function generateFromOperation(CSejour $mbVenue, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $venuePatient = $this->addElement($evenementPatient, "venuePatient");
    $actionConversion = array (
      "create" => "création",
      "store"  => "modification",
      "delete" => "suppression"
    );
    $action = $actionConversion[$mbVenue->_ref_last_log->type];
    if ($mbVenue->annule) {
      $action = "suppression";
    }
    $this->addAttribute($venuePatient, "action", $action);
    
    $patient = $this->addElement($venuePatient, "patient");
    // Ajout du patient   
    $this->addPatient($patient, $mbVenue->_ref_patient, $referent);
    
    $venue = $this->addElement($venuePatient, "venue"); 
    // Ajout de la venue   
    $this->addVenue($venue, $mbVenue, $referent);
    
    // Ajout des attributs du séjour
    $this->addAttribute($venue, "confidentiel", "non");
    
    // Etat d'une venue : encours, clôturée ou préadmission
    $etatConversion = array (
      "preadmission" => "préadmission",
      "encours"  => "encours",
      "cloture" => "clôturée"
    );

    $this->addAttribute($venue, "etat", $etatConversion[$mbVenue->_etat]);
    
    $this->addAttribute($venue, "facturable", ($mbVenue->facturable)  ? "oui" : "non");
    $this->addAttribute($venue, "declarationMedecinTraitant", ($mbVenue->_adresse_par_prat)  ? "oui" : "non");
    
    // Cas d'une annulation dans Mediboard on passe en trash le num dossier
    if (CAppUI::conf("hprimxml trash_numdos_sejour_cancel") && $mbVenue->annule && $mbVenue->_num_dossier) {
      $num_dossier = new CIdSante400();
      //Paramétrage de l'id 400
      $num_dossier->object_class = "CSejour";
      $num_dossier->tag = $this->_ref_echange_hprim->_ref_destinataire->_tag_sejour;
      $num_dossier->id400 = $mbVenue->_num_dossier;
      
      if ($num_dossier->loadMatchingObject()) {
        $num_dossier->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_trash').$this->_ref_echange_hprim->_ref_destinataire->_tag_sejour;
        $num_dossier->store();
      }
    }
            
    // Traitement final
    $this->purgeEmptyElements();
  }

  function getContentsXML() {
    $xpath = new CHPrimXPath($this);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $venuePatient= $xpath->queryUniqueNode("hprim:venuePatient", $evenementPatient);

    $data['action'] = $this->getActionEvenement("hprim:venuePatient", $evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $venuePatient);
    $data['venue'] = $xpath->queryUniqueNode("hprim:venue", $venuePatient);

    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['idSourceVenue'] = $this->getIdSource($data['venue']);
    $data['idCibleVenue'] = $this->getIdCible($data['venue']);
    
    return $data;
  }
  
  /**
   * Coming recording 
   * @param CHPrimXMLAcquittementsPatients $domAcquittement
   * @param CEchangeHprim $echange_hprim
   * @param CPatient $newPatient
   * @param CSejour $newSejour
   * @param array $data
   * @return CHPrimXMLAcquittementsPatients $messageAcquittement 
   **/
  function venuePatient($domAcquittement, $newPatient, $data, &$newVenue = null) {
    $echange_hprim = $this->_ref_echange_hprim;
    
    // Cas 1 : Traitement du patient
    $domEnregistrementPatient = new CHPrimXMLEnregistrementPatient();
    $domEnregistrementPatient->_ref_echange_hprim = $echange_hprim;
    $messageAcquittement = $domEnregistrementPatient->enregistrementPatient($domAcquittement, $newPatient, $data);
    if ($echange_hprim->statut_acquittement != "OK") {
      return $messageAcquittement;
    }
    
    $domAcquittement = new CHPrimXMLAcquittementsPatients();
    $domAcquittement->_identifiant_acquitte = $data['identifiantMessage'];
    $domAcquittement->_sous_type_evt        = $this->sous_type;
    $domAcquittement->_ref_echange_hprim    = $echange_hprim;
    
    // Traitement du message des erreurs
    $avertissement = $msgID400 = $msgVenue = $msgNumDossier = "";    
    $_code_Venue = $_code_NumDos = $_num_dos_create = $_modif_venue = false;
    $mutexSej = new CMbSemaphore("sip-numdos"); 
    
    $dest_hprim = $echange_hprim->_ref_emetteur;
    
    if (!$newVenue) {
      $newVenue = new CSejour();
    }
    
    // Cas d'une annulation
    $cancel = false;
    if ($data['action'] == "suppression") {
      $cancel = true;
    }
      
    // Affectation du patient
    $newVenue->patient_id = $newPatient->_id; 
    // Affectation de l'établissement
    $newVenue->group_id = $dest_hprim->group_id;
    
    // Si SIP
    if (CAppUI::conf('sip server')) {
      // Cas 2 : idSource non fourni, idCible fourni
      if (!$data['idSourceVenue'] && $data['idCibleVenue']) {
        $num_dossier = new CIdSante400();
        //Paramétrage de l'id 400
        $num_dossier->object_class = "CSejour";
        $num_dossier->tag = CAppUI::conf("sip tag_dossier");
        $num_dossier->id400 = str_pad($data['idCibleVenue'], 6, '0', STR_PAD_LEFT);
        
        // Cas 2.1 : idCible connu
        if ($num_dossier->loadMatchingObject()) {
          $newVenue->load($num_dossier->object_id);
          $newVenue->loadRefPatient();
          
          /* @todo Voir comment faire !!! 
           * même patient, même praticien, même date ?
           * */
          /*if (!$this->checkSimilarSejour($newVenue, $data['venue'])) {
            $commentaire = "Le patient et/ou praticien et/ou date d'entrée sont très différents."; 
            $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E116", $commentaire);
            $doc_valid = $domAcquittement->schemaValidate();
            
            $echange_hprim->setAckError($doc_valid, $messageAcquittement, "erreur");
            return $messageAcquittement;
          }*/
          
          // Dans le cas d'une annulation de la venue
          if ($cancel) {
            if ($messageAcquittement = $this->doNotCancelVenue($newVenue, $domAcquittement, $echange_hprim)) {
              return $messageAcquittement;
            }
          }
          
          // Mapping de la venue
          $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
          $newVenue->_num_dossier = $num_dossier->id400;
          $msgVenue = $newVenue->store();
          
          $newVenue->loadLogs();
          $modified_fields = "";
          if (is_array($newVenue->_ref_last_log->_fields)) {
            foreach ($newVenue->_ref_last_log->_fields as $field) {
              $modified_fields .= "$field \n";
            }
          } 
          
          $codes = array ($msgVenue ? "A103" : "I102", "I103", $cancel ? "A130" : null);
          if ($msgVenue) {
            $avertissement = $msgVenue." ";
          } else {
            $commentaire .= "Venue : $newVenue->_id. Les champs mis à jour sont les suivants : $modified_fields. Num dossier : $num_dossier->id400.";
          }
        }
        
        // Cas 2.2 : idCible non connu 
        else {
          if (is_numeric($num_dossier->id400) && (strlen($num_dossier->id400) <= 6)) {
            // Mapping de la venue
            $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
            
            // Dans le cas d'une annulation de la venue
            if ($cancel) {
              if ($messageAcquittement = $this->doNotCancelVenue($newVenue, $domAcquittement, $echange_hprim)) {
                return $messageAcquittement;
              }
            }
          
            // Si serveur et pas de num_dos sur la venue
            $newVenue->_no_num_dos = 1;
            $msgVenue = $newVenue->store();
            
            $num_dossier->object_id = $newVenue->_id;
            $num_dossier->last_update = mbDateTime();
            $msgNumDossier = $num_dossier->store();
        
            $newVenue->_num_dossier = $num_dossier->id400;
            // Si serveur et on a un num_dos sur la venue
            $newVenue->_no_num_dos = 0;
            $msgVenue = $newVenue->store();
                      
            $codes = array ($msgVenue ? "A102" : "I101", $msgNumDossier ? "A105" : "A101", $cancel ? "A130" : null);
            if ($msgVenue) {
              $avertissement = $msgVenue." ";
            } else {
              $commentaire = "Venue : $newVenue->_id. Numéro de dossier : $num_dossier->id400.";
            }
          }
        }
        
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
          
        $echange_hprim->_acquittement = $messageAcquittement;
        $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
        $echange_hprim->setObjectIdClass("CSejour", $newVenue->_id);
        $echange_hprim->store();
        
        return $messageAcquittement;
      }
      
      $id400 = new CIdSante400();
      //Paramétrage de l'id 400
      $id400->object_class = "CSejour";
      $id400->tag          = $dest_hprim->_tag_sejour;
      $id400->id400        = $data['idSourceVenue'];
      
      // Cas 3 : idSource fourni et retrouvé : la venue existe sur le SMP
      if($id400->loadMatchingObject()) {
        // Identifiant de la venue sur le SMP
        $idVenueSMP = $id400->object_id;
        // Cas 3.1 : idCible non fourni
        if(!$data['idCibleVenue']) {
          if ($newVenue->load($idVenueSMP)) {
            // Dans le cas d'une annulation de la venue
            if ($cancel) {
              if ($messageAcquittement = $this->doNotCancelVenue($newVenue, $domAcquittement, $echange_hprim)) {
                return $messageAcquittement;
              }
            }
          
            // Mapping de la venue
            $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);

            // Création du numéro de dossier
            $num_dossier = new CIdSante400();
            //Paramétrage de l'id 400
            $num_dossier->object_class = "CSejour";
            $num_dossier->tag = CAppUI::conf("sip tag_dossier");
            $num_dossier->object_id = $idVenueSMP;

            $mutexSej->acquire();
            // Chargement du dernier numéro de dossier s'il existe
            if (!$num_dossier->loadMatchingObject("id400 DESC")) {
              // Incrementation de l'id400
              $num_dossier->id400++;
              $num_dossier->id400 = str_pad($num_dossier->id400, 6, '0', STR_PAD_LEFT);

              $num_dossier->_id = null;
              $num_dossier->last_update = mbDateTime();
              $msgNumDossier = $num_dossier->store();
                
              $_num_dos_create = true;
            }
            $mutexSej->release();

            $newVenue->_num_dossier = $num_dossier->_id;
            $msgVenue = $newVenue->store();

            $newVenue->loadLogs();
            $modified_fields = "";
            if (is_array($newVenue->_ref_last_log->_fields)) {
              foreach ($newVenue->_ref_last_log->_fields as $field) {
                $modified_fields .= "$field \n";
              }
            }
             
            $codes = array ($msgVenue ? "A103" : "I102", $msgNumDossier ? "A105" : $_num_dos_create ? "I106" : "I108", $cancel ? "A130" : null);
            if ($msgVenue || $msgNumDossier) {
              $avertissement = $msgVenue." ".$msgNumDossier;
            } else {
              $commentaire = "Venue : $newVenue->_id. Les champs mis à jour sont les suivants : $modified_fields. Numéro de dossier : $num_dossier->id400.";
            }
            $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
            $doc_valid = $domAcquittement->schemaValidate();
            $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
              
            $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
          }
        }
        // Cas 3.2 : idCible fourni
        else {
          $num_dossier = new CIdSante400();
          //Paramétrage de l'id 400
          $num_dossier->object_class = "CSejour";
          $num_dossier->tag = CAppUI::conf("sip tag_dossier");

          $num_dossier->id400 = $data['idCibleVenue'];
          // Cas 3.2.1 : idCible connu
          if ($num_dossier->loadMatchingObject()) {
            // Acquittement d'erreur idSource et idCible incohérent
            if ($idVenueSMP != $num_dossier->object_id) {
              $commentaire = "L'identifiant source fait référence à la venue : $idVenueSMP et l'identifiant cible à la venue : $num_dossier->object_id.";
              $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E104", $commentaire);
              $doc_valid = $domAcquittement->schemaValidate();
              $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
              $echange_hprim->_acquittement = $messageAcquittement;
              $echange_hprim->statut_acquittement = "erreur";
              $echange_hprim->date_echange = mbDateTime();
              $echange_hprim->setObjectIdClass("CSejour", $newVenue->_id);
              $echange_hprim->store();
              
              return $messageAcquittement;
            } else {
              $newVenue->load($num_dossier->object_id);
              
              // Dans le cas d'une annulation de la venue
              if ($cancel) {
                if ($messageAcquittement = $this->doNotCancelVenue($newVenue, $domAcquittement, $echange_hprim)) {
                  return $messageAcquittement;
                }
              }
          
              // Mapping de la venue
              $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
              $newVenue->_num_dossier = $num_dossier->id400;
              $msgVenue = $newVenue->store();
              
              $newVenue->loadLogs();
              $modified_fields = "";
              if (is_array($newVenue->_ref_last_log->_fields)) {
                foreach ($newVenue->_ref_last_log->_fields as $field) {
                  $modified_fields .= "$field \n";
                }
              }
              $codes = array ($msgVenue ? "A103" : "I102", $cancel ? "A130" : null);
              if ($msgVenue) {
                $avertissement = $msgVenue." ";
              } else {
                $commentaire = "Venue : $newVenue->_id. Les champs mis à jour sont les suivants : $modified_fields.";
              }
              $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
              $doc_valid = $domAcquittement->schemaValidate();
              $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
              $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
            }
          }
          // Cas 3.2.2 : idCible non connu
          else {
            $commentaire = "L'identifiant source fait référence à la venue : $idVenueSMP et l'identifiant cible n'est pas connu.";
            $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E103", $commentaire);
            $doc_valid = $domAcquittement->schemaValidate();
            $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
            $echange_hprim->statut_acquittement = "erreur";
            $echange_hprim->_acquittement = $messageAcquittement;
            $echange_hprim->date_echange = mbDateTime();
            $echange_hprim->setObjectIdClass("CSejour", $newVenue->_id);
            $echange_hprim->store();
    
            return $messageAcquittement;
          }
        }
      }    
      // Cas 4 : Venue n'existe pas sur le SMP
      else {
        // Mapping de la venue
        $newVenue = $this->mappingVenue($data['venue'], $newVenue);
        // Cas 4.1 : Venue retrouvé  (patient, date d'entrée, date de sortie)     
        if ($newVenue->loadMatchingSejour()) {
          // Dans le cas d'une annulation de la venue
          if ($cancel) {
            if ($messageAcquittement = $this->doNotCancelVenue($newVenue, $domAcquittement, $echange_hprim)) {
              return $messageAcquittement;
            }
          }
          
          // Mapping de la venue
          $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
          // Si serveur et pas de numéro de dossier sur la venue
          $newVenue->_no_num_dos = 1;
          $msgVenue = $newVenue->store();

          $newVenue->loadLogs();
          $modified_fields = "";
          if (is_array($newVenue->_ref_last_log->_fields)) {
            foreach ($newVenue->_ref_last_log->_fields as $field) {
              $modified_fields .= "$field \n";
            }
          }
          $_modif_venue = true; 
          $commentaire = "Venue : $newVenue->_id.  Les champs mis à jour sont les suivants : $modified_fields.";           
        } 
        // Cas 4.2 : Venue non retrouvé
        else {
          // Dans le cas d'une annulation de la venue
          if ($cancel) {
            if ($messageAcquittement = $this->doNotCancelVenue($newVenue, $domAcquittement, $echange_hprim)) {
              return $messageAcquittement;
            }
            
            $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
          }
          
          // Si serveur et pas de numéro de dossier sur la venue
          $newVenue->_no_num_dos = 1;
          $msgVenue = $newVenue->store();
        
          $commentaire = "Venue : $newVenue->_id. ";
        }
        
        // Création de l'identifiant externe TAG CIP + idSource
        $id400Venue = new CIdSante400();
        //Paramétrage de l'id 400
        $id400Venue->object_class = "CSejour";
        $id400Venue->tag          = $dest_hprim->_tag_sejour;
        $id400Venue->id400        = $data['idSourceVenue'];
        $id400Venue->object_id    = $newVenue->_id;
        $id400Venue->_id          = null;
        $id400Venue->last_update  = mbDateTime();
        $msgID400 = $id400Venue->store();
        
        // Création du numéro de dossier
        $num_dossier = new CIdSante400();
        //Paramétrage de l'id 400
        $num_dossier->object_class = "CSejour";
        $num_dossier->tag = CAppUI::conf("sip tag_dossier");
        
        $mutexSej->acquire();
        // Cas num dossier fourni
        if ($data['idCibleVenue']) {
          $num_dossier->id400 = str_pad($data['idCibleVenue'], 6, '0', STR_PAD_LEFT);

          // Num dossier fourni non connu
          if (!$num_dossier->loadMatchingObject() && is_numeric($num_dossier->id400) && (strlen($num_dossier->id400) <= 6)) {
            $_code_NumDos = "A101";
          }
          // Num dossier fourni connu
          else {  
            // Si num dossier est identique à la venue retrouvée
            if ($num_dossier->object_id == $newVenue->_id) {
              $_code_NumDos = "I130";
            } else {
              // Annule le num dossier envoyé          
              $num_dossier->id400 = null;
              $num_dossier->loadMatchingObject("id400 DESC");
  
              // Incrementation de l'id400
              $num_dossier->id400++;
              $num_dossier->id400 = str_pad($num_dossier->id400, 6, '0', STR_PAD_LEFT);
              $num_dossier->_id = null;
               
              $_code_NumDos = "I109";
            }
          }
        } else { 
          // Si la venue a été retrouvée on a déjà le num dossier
          if ($_modif_venue) {
            $num_dossier->object_id = $newVenue->_id;
            $num_dossier->loadMatchingObject();
            $_code_NumDos = "I126";
          } else {
            $num_dossier->loadMatchingObject("id400 DESC");
  
            // Incrementation de l'id400
            $num_dossier->id400++;
            $num_dossier->id400 = str_pad($num_dossier->id400, 6, '0', STR_PAD_LEFT);
  
            $num_dossier->_id = null;
  
            $_code_NumDos = "I106";
          }          
        }

        $num_dossier->object_id = $newVenue->_id;

        $num_dossier->last_update = mbDateTime();
        $msgVenue = $num_dossier->store();

        $mutexSej->release();
        
        $newVenue->_IPP = $num_dossier->id400;
        // Si serveur et on a un num dossier sur la venue
        $newVenue->_no_num_dos = 0;
        $msgVenue = $newVenue->store();
        
        if ($cancel) {
          $codes[] = "A130";
        }
        
        $codes = array ($msgVenue ? ($_modif_venue ? "A103" : "A102") : "I101", 
                        $msgID400 ? "A104" : "I104", 
                        $msgNumDossier ? "A105" : $_code_NumDos, 
                        $cancel ? "A130" : null);
        if ($msgVenue || $msgID400 || $msgNumDossier) {
          $avertissement = $msgVenue." ".$msgID400." ".$msgNumDossier;
        } else {
          $commentaire = "Venue : $newVenue->_id. Identifiant externe : $id400Venue->id400. IPP : $num_dossier->id400.";
        }
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
        $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
      }
    }
    // Si CIP
    else {      
      // Acquittement d'erreur : identifiants source et cible non fournis pour le patient / venue
      if (!$data['idSourceVenue'] && !$data['idCibleVenue']) {
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E100");
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "erreur");
        return $messageAcquittement;
      }
      
      $num_dossier = new CIdSante400();
      //Paramétrage de l'id 400
      $num_dossier->object_class = "CSejour";
      $num_dossier->tag = $dest_hprim->_tag_sejour;
      $num_dossier->id400 = $data['idSourceVenue'];
      
      // idSource non connu
      if(!$num_dossier->loadMatchingObject()) {
        // idCible fourni
        if ($data['idCibleVenue']) {
          if ($newVenue->load($data['idCibleVenue'])) {
            // Dans le cas d'une annulation de la venue
            if ($cancel) {
              if ($messageAcquittement = $this->doNotCancelVenue($newVenue, $domAcquittement, $echange_hprim)) {
                return $messageAcquittement;
              }
            }
            
            // Recherche d'un num dossier déjà existant pour cette venue 
            // Mise en trash du numéro de dossier reçu
            $newVenue->loadNumDossier();
            if ($newVenue->_num_dossier) {
                $num_dossier->_trash = true;
            } else {
               // Mapping du séjour si pas de numéro de dossier
              $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
              
              // Notifier les autres destinataires
              $newVenue->_hprim_initiateur_group_id = $dest_hprim->group_id;
              $msgVenue = $newVenue->store();
          
              $newVenue->loadLogs();
              $modified_fields = "";
              if ($newVenue->_ref_last_log->_fields) {
                foreach ($newVenue->_ref_last_log->_fields as $field) {
                  $modified_fields .= "$field \n";
                }
              }
              $_code_NumDos = "I121";
              $_code_Venue = true; 
              $commentaire = "Séjour modifiée : $newVenue->_id. Les champs mis à jour sont les suivants : $modified_fields.";
            }
          } else {
            $_code_NumDos = "I120";
          }
        } else {
          $_code_NumDos = "I122";  
        }
        if (!$newVenue->_id) {   
          // Mapping du séjour
          $newVenue->_num_dossier = $num_dossier->id400;
          $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
                    
          // Séjour retrouvé
          if (CAppUI::conf("hprimxml strictSejourMatch")) {
            if ($newVenue->loadMatchingSejour(null, true)) {
              // Dans le cas d'une annulation de la venue
              if ($cancel) {
                if ($messageAcquittement = $this->doNotCancelVenue($newVenue, $domAcquittement, $echange_hprim)) {
                  return $messageAcquittement;
                }
              }
              
              // Recherche d'un num dossier déjà existant pour cette venue 
              // Mise en trash du numéro de dossier reçu
              $newVenue->loadNumDossier();
              if ($newVenue->_num_dossier) {
                $num_dossier->_trash = true;
              } else {
                // Notifier les autres destinataires
                $newVenue->_hprim_initiateur_group_id = $dest_hprim->group_id;
                // Mapping du séjour
                $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
                
                $msgVenue = $newVenue->store();

                $newVenue->loadLogs();
                $modified_fields = "";
                if (is_array($newVenue->_ref_last_log->_fields)) {
                  foreach ($newVenue->_ref_last_log->_fields as $field) {
                    $modified_fields .= "$field \n";
                  }
                }
                $_code_NumDos = "A121";
                $_code_Venue = true;
                $commentaire = "Séjour modifiée : $newVenue->_id.  Les champs mis à jour sont les suivants : $modified_fields.";
              }
            }
          } else {
            $collision = $newVenue->getCollisions();
            if (count($collision) == 1) {
              $newVenue = reset($collision);
              // Dans le cas d'une annulation de la venue
              if ($cancel) {
                if ($messageAcquittement = $this->doNotCancelVenue($newVenue, $domAcquittement, $echange_hprim)) {
                  return $messageAcquittement;
                }
              }
              
              // Recherche d'un num dossier déjà existant pour cette venue 
              // Mise en trash du numéro de dossier reçu
              $newVenue->loadNumDossier();
              if ($newVenue->_num_dossier) {
                $num_dossier->_trash = true;
              } else {
                // Mapping du séjour
                $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
                $msgVenue = $newVenue->store();
  
                $newVenue->loadLogs();
                $modified_fields = "";
                if (is_array($newVenue->_ref_last_log->_fields)) {
                  foreach ($newVenue->_ref_last_log->_fields as $field) {
                    $modified_fields .= "$field \n";
                  }
                }
                $_code_NumDos = "A122";
                $_code_Venue = true;
                $commentaire = "Séjour modifiée : $newVenue->_id.  Les champs mis à jour sont les suivants : $modified_fields.";
              }
            }
          }
          if (!$newVenue->_id && !isset($num_dossier->_trash)) {
            // Notifier les autres destinataires
            $newVenue->_hprim_initiateur_group_id = $dest_hprim->group_id;
            // Mapping du séjour
            $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
          
            $msgVenue = $newVenue->store();
            $commentaire = "Séjour créé : $newVenue->_id. ";
          }
        }
        
        if (isset($num_dossier->_trash)) {
          $num_dossier->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_trash').$dest_hprim->_tag_sejour;
          $num_dossier->loadMatchingObject();
          $codes = array("I125");
          $commentaire = "Sejour non récupéré. Impossible d'associer le numéro de dossier.";
        }
        
        if ($cancel) {
          $codes[] = "A130";
          $num_dossier->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_trash').$dest_hprim->_tag_sejour;
        }
        
        $num_dossier->object_id = $newVenue->_id;
        $num_dossier->last_update = mbDateTime();
        $msgNumDossier = $num_dossier->store();
        
        if (!isset($num_dossier->_trash)) { 
          $codes = array ($msgVenue ? ($_code_Venue ? "A103" : "A102") : ($_code_Venue ? "I102" : "I101"), 
                        $msgNumDossier ? "A105" : $_code_NumDos);
        }
        
        if ($msgVenue || $msgNumDossier) {
          $avertissement = $msgVenue." ".$msgNumDossier;
        } else {
          if (!isset($num_dossier->_trash)) {
            $commentaire .= "Numéro dossier créé : $num_dossier->id400.";
          }
        }
      } 
      // idSource connu
      else {
        $newVenue->_num_dossier = $num_dossier->id400;
        $newVenue->load($num_dossier->object_id);
        // Dans le cas d'une annulation de la venue
        if ($cancel) {
          if ($messageAcquittement = $this->doNotCancelVenue($newVenue, $domAcquittement, $echange_hprim)) {
            return $messageAcquittement;
          }
        }
        
        // Mapping du séjour
        $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
                        
        // idCible non fourni
        if (!$data['idCibleVenue']) {
          $_code_NumDos = "I123"; 
        } else {
          $tmpVenue = new CSejour();
          // idCible connu
          if ($tmpVenue->load($data['idCibleVenue'])) {
            if ($tmpVenue->_id != $num_dossier->object_id) {
              $commentaire = "L'identifiant source fait référence au séjour : $num_dossier->object_id et l'identifiant cible au séjour : $tmpVenue->_id.";
              $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E104", $commentaire);
              $doc_valid = $domAcquittement->schemaValidate();
              
              $echange_hprim->setAckError($doc_valid, $messageAcquittement, "erreur");
              return $messageAcquittement;
            }
            $_code_NumDos = "I124"; 
          }
          // idCible non connu
          else {
            $_code_NumDos = "A120";
          }
        }
        // Notifier les autres destinataires
        $newVenue->_hprim_initiateur_group_id = $dest_hprim->group_id;
        $msgVenue = $newVenue->store();
        
        $newVenue->loadLogs();
        $modified_fields = "";
        if (is_array($newVenue->_ref_last_log->_fields)) {
          foreach ($newVenue->_ref_last_log->_fields as $field) {
            $modified_fields .= "$field \n";
          }
        }
        $codes = array($msgVenue ? "A103" : "I102", $_code_NumDos);
        
        if ($cancel) {
          $codes[] = "A130";
          $num_dossier->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_trash').$dest_hprim->_tag_sejour;
          $num_dossier->last_update = mbDateTime();
          $msgNumDossier = $num_dossier->store();
        }

        if ($msgVenue || $msgNumDossier) {
          $avertissement = $msgVenue." ".$msgNumDossier;
        } else {
          $commentaire = "Séjour modifiée : $newVenue->_id. Les champs mis à jour sont les suivants : $modified_fields. Numéro dossier associé : $num_dossier->id400.";
        }
      }
      
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : substr($commentaire, 0, 4000)); 
      $doc_valid = $domAcquittement->schemaValidate();
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
      $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
    }
    $echange_hprim->_acquittement = $messageAcquittement;
    $echange_hprim->date_echange = mbDateTime();
    $echange_hprim->setObjectIdClass("CSejour", $newVenue->_id);
    $echange_hprim->store();

    return $messageAcquittement;
  }
}

?>