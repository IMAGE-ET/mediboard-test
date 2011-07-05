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
        
    // Cas d'une annulation dans Mediboard on passe en trash le num dossier
    if (CAppUI::conf("hprimxml trash_numdos_sejour_cancel") && $mbVenue->annule && $mbVenue->_num_dossier) {
      $NDA = CIdSante400::getMatch("CSejour", $this->_ref_echange_hprim->_ref_receiver->_tag_sejour, $mbVenue->_num_dossier);
      if ($NDA->_id) {
        $NDA->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_trash').$this->_ref_echange_hprim->_ref_receiver->_tag_sejour;
        $NDA->store();
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
   * @param CHPrimXMLAcquittementsPatients $dom_acq
   * @param CEchangeHprim $echg_hprim
   * @param CPatient $newPatient
   * @param CSejour $newSejour
   * @param array $data
   * @return CHPrimXMLAcquittementsPatients $msgAcq 
   **/
  function venuePatient($dom_acq, $newPatient, $data, &$newVenue = null) {
    $echg_hprim = $this->_ref_echange_hprim;
    
    // Cas 1 : Traitement du patient
    $domEnregistrementPatient = new CHPrimXMLEnregistrementPatient();
    $domEnregistrementPatient->_ref_echange_hprim = $echg_hprim;
    $msgAcq = $domEnregistrementPatient->enregistrementPatient($dom_acq, $newPatient, $data);
    if ($echg_hprim->statut_acquittement != "OK") {
      return $msgAcq;
    }
    
    $dom_acq = new CHPrimXMLAcquittementsPatients();
    $dom_acq->_identifiant_acquitte = $data['identifiantMessage'];
    $dom_acq->_sous_type_evt        = $this->sous_type;
    $dom_acq->_ref_echange_hprim    = $echg_hprim;
    
    // Traitement du message des erreurs
    $avertissement = $msgID400 = $msgVenue = $msgNumDossier = "";    
    $_code_Venue = $_code_NumDos = $_num_dos_create = $_modif_venue = false;
    $mutexSej = new CMbSemaphore("sip-numdos"); 
    
    $sender = $echg_hprim->_ref_sender;
    
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
    $newVenue->group_id = $sender->group_id;
    
    // Si SIP
    if (CAppUI::conf('sip server')) {
      // Cas 2 : idSource non fourni, idCible fourni
      if (!$data['idSourceVenue'] && $data['idCibleVenue']) {
        $num_dossier = CIdSante400::getMatch("CSejour", CAppUI::conf("sip tag_dossier"), str_pad($data['idCibleVenue'], 6, '0', STR_PAD_LEFT));
        // Cas 2.1 : idCible connu
        if ($num_dossier->_id) {
          $newVenue->load($num_dossier->object_id);
          $newVenue->loadRefPatient();
          
          /* @todo Voir comment faire !!! 
           * même patient, même praticien, même date ?
           * */
          /*if (!$this->checkSimilarSejour($newVenue, $data['venue'])) {
            $commentaire = "Le patient et/ou praticien et/ou date d'entrée sont très différents."; 
            $msgAcq = $dom_acq->generateAcquittements("erreur", "E116", $commentaire);
            $doc_valid = $dom_acq->schemaValidate();
            
            $echg_hprim->setAckError($doc_valid, $msgAcq, "erreur");
            return $msgAcq;
          }*/
          
          // Dans le cas d'une annulation de la venue
          if ($cancel) {
            if ($msgAcq = $this->doNotCancelVenue($newVenue, $dom_acq, $echg_hprim)) {
              return $msgAcq;
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
              if ($msgAcq = $this->doNotCancelVenue($newVenue, $dom_acq, $echg_hprim)) {
                return $msgAcq;
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
        
        $msgAcq = $dom_acq->generateAcquittements($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
        $doc_valid = $dom_acq->schemaValidate();
        $echg_hprim->acquittement_valide = $doc_valid ? 1 : 0;
          
        $echg_hprim->_acquittement = $msgAcq;
        $echg_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
        $echg_hprim->setObjectIdClass("CSejour", $newVenue->_id);
        $echg_hprim->store();
        
        return $msgAcq;
      }
      
      $id400 = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $data['idSourceVenue']);
      // Cas 3 : idSource fourni et retrouvé : la venue existe sur le SMP
      if ($id400->_id) {
        // Identifiant de la venue sur le SMP
        $idVenueSMP = $id400->object_id;
        // Cas 3.1 : idCible non fourni
        if (!$data['idCibleVenue']) {
          if ($newVenue->load($idVenueSMP)) {
            // Dans le cas d'une annulation de la venue
            if ($cancel) {
              if ($msgAcq = $this->doNotCancelVenue($newVenue, $dom_acq, $echg_hprim)) {
                return $msgAcq;
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
            $msgAcq = $dom_acq->generateAcquittements($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
            $doc_valid = $dom_acq->schemaValidate();
            $echg_hprim->acquittement_valide = $doc_valid ? 1 : 0;
              
            $echg_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
          }
        }
        // Cas 3.2 : idCible fourni
        else {
          $num_dossier = CIdSante400::getMatch("CSejour", CAppUI::conf("sip tag_dossier"), $data['idCibleVenue']);
          // Cas 3.2.1 : idCible connu
          if ($num_dossier->_id) {
            // Acquittement d'erreur idSource et idCible incohérent
            if ($idVenueSMP != $num_dossier->object_id) {
              $commentaire = "L'identifiant source fait référence à la venue : $idVenueSMP et l'identifiant cible à la venue : $num_dossier->object_id.";
              return $dom_acq->generateAcquittementsError("E104", $commentaire, $newVenue);
            } else {
              $newVenue->load($num_dossier->object_id);
              
              // Dans le cas d'une annulation de la venue
              if ($cancel) {
                if ($msgAcq = $this->doNotCancelVenue($newVenue, $dom_acq, $echg_hprim)) {
                  return $msgAcq;
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
              $msgAcq = $dom_acq->generateAcquittements($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
              $doc_valid = $dom_acq->schemaValidate();
              $echg_hprim->acquittement_valide = $doc_valid ? 1 : 0;
              $echg_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
            }
          }
          // Cas 3.2.2 : idCible non connu
          else {
            $commentaire = "L'identifiant source fait référence à la venue : $idVenueSMP et l'identifiant cible n'est pas connu.";
            return $dom_acq->generateAcquittementsError("E103", $commentaire, $newVenue);
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
            if ($msgAcq = $this->doNotCancelVenue($newVenue, $dom_acq, $echg_hprim)) {
              return $msgAcq;
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
            if ($msgAcq = $this->doNotCancelVenue($newVenue, $dom_acq, $echg_hprim)) {
              return $msgAcq;
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
        $id400Venue->tag          = $sender->_tag_sejour;
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
        $msgAcq = $dom_acq->generateAcquittements($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
        $doc_valid = $dom_acq->schemaValidate();
        $echg_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
        $echg_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
      }
    }
    // Si CIP
    else {      
      // Acquittement d'erreur : identifiants source et cible non fournis pour le patient / venue
      if (!$data['idSourceVenue'] && !$data['idCibleVenue']) {
        return $dom_acq->generateAcquittementsError("E100", $commentaire, $newVenue);
      }
      
      $num_dossier = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $data['idSourceVenue']);
      // idSource non connu
      if (!$num_dossier->_id) {
        // idCible fourni
        if ($data['idCibleVenue']) {
          if ($newVenue->load($data['idCibleVenue'])) {
            // Dans le cas d'une annulation de la venue
            if ($cancel) {
              if ($msgAcq = $this->doNotCancelVenue($newVenue, $dom_acq, $echg_hprim)) {
                return $msgAcq;
              }
            }
            
            // Recherche d'un num dossier déjà existant pour cette venue 
            // Mise en trash du numéro de dossier reçu
            $newVenue->loadNumDossier();
            if ($this->trashNumDossier($newVenue, $sender)) {
                $num_dossier->_trash = true;
            } else {
               // Mapping du séjour si pas de numéro de dossier
              $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
              
              // Notifier les autres destinataires
              $newVenue->_hprim_initiateur_group_id = $sender->group_id;
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
            if ($newVenue->loadMatchingSejour(null, true, $sender->_configs["use_sortie_matching"])) {
              // Dans le cas d'une annulation de la venue
              if ($cancel) {
                if ($msgAcq = $this->doNotCancelVenue($newVenue, $dom_acq, $echg_hprim)) {
                  return $msgAcq;
                }
              }
              
              // Recherche d'un num dossier déjà existant pour cette venue 
              // Mise en trash du numéro de dossier reçu
              $newVenue->loadNumDossier();
              if ($this->trashNumDossier($newVenue, $sender)) {
                $num_dossier->_trash = true;
              } else {
                // Notifier les autres destinataires
                $newVenue->_hprim_initiateur_group_id = $sender->group_id;
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
                if ($msgAcq = $this->doNotCancelVenue($newVenue, $dom_acq, $echg_hprim)) {
                  return $msgAcq;
                }
              }
              
              // Recherche d'un num dossier déjà existant pour cette venue 
              // Mise en trash du numéro de dossier reçu
              $newVenue->loadNumDossier();
              if ($this->trashNumDossier($newVenue, $sender)) {
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
            $newVenue->_hprim_initiateur_group_id = $sender->group_id;
            // Mapping du séjour
            $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
          
            $msgVenue = $newVenue->store();
            $commentaire = "Séjour créé : $newVenue->_id. ";
          }
        }
        
        if (isset($num_dossier->_trash)) {
          $num_dossier->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_trash').$sender->_tag_sejour;
          $num_dossier->loadMatchingObject();
          $codes = array("I125");
          $commentaire = "Sejour non récupéré. Impossible d'associer le numéro de dossier.";
        }
        
        if ($cancel) {
          $codes[] = "A130";
          $num_dossier->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_trash').$sender->_tag_sejour;
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
          if ($msgAcq = $this->doNotCancelVenue($newVenue, $dom_acq, $echg_hprim)) {
            return $msgAcq;
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
              return $dom_acq->generateAcquittementsError("E104", $commentaire, $newVenue);
            }
            $_code_NumDos = "I124"; 
          }
          // idCible non connu
          else {
            $_code_NumDos = "A120";
          }
        }
        // Notifier les autres destinataires
        $newVenue->_hprim_initiateur_group_id = $sender->group_id;
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
          $num_dossier->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_trash').$sender->_tag_sejour;
          $num_dossier->last_update = mbDateTime();
          $msgNumDossier = $num_dossier->store();
        }

        if ($msgVenue || $msgNumDossier) {
          $avertissement = $msgVenue." ".$msgNumDossier;
        } else {
          $commentaire = "Séjour modifiée : $newVenue->_id. Les champs mis à jour sont les suivants : $modified_fields. Numéro dossier associé : $num_dossier->id400.";
        }
      }
      
      $msgAcq = $dom_acq->generateAcquittements($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : substr($commentaire, 0, 4000)); 
      $doc_valid = $dom_acq->schemaValidate();
      $echg_hprim->acquittement_valide = $doc_valid ? 1 : 0;
      $echg_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
    }
    $echg_hprim->_acquittement = $msgAcq;
    $echg_hprim->date_echange = mbDateTime();
    $echg_hprim->setObjectIdClass("CSejour", $newVenue->_id);
    $echg_hprim->store();

    return $msgAcq;
  }
}

?>