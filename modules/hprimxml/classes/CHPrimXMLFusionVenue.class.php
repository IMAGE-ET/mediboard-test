<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLFusionVenue extends CHPrimXMLEvenementsPatients { 
  var $actions = array(
    'fusion' => "fusion"
  );
  
  function __construct() {    
    $this->sous_type = "fusionVenue";
            
    parent::__construct();
  }
  
  function generateFromOperation(CSejour $mbVenue, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $fusionVenue = $this->addElement($evenementPatient, "fusionVenue");
    $this->addAttribute($fusionVenue, "action", "fusion");
          
    // Ajout du patient
    $patient = $this->addElement($fusionVenue, "patient");
    $this->addPatient($patient, $mbVenue->_ref_patient, $referent);
    
    // Ajout de la venue   
    $venue = $this->addElement($fusionVenue, "venue");
    $this->addVenue($venue, $mbVenue, $referent);

    $venueEliminee = $this->addElement($fusionVenue, "venueEliminee");
    // Ajout de la venue a eliminer
    $this->addVenue($venueEliminee, $mbVenue->_sejour_eliminee, $referent);
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getContentsXML() {
    $xpath = new CHPrimXPath($this);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $fusionVenue = $xpath->queryUniqueNode("hprim:fusionVenue", $evenementPatient);

    $data['action']  = $this->getActionEvenement("hprim:fusionVenue", $evenementPatient);
  
    $data['patient']  = $xpath->queryUniqueNode("hprim:patient", $fusionVenue);
    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['venue']         = $xpath->queryUniqueNode("hprim:venue", $fusionVenue);
    $data['idSourceVenue'] = $this->getIdSource($data['venue']);
    $data['idCibleVenue']  = $this->getIdCible($data['venue']);
    
    $data['venueEliminee']         = $xpath->queryUniqueNode("hprim:venueEliminee", $fusionVenue);
    $data['idSourceVenueEliminee'] = $this->getIdSource($data['venueEliminee']);
    $data['idCibleVenueEliminee']  = $this->getIdCible($data['venueEliminee']);
        
    return $data;
  }
  
  /**
   * Fusion and recording a stay with an num_dos in the system
   * 
   * @param CHPrimXMLAcquittementsPatients $dom_acq
   * @param CEchangeHprim $echg_hprim.
   * @param CPatient $newPatient
   * @param array $data
   *
   * @return string acquittement 
   **/
  function fusionVenue($dom_acq, $newPatient, $data) {
    $echg_hprim = $this->_ref_echange_hprim;
    
    // Traitement du patient
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
    
     // Si CIP
    if (!CAppUI::conf('smp server')) {
      $mbVenueEliminee = new CSejour();
      $mbVenue = new CSejour();
     
      $sender = $echg_hprim->_ref_sender;
      
      // Acquittement d'erreur : identifiants source et cible non fournis pour le venue / venueEliminee
      if (!$data['idSourceVenue'] && !$data['idCibleVenue'] && !$data['idSourceVenueEliminee'] && !$data['idCibleVenueEliminee']) {
        return $dom_acq->generateAcquittementsError("E100", $commentaire, $newVenue);
      }
      
      $etatVenue         = CHPrimXMLEvenementsPatients::getEtatVenue($data['venue']);
      $etatVenueEliminee = CHPrimXMLEvenementsPatients::getEtatVenue($data['venueEliminee']);
     
      $idexVenue = CIdSante400::getMatch("CSejour", 
        ($etatVenue == "préadmission") ? CAppUI::conf('dPplanningOp CSejour tag_dossier_pa').$sender->_tag_sejour : 
                                         $sender->_tag_sejour, $data['idSourceVenue']);
      if ($mbVenue->load($data['idCibleVenue'])) {
        // Pas de test dans le cas ou la fusion correspond à un changement de numéro de dossier
        if (($etatVenue == "préadmission") || ($etatVenueEliminee != "préadmission")) {
          if ($idexVenue->object_id && ($mbVenue->_id != $idexVenue->object_id)) {
            $commentaire = "L'identifiant source fait référence au séjour : $idexVenue->object_id et l'identifiant cible au séjour : $mbVenue->_id.";
            return $dom_acq->generateAcquittementsError("E104", $commentaire, $newVenue);
          }
        }
      } 
      if (!$mbVenue->_id) {
        $mbVenue->_id = $idexVenue->object_id;
      }
      
      $idexVenueEliminee = CIdSante400::getMatch("CSejour", 
        ($etatVenue == "préadmission") ? CAppUI::conf('dPplanningOp CSejour tag_dossier_pa').$sender->_tag_sejour : 
                                         $sender->_tag_sejour, $data['idSourceVenueEliminee']);
      if ($mbVenueEliminee->load($data['idCibleVenueEliminee'])) {
        if ($idexVenueEliminee->object_id && ($mbVenueEliminee->_id != $idexVenueEliminee->object_id)) {
          $commentaire = "L'identifiant source fait référence au séjour : $idexVenueEliminee->object_id et l'identifiant cible au séjour : $mbVenueEliminee->_id.";
          return $dom_acq->generateAcquittementsError("E141", $commentaire, $mbVenueEliminee);
        }
      }
      if (!$mbVenueEliminee->_id) {
        $mbVenueEliminee->_id = $idexVenueEliminee->object_id;
      }
      
      $messages = array();
      $avertissement = null;
      
      $newVenue = new CSejour();
      // Cas 0 : Aucun séjour
      if (!$mbVenue->_id && !$mbVenueEliminee->_id) {
        $newVenue->patient_id = $newPatient->_id; 
        $newVenue->group_id   = CGroups::loadCurrent()->_id;
        $messages = $this->mapAndStoreVenue($newVenue, $data, $etatVenueEliminee, $idexVenue, $idexVenueEliminee);
      }
      // Cas 1 : 1 séjour
      else if ($mbVenue->_id || $mbVenueEliminee->_id) {
        // Suppression de l'identifiant du séjour trouvé
        if ($mbVenue->_id) {
          $newVenue->load($mbVenue->_id);
          $messages['msgNumDosVenue'] = $idexVenue->delete();
        }
        else if ($mbVenueEliminee->_id) {
          $newVenue->load($mbVenueEliminee->_id);
          $messages['msgNumDosVenueEliminee'] = $idexVenueEliminee->delete();
        }
        // Cas 0
        $messages = $this->mapAndStoreVenue($newVenue, $data, $etatVenueEliminee, $idexVenue, $idexVenueEliminee);
        
        $commentaire = "Séjour modifié : $newVenue->_id.";
      }
      // Cas 2 : 2 Séjour
      else if ($mbVenue->_id && $mbVenueEliminee->_id) {
        // Suppression des identifiants des séjours trouvés
        $messages['msgNumDosVenue'] = $idexVenue->delete();
        $messages['msgNumDosVenueEliminee'] = $idexVenueEliminee->delete();
        
        // Transfert des backsref
        $mbVenueEliminee->transferBackRefsFrom($mbVenue);
         
        // Suppression de la venue a éliminer
        $msgDelete = $mbVenueEliminee->delete();
        
        // Cas 0
        $newVenue->load($mbVenue->_id);
        $messages = $this->mapAndStoreVenue($newVenue, $data, $etatVenueEliminee, $idexVenue, $idexVenueEliminee);
      }
      
      $codes = array ($messages['msgVenue'] ? (($messages['_code_Venue'] == "store") ? "A103" : "A102") : 
                                              (($messages['_code_Venue'] == "store") ? "I102" : "I101"), 
                      $messages['msgNumDosVenue'] ? "A105" : $messages['_code_NumDos']);

      if ($messages['msgVenue']) {
        $avertissement = $messages['msgVenue'];
      }
      
      $commentaire = "Séjour $newVenue->_id fusionné";
    }
    
    return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $commentaire, $newVenue);
  }
  
  private function mapAndStoreVenue(&$newVenue, $data, $etatVenueEliminee, &$idexVenue, &$idexVenueEliminee) {
    $sender = new CDestinataireHprim();
    $sender->nom = $data['idClient'];
    $sender->loadMatchingObject();
    
    $messages = array();
    // Mapping de la venue a éliminer
    $newVenue = $this->mappingVenue($data['venueEliminee'], $newVenue);
    // Mapping de la venue a garder
    $newVenue = $this->mappingVenue($data['venue'], $newVenue);

    // Notifier les autres destinataires
    $newVenue->_eai_initiateur_group_id = $sender->group_id;

    // Séjour retrouvé
    if ($newVenue->loadMatchingSejour() || $newVenue->_id) {
      $messages['msgVenue'] = $newVenue->store();

      $newVenue->loadLogs();
      $modified_fields = "";
      if (is_array($newVenue->_ref_last_log->_fields)) {
        foreach ($newVenue->_ref_last_log->_fields as $field) {
          $modified_fields .= "$field \n";
        }
      }
      $messages['_code_NumDos'] = "A121";
      $messages['_code_Venue'] = "store";
      $messages['commentaire'] = "Séjour modifiée : $newVenue->_id.  Les champs mis à jour sont les suivants : $modified_fields.";           
    }
    else {
      $messages['_code_NumDos'] = "I122";
      $messages['_code_Venue']  = "create";
      $messages['msgVenue'] = $newVenue->store();
      $messages['commentaire'] = "Séjour créé : $newVenue->_id. ";
    }

    $idexVenue->object_id = $newVenue->_id;
    $idexVenue->last_update = CMbDT::dateTime();
    $messages['msgNumDosVenue'] = $idexVenue->store();
    
    $idexVenueEliminee->tag = ($etatVenueEliminee != "préadmission") ? 
      CAppUI::conf('dPplanningOp CSejour tag_dossier_cancel').$sender->_tag_sejour :
      CAppUI::conf('dPplanningOp CSejour tag_dossier_pa').$sender->_tag_sejour;
        
    $idexVenueEliminee->object_id = $newVenue->_id;
    $idexVenueEliminee->last_update = CMbDT::dateTime();
    $messages['msgNumDosVenueEliminee'] = $idexVenueEliminee->store();
    
    return $messages;
  }
}