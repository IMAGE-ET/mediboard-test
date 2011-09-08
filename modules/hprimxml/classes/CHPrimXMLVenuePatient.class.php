<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "CHPrimXMLEvenementsPatients");

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
    if (CAppUI::conf("hprimxml trash_numdos_sejour_cancel") && $mbVenue->annule && $mbVenue->_NDA) {
      $NDA = CIdSante400::getMatch("CSejour", $this->_ref_echange_hprim->_ref_receiver->_tag_sejour, $mbVenue->_NDA);
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
    
    // Cas 2 : Traitement de la venue
    $dom_acq = new CHPrimXMLAcquittementsPatients();
    $dom_acq->_identifiant_acquitte = $data['identifiantMessage'];
    $dom_acq->_sous_type_evt        = $this->sous_type;
    $dom_acq->_ref_echange_hprim    = $echg_hprim;
    
    // Traitement du message des erreurs
    $avertissement = $msgID400 = $msgVenue = $msgNumDossier = "";    
    $_code_Venue   = $_code_NumDos = $_num_dos_create = $_modif_venue = false;
    
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
        $nda = CIdSante400::getMatch("CSejour", CAppUI::conf("sip tag_dossier"), str_pad($data['idCibleVenue'], 6, '0', STR_PAD_LEFT));
        // Cas 2.1 : idCible connu
        if ($nda->_id) {
          $newVenue->load($nda->object_id);
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
          // Store de la venue
          $msgPatient = CEAISejour::storeSejour($newVenue, $nda->id400);
          
          $modified_fields = CEAISejour::getModifiedFields($newVenue);
          
          $codes = array ($msgVenue ? "A103" : "I102", "I103", $cancel ? "A130" : null);
          if ($msgVenue) {
            $avertissement = $msgVenue." ";
          } else {
            $commentaire .= "Venue : $newVenue->_id. Les champs mis à jour sont les suivants : $modified_fields. Num dossier : $nda->id400.";
          }
        }
        
        // Cas 2.2 : idCible non connu 
        else {
          if (is_numeric($nda->id400) && (strlen($nda->id400) <= 6)) {
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
            
            $msgNumDossier = CEAISejour::storeNDA($nda, $newVenue);
            
            $newVenue->_NDA = $nda->id400;
            // Si serveur et on a un num_dos sur la venue
            $newVenue->_no_num_dos = 0;
            $msgVenue = $newVenue->store();
                      
            $codes = array ($msgVenue ? "A102" : "I101", $msgNumDossier ? "A105" : "A101", $cancel ? "A130" : null);
            if ($msgVenue) {
              $avertissement = $msgVenue." ";
            } else {
              $commentaire = "Venue : $newVenue->_id. Numéro de dossier : $nda->id400.";
            }
          }
        }
        
        return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $commentaire, $newVenue);
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
            $nda = new CIdSante400();
            //Paramétrage de l'id 400
            CEAISejour::NDASMPSetting($nda, $idVenueSMP);
            
            $mutexSej = new CMbSemaphore("sip-numdos"); 
            $mutexSej->acquire();
            // Chargement du dernier numéro de dossier s'il existe
            if (!$nda->loadMatchingObject("id400 DESC")) {
              // Incrementation de l'id400
              CEAISejour::NDASMPIncrement($nda);

              $msgNumDossier = CEAISejour::storeNDA($nda);
                
              $_num_dos_create = true;
            }
            $mutexSej->release();
            
            // Store de la venue
            $msgVenue = CEAISejour::storeSejour($newVenue, $nda->_id);

            $modified_fields = CEAISejour::getModifiedFields($newVenue);
             
            $codes = array ($msgVenue ? "A103" : "I102", $msgNumDossier ? "A105" : $_num_dos_create ? "I106" : "I108", $cancel ? "A130" : null);
            if ($msgVenue || $msgNumDossier) {
              $avertissement = $msgVenue." ".$msgNumDossier;
            } else {
              $commentaire = "Venue : $newVenue->_id. Les champs mis à jour sont les suivants : $modified_fields. Numéro de dossier : $nda->id400.";
            }
            
            return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $commentaire, $newVenue);
          }
        }
        // Cas 3.2 : idCible fourni
        else {
          $nda = CIdSante400::getMatch("CSejour", CAppUI::conf("sip tag_dossier"), $data['idCibleVenue']);
          // Cas 3.2.1 : idCible connu
          if ($nda->_id) {
            // Acquittement d'erreur idSource et idCible incohérent
            if ($idVenueSMP != $nda->object_id) {
              $commentaire = "L'identifiant source fait référence à la venue : $idVenueSMP et l'identifiant cible à la venue : $nda->object_id.";
              return $echg_hprim->setAckError($dom_acq, "E104", $commentaire, $newVenue);
            } else {
              $newVenue->load($nda->object_id);
              
              // Dans le cas d'une annulation de la venue
              if ($cancel) {
                if ($msgAcq = $this->doNotCancelVenue($newVenue, $dom_acq, $echg_hprim)) {
                  return $msgAcq;
                }
              }
          
              // Mapping de la venue
              $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
              // Store de la venue
              $msgVenue = CEAISejour::storeSejour($newVenue, $nda->id400);
              
              $modified_fields = CEAISejour::getModifiedFields($newVenue);
              
              $codes = array ($msgVenue ? "A103" : "I102", $cancel ? "A130" : null);
              if ($msgVenue) {
                $avertissement = $msgVenue." ";
              } else {
                $commentaire = "Venue : $newVenue->_id. Les champs mis à jour sont les suivants : $modified_fields.";
              }
  
              return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $commentaire, $newVenue); 
            }
          }
          // Cas 3.2.2 : idCible non connu
          else {
            $commentaire = "L'identifiant source fait référence à la venue : $idVenueSMP et l'identifiant cible n'est pas connu.";
            return $echg_hprim->setAckError($dom_acq, "E103", $commentaire, $newVenue);
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

          $modified_fields = CEAISejour::getModifiedFields($newVenue);
          
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
        $msgID400 = CEAISejour::storeID400CIP($id400Venue, $sender, $data['idSourceVenue'], $newVenue);
        
        // Création du numéro de dossier
        $nda = new CIdSante400();
        //Paramétrage de l'id 400
        CEAISejour::NDASMPSetting($nda);
        
        $mutexSej = new CMbSemaphore("sip-numdos"); 
        $mutexSej->acquire();
        // Cas num dossier fourni
        if ($data['idCibleVenue']) {
          $nda->id400 = str_pad($data['idCibleVenue'], 6, '0', STR_PAD_LEFT);

          // Num dossier fourni non connu
          if (!$nda->loadMatchingObject() && is_numeric($nda->id400) && (strlen($nda->id400) <= 6)) {
            $_code_NumDos = "A101";
          }
          // Num dossier fourni connu
          else {  
            // Si num dossier est identique à la venue retrouvée
            if ($nda->object_id == $newVenue->_id) {
              $_code_NumDos = "I130";
            } else {
              // Annule le num dossier envoyé          
              $nda->id400 = null;
              $nda->loadMatchingObject("id400 DESC");
  
              // Incrementation de l'id400
              CEAISejour::NDASMPIncrement($nda);

              $_code_NumDos = "I109";
            }
          }
        } else { 
          // Si la venue a été retrouvée on a déjà le num dossier
          if ($_modif_venue) {
            $nda->object_id = $newVenue->_id;
            $nda->loadMatchingObject();
            $_code_NumDos = "I126";
          } else {
            $nda->loadMatchingObject("id400 DESC");
  
            // Incrementation de l'id400
            CEAISejour::NDASMPIncrement($nda);
  
            $_code_NumDos = "I106";
          }          
        }
        $mutexSej->release();
        
        $msgVenue = CEAISejour::storeNDA($nda, $newVenue);        
        
        $newVenue->_IPP = $nda->id400;
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
          $commentaire = "Venue : $newVenue->_id. Identifiant externe : $id400Venue->id400. IPP : $nda->id400.";
        }
        
        return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $commentaire, $newVenue);
      }
    }
    // Si CIP
    else {      
      // Acquittement d'erreur : identifiants source et cible non fournis pour le patient / venue
      if (!$data['idSourceVenue'] && !$data['idCibleVenue']) {
        return $echg_hprim->setAckError($dom_acq, "E100", $commentaire, $newVenue);
      }
      
      $nda = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $data['idSourceVenue']);
      // idSource non connu
      if (!$nda->_id) {
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
                $nda->_trash = true;
            } else {
               // Mapping du séjour si pas de numéro de dossier
              $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
              
              // Notifier les autres destinataires autre que le sender
              $newVenue->_hprim_initiateur_group_id = $sender->group_id;
              $msgVenue = $newVenue->store();
          
              $modified_fields = CEAISejour::getModifiedFields($newVenue);
              
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
          $newVenue->_NDA = $nda->id400;
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
                $nda->_trash = true;
              } else {
                
                // Mapping du séjour
                $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);

                // Notifier les autres destinataires autre que le sender
                $newVenue->_hprim_initiateur_group_id = $sender->group_id;
                $msgVenue = $newVenue->store();

                $modified_fields = CEAISejour::getModifiedFields($newVenue);
                
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
                $nda->_trash = true;
              } else {
                // Mapping du séjour
                $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
                $msgVenue = $newVenue->store();

                $modified_fields = CEAISejour::getModifiedFields($newVenue);
                
                $_code_NumDos = "A122";
                $_code_Venue = true;
                $commentaire = "Séjour modifiée : $newVenue->_id.  Les champs mis à jour sont les suivants : $modified_fields.";
              }
            }
          }
          if (!$newVenue->_id && !isset($nda->_trash)) {
            // Mapping du séjour
            $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
            
            // Notifier les autres destinataires autre que le sender
            $newVenue->_hprim_initiateur_group_id = $sender->group_id;
            $msgVenue = $newVenue->store();
            
            $commentaire = "Séjour créé : $newVenue->_id. ";
          }
        }
        
        if (isset($nda->_trash)) {
          $nda->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_trash').$sender->_tag_sejour;
          $nda->loadMatchingObject();
          $codes = array("I125");
          $commentaire = "Sejour non récupéré. Impossible d'associer le numéro de dossier.";
        }
        
        if ($cancel) {
          $codes[] = "A130";
          $nda->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_trash').$sender->_tag_sejour;
        }
        
        $msgNumDossier = CEAISejour::storeNDA($nda, $newVenue);
        
        if (!isset($nda->_trash)) { 
          $codes = array ($msgVenue ? ($_code_Venue ? "A103" : "A102") : ($_code_Venue ? "I102" : "I101"), 
                        $msgNumDossier ? "A105" : $_code_NumDos);
        }
        
        if ($msgVenue || $msgNumDossier) {
          $avertissement = $msgVenue." ".$msgNumDossier;
        } else {
          if (!isset($nda->_trash)) {
            $commentaire .= "Numéro dossier créé : $nda->id400.";
          }
        }
      } 
      // idSource connu
      else {
        $newVenue->_NDA = $nda->id400;
        $newVenue->load($da->object_id);
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
            if ($tmpVenue->_id != $nda->object_id) {
              $commentaire = "L'identifiant source fait référence au séjour : $nda->object_id et l'identifiant cible au séjour : $tmpVenue->_id.";
              return $dom_acq->generateAcquittementsError("E104", $commentaire, $newVenue);
            }
            $_code_NumDos = "I124"; 
          }
          // idCible non connu
          else {
            $_code_NumDos = "A120";
          }
        }
        // Notifier les autres destinataires autre que le sender
        $newVenue->_hprim_initiateur_group_id = $sender->group_id;
        $msgVenue = $newVenue->store();
        
        $modified_fields = CEAISejour::getModifiedFields($newVenue);
        
        $codes = array($msgVenue ? "A103" : "I102", $_code_NumDos);
        
        if ($cancel) {
          $codes[] = "A130";
          $nda->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_trash').$sender->_tag_sejour;
          $nda->last_update = mbDateTime();
          $msgNumDossier = $nda->store();
        }

        if ($msgVenue || $msgNumDossier) {
          $avertissement = $msgVenue." ".$msgNumDossier;
        } else {
          $commentaire = "Séjour modifiée : $newVenue->_id. Les champs mis à jour sont les suivants : $modified_fields. Numéro dossier associé : $nda->id400.";
        }
      }
      
      return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $commentaire, $newVenue);
    }
  }
}

?>