<?php

/**
 * Mouvement patient
 *
 * @category Hprimxml
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimXMLMouvementPatient
 * Mouvement patient
 */

class CHPrimXMLMouvementPatient extends CHPrimXMLEvenementsPatients { 
  var $actions = array(
    'création' => "création",
    'remplacement' => "remplacement",
    'modification' => "modification",
  );

  /**
   * Construct
   *
   * @return CHPrimXMLMouvementPatient
   */
  function __construct() {    
    $this->sous_type = "mouvementPatient";
            
    parent::__construct();
  }

  /**
   * Generate content message
   *
   * @param CAffectation $mouvement Movement
   * @param bool         $referent  Is referring ?
   *
   * @return void
   */
  function generateFromOperation(CAffectation $mouvement, $referent) {
    $evenementsPatients = $this->documentElement;
    $evenementPatient   = $this->addElement($evenementsPatients, "evenementPatient");
    
    $mouvementPatient = $this->addElement($evenementPatient, "mouvementPatient");
    $actionConversion = array (
      "create" => "création",
      "store"  => "modification",
      "delete" => "suppression"
    );
    $action = $mouvement->_ref_last_log->type ? $mouvement->_ref_last_log->type : "create";
    $this->addAttribute($mouvementPatient, "action", $actionConversion[$action]);
    
    $patient = $this->addElement($mouvementPatient, "patient");
    // Ajout du patient   
    $this->addPatient($patient, $mouvement->_ref_sejour->_ref_patient, $referent);
    
    $venue = $this->addElement($mouvementPatient, "venue"); 
    // Ajout de la venue   
    $this->addVenue($venue, $mouvement->_ref_sejour, $referent);
    
    // Ajout du mouvement (1 seul dans notre cas pas l'historique)
    $mouvements = $this->addElement($mouvementPatient, "mouvements"); 
    $this->addMouvement($mouvements, $mouvement, $referent);

    // Traitement final
    $this->purgeEmptyElements();
  }

  /**
   * Get content XML
   *
   * @return array
   */
  function getContentsXML() {
    $xpath = new CHPrimXPath($this);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $mouvementPatient = $xpath->queryUniqueNode("hprim:mouvementPatient"  , $evenementPatient);

    $data['action']   = $this->getActionEvenement("hprim:mouvementPatient", $evenementPatient);
  
    $data['patient']  = $xpath->queryUniqueNode("hprim:patient", $mouvementPatient);
    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['venue']         = $xpath->queryUniqueNode("hprim:venue", $mouvementPatient);
    $data['idSourceVenue'] = $this->getIdSource($data['venue']);
    $data['idCibleVenue']  = $this->getIdCible($data['venue']);
    
    $data['priseEnCharge'] = $xpath->queryUniqueNode("hprim:priseEnCharge", $mouvementPatient);
    $data['mouvements']    = $xpath->queryUniqueNode("hprim:mouvements"   , $mouvementPatient);
    $data['voletMedical']  = $xpath->queryUniqueNode("hprim:voletMedical" , $mouvementPatient);
    $data['dossierResume'] = $xpath->queryUniqueNode("hprim:dossierResume", $mouvementPatient);
        
    return $data;
  }
  
  /**
   * Fusion and recording a stay with an num_dos in the system
   *
   * @param CHPrimXMLAcquittementsPatients $dom_acq    Acquittement
   * @param CPatient                       $newPatient Patient
   * @param array                          $data       Data
   *
   * @return string acquittement 
   **/
  function mouvementPatient(CHPrimXMLAcquittementsPatients $dom_acq, CPatient $newPatient, $data) {
    $echg_hprim = $this->_ref_echange_hprim;
    
    // Traitement de la venue
    $newVenue        = new CSejour();
    $domVenuePatient = new CHPrimXMLVenuePatient();
    $domVenuePatient->_ref_echange_hprim = $echg_hprim;
    $msgAcq = $domVenuePatient->venuePatient($dom_acq, $newPatient, $data, $newVenue);
    if ($echg_hprim->statut_acquittement != "OK") {
      return $msgAcq;
    }
    
    $dom_acq = new CHPrimXMLAcquittementsPatients();
    $dom_acq->_identifiant_acquitte = $data['identifiantMessage'];
    $dom_acq->_sous_type_evt        = $this->sous_type;
    $dom_acq->_ref_echange_hprim    = $echg_hprim;
    
    // Si CIP
    if (!CAppUI::conf('smp server')) { 
      $sender = $echg_hprim->_ref_sender;
      
      $avertissement = null;
      
      // Mapping des mouvements
      $newVenue = $this->mappingMouvements($data['mouvements'], $newVenue);

      // Notifier les autres destinataires
      $newVenue->_eai_initiateur_group_id = $sender->group_id;
      $msgVenue = $newVenue->store();
      
      $codes = array ($msgVenue ? "A103" : "I102");
      
      if ($msgVenue) {
        $avertissement = $msgVenue." ";
      }
      else {
        $newVenue->loadLogs();
        $modified_fields = "";
        if (is_array($newVenue->_ref_last_log->_fields)) {
          foreach ($newVenue->_ref_last_log->_fields as $field) {
            $modified_fields .= "$field \n";
          }
        }
      
        $commentaire = "Séjour modifiée : $newVenue->_id. Les champs mis à jour sont les suivants : $modified_fields.";
      }
    }

    return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $commentaire, $newVenue);
  } 
}