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
    /*'suppression'   => "suppression"*/
  );
  
  function __construct() {    
  	$this->sous_type = "venuePatient";
  	        
    parent::__construct();
  }
  
  function generateFromOperation($mbVenue, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $venuePatient = $this->addElement($evenementPatient, "venuePatient");
    $actionConversion = array (
      "create" => "création",
      "store"  => "modification",
      "delete" => "suppression"
    );
    $action = $actionConversion[$mbVenue->_ref_last_log->type];
    /*if ($mbVenue->annule) {
      $action = "suppression";
    }*/
    $this->addAttribute($venuePatient, "action", $action);
    
    $patient = $this->addElement($venuePatient, "patient");
    // Ajout du patient   
    $this->addPatient($patient, $mbVenue->_ref_patient, null, $referent);
    
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
        
    // Traitement final
    $this->purgeEmptyElements();
  }

  function getVenuePatientXML() {
    $xpath = new CMbXPath($this, true);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $venuePatient= $xpath->queryUniqueNode("hprim:venuePatient", $evenementPatient);

    $data['action'] = $this->getActionEvenement("hprim:venuePatient", $evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $venuePatient);
    $data['venue'] = $xpath->queryUniqueNode("hprim:venue", $venuePatient);

    $data['idSource'] = $this->getIdSource($data['patient']);
    $data['idCible'] = $this->getIdCible($data['patient']);
    
    $data['idSourceVenue'] = $this->getIdSource($data['venue']);
    $data['idCibleVenue'] = $this->getIdCible($data['venue']);
    
    return $data;
  }
  
  /**
   * Stay recording 
   * @param CHPrimXMLAcquittementsPatients $domAcquittement
   * @param CEchangeHprim $echange_hprim
   * @param CPatient $newPatient
   * @param CSejour $newSejour
   * @param array $data
   * @return CHPrimXMLAcquittementsPatients $messageAcquittement 
   **/
  function venuePatient($domAcquittement, $echange_hprim, $newPatient, $data, &$newVenue = null) {
    // Traitement du patient
    $domEnregistrementPatient = new CHPrimXMLEnregistrementPatient();
    $messageAcquittement = $domEnregistrementPatient->enregistrementPatient($domAcquittement, $echange_hprim, $newPatient, $data);
    if ($echange_hprim->statut_acquittement != "OK") {
      return $messageAcquittement;
    }
    
    $domAcquittement = new CHPrimXMLAcquittementsPatients();
    $domAcquittement->identifiant = $data['identifiantMessage'];
    $domAcquittement->destinataire = $data['idClient'];
    $domAcquittement->destinataire_libelle = $data['libelleClient'];
    
    // Traitement de la venue
    $mutexSej = new CMbSemaphore("sip-numdos"); 
    
     // Traitement du message des erreurs
    $avertissement = $msgID400 = $msgNumDossier = "";
    $_code_Venue = false;
    
    // Si CIP
    if (!CAppUI::conf('sip server')) {
      $newVenue = new CSejour();
      $newVenue->patient_id = $newPatient->_id; 
      $newVenue->group_id = CGroups::loadCurrent()->_id;
       
      $dest_hprim = new CDestinataireHprim();
      $dest_hprim->nom = $data['idClient'];
      $dest_hprim->loadMatchingObject();
    
      // Acquittement d'erreur : identifiants source et cible non fournis pour le patient / venue
      if (!$data['idSourceVenue'] && !$data['idCibleVenue']) {
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E100");
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
          
        $echange_hprim->acquittement = $messageAcquittement;
        $echange_hprim->statut_acquittement = "erreur";
        $echange_hprim->store();
        
        return $messageAcquittement;
      }
      
      $num_dossier = new CIdSante400();
      //Paramétrage de l'id 400
      $num_dossier->object_class = "CSejour";
      $num_dossier->tag = $dest_hprim->_tag_sejour;
      $num_dossier->id400 = $data['idSourceVenue'];
      
      // Cas d'une annulation
      $cancel = false;
      /*if ($data['action'] == "suppression") {
        $cancel = true;
      }*/
      
      // idSource non connu
      if(!$num_dossier->loadMatchingObject()) {
        // idCible fourni
        if ($data['idCibleVenue']) {
          if ($newVenue->load($data['idCibleVenue'])) {
            // Mapping du séjour
            $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
        
            // Evite de passer dans le sip handler
            $newVenue->_coms_from_hprim = 1;
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
          } else {
            $_code_NumDos = "I120";
          }
        } else {
          $_code_NumDos = "I122";  
        }
        if (!$newVenue->_id) {
          // Evite de passer dans le sip handler
          $newVenue->_coms_from_hprim = 1;
          // Mapping du séjour
          $newVenue = $this->mappingVenue($data['venue'], $newVenue, $cancel);
            
          // Séjour retrouvé
          if (CAppUI::conf("hprimxml strictSejourMatch")) {
            if ($newVenue->loadMatchingSejour()) {
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
          } else {
            $collision = $newVenue->getCollisions();
            if (count($collision) == 1) {
              $newVenue = reset($collision);
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
          if (!$newVenue->_id) {
            $msgVenue = $newVenue->store();
            $commentaire = "Séjour créé : $newVenue->_id. ";
          }
        }
          
        $num_dossier->object_id = $newVenue->_id;
        $num_dossier->last_update = mbDateTime();
        $msgNumDossier = $num_dossier->store();
        
        $codes = array ($msgVenue ? ($_code_Venue ? "A103" : "A102") : ($_code_Venue ? "I102" : "I101"), 
                        $msgNumDossier ? "A105" : $_code_NumDos,
                        $cancel ? "A130" : "");
        
        if ($msgVenue || $msgNumDossier) {
          $avertissement = $msgVenue." ".$msgNumDossier;
        } else {
          $commentaire .= "Numéro dossier créé : $num_dossier->id400.";
        }
      } 
      // idSource connu
      else {
        $newVenue->load($num_dossier->object_id);
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
              $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
              $echange_hprim->acquittement = $messageAcquittement;
              $echange_hprim->statut_acquittement = "erreur";
              $echange_hprim->store();
              return $messageAcquittement;
            }
            $_code_NumDos = "I124"; 
          }
          // idCible non connu
          else {
            $_code_NumDos = "A120";
          }
        }
        // Evite de passer dans le sip handler
        $newVenue->_coms_from_hprim = 1;
        $msgVenue = $newVenue->store();
        
        $newVenue->loadLogs();
        $modified_fields = "";
        if (is_array($newVenue->_ref_last_log->_fields)) {
          foreach ($newVenue->_ref_last_log->_fields as $field) {
            $modified_fields .= "$field \n";
          }
        }
        $codes = array ($msgVenue ? "A103" : "I102", $_code_NumDos, $cancel ? "A130" : "");
        
        if ($msgVenue) {
          $avertissement = $msgVenue." ";
        } else {
          $commentaire = "Séjour modifiée : $newVenue->_id. Les champs mis à jour sont les suivants : $modified_fields. Numéro dossier associé : $num_dossier->id400.";
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