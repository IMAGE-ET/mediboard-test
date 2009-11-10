<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementspatients");

class CHPrimXMLFusionVenue extends CHPrimXMLEvenementsPatients { 
  function __construct() {    
  	$this->sous_type = "fusionVenue";
  	        
    parent::__construct();
  }
  
  function generateFromOperation($mbVenue, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $fusionVenue = $this->addElement($evenementPatient, "fusionVenue");
    $this->addAttribute($fusionVenue, "action", "fusion");
      
    $venue = $this->addElement($fusionVenue, "venue");

    // Ajout de la venue   
    $this->addVenue($venue, $mbVenue, $referent);
      
    $venueElimine = $this->addElement($fusionVenue, "venueElimine");
    $mbVenueElimine = new CVenue();
    $mbVenueElimine->load($mbVenue->_merging);

    // Ajout du patient a eliminer
    $this->addPatient($venueElimine, $mbVenueElimine, null, $referent);
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getFusionVenueXML() {
    $xpath = new CMbXPath($this, true);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $fusionVenue = $xpath->queryUniqueNode("hprim:fusionVenue", $evenementPatient);

    $data['action']  = $this->getActionEvenement("hprim:fusionVenue", $evenementPatient);
	
    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $fusionVenue);
    $data['idSource'] = $this->getIdSource($data['patient']);
    $data['idCible']  = $this->getIdCible($data['patient']);
    
    $data['venue']        = $xpath->queryUniqueNode("hprim:venue", $fusionVenue);
    $data['idSourceVenue'] = $this->getIdSource($data['venue']);
    $data['idCibleVenue']  = $this->getIdCible($data['venue']);
    
    $data['venueElimine'] = $xpath->queryUniqueNode("hprim:venueElimine", $fusionVenue);
    $data['idSourceVenueEliminee'] = $this->getIdSource($data['venueElimine']);
    $data['idCibleVenueEliminee']  = $this->getIdCible($data['venueElimine']);
		    
    return $data;
  }
  
  /**
   * Fusion and recording a stay with an num_dos in the system
   * @param CHPrimXMLAcquittementsPatients $domAcquittement
   * @param CEchangeHprim $echange_hprim
   * @param array $data
   * @return string acquittement 
   **/
  function fusionVenue($domAcquittement, $echange_hprim, $data) {
    // Seulement le cas d'une fusion
    if ($data['action'] != "fusion") {
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E08");
      $doc_valid = $domAcquittement->schemaValidate();
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
      $echange_hprim->acquittement = $messageAcquittement;
      $echange_hprim->statut_acquittement = "erreur";
      $echange_hprim->store();
      
      return $messageAcquittement;
    }
    
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
    
     // Si CIP
    if (!CAppUI::conf('sip server')) {
      $mbVenueEliminee = new CSejour();
      
      $mbVenue = new CSejour();
     
      // Acquittement d'erreur : identifiants source et cible non fournis pour le venue / venueEliminee
      if (!$data['idSourceVenue'] && !$data['idCibleVenue'] && !$data['idSourceVenueEliminee'] && !$data['idCibleVenueEliminee']) {
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E100");
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
          
        $echange_hprim->acquittement = $messageAcquittement;
        $echange_hprim->statut_acquittement = "erreur";
        $echange_hprim->store();
        
        return $messageAcquittement;
      }
      
      $etatVenue         = CHPrimXMLEvenementsPatients::getEtatVenue($data['venue']);
      $etatVenueEliminee = CHPrimXMLEvenementsPatients::getEtatVenue($data['venueElimine']);
      
      $id400Venue = new CIdSante400();
      //Paramétrage de l'id 400
      $id400Venue->object_class = "CSejour";
      $id400Venue->tag = ($etatVenue == "préadmission") ? CAppUI::conf('dPplanningOp CSejour tag_dossier_pa').$data['idClient'] : $data['idClient'];
      $id400Venue->id400 = $data['idSourceVenue'];
      $id400Venue->loadMatchingObject();
      if ($mbVenue->load($data['idCibleVenue'])) {
        if ($mbVenue->_id != $id400Venue->object_id) {
          $commentaire = "L'identifiant source fait référence au séjour : $id400Venue->object_id et l'identifiant cible au séjour : $mbVenue->_id.";
          $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E104", $commentaire);
          $doc_valid = $domAcquittement->schemaValidate();
          $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
    
          $echange_hprim->acquittement = $messageAcquittement;
          $echange_hprim->statut_acquittement = "erreur";
          $echange_hprim->store();
          return $messageAcquittement;
        }
      }
      
      $id400VenueEliminee = new CIdSante400();
      //Paramétrage de l'id 400
      $id400VenueEliminee->object_class = "CSejour";
      $id400VenueEliminee->tag = ($etatVenue == "préadmission") ? CAppUI::conf('dPplanningOp CSejour tag_dossier_pa').$data['idClient'] : $data['idClient'];
      $id400VenueEliminee->id400 = $data['idSourceVenue'];
      $id400VenueEliminee->loadMatchingObject();
      if ($mbVenueEliminee->load($data['idCibleVenue'])) {
        if ($mbVenueEliminee->_id != $id400VenueEliminee->object_id) {
          $commentaire = "L'identifiant source fait référence au séjour : $id400VenueEliminee->object_id et l'identifiant cible au séjour : $mbVenueEliminee->_id.";
          $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E141", $commentaire);
          $doc_valid = $domAcquittement->schemaValidate();
          $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
    
          $echange_hprim->acquittement = $messageAcquittement;
          $echange_hprim->statut_acquittement = "erreur";
          $echange_hprim->store();
          return $messageAcquittement;
        }
      }
      
      $messages = array();
      
      $newVenue = CSejour();
      // Cas 0 : Aucun séjour
      if (!$mbVenue->_id && !$mbVenueEliminee->_id) {
        $newVenue->patient_id = $newPatient->_id; 
        $newVenue->group_id   = CGroups::loadCurrent()->_id;
        $messages = $this->mapAndStoreVenue($newVenue, $data, $etatVenueEliminee, $id400Venue, $id400VenueEliminee);
      }
      // Cas 1 : 1 séjour
      else if ($mbVenue->_id || $mbVenueEliminee->_id) {
        // Suppression de l'identifiant du séjour trouvé
        if ($mbVenue->_id) {
          $messages['msgNumDosVenue'] = $id400Venue->delete();
          
        } else if ($mbVenueEliminee->_id) {
          $messages['msgNumDosVenueEliminee'] = $id400VenueEliminee->delete();
        }
        
        // Cas 0
        $messages = $this->mapAndStoreVenue($newVenue, $data, $etatVenueEliminee, $id400Venue, $id400VenueEliminee);
      }
      // Cas 2 : 2 Séjour
      else if ($mbVenue->_id && $mbVenueEliminee->_id) {
        // Suppression des identifiants des séjours trouvés
        $messages['msgNumDosVenue'] = $id400Venue->delete();
        $messages['msgNumDosVenueEliminee'] = $id400VenueEliminee->delete();
        
        // Transfert des backsref
         $mbVenueEliminee->transferBackRefsFrom($mbVenue);
         
        // Suppression de la venue a éliminer
        $msgDelete = $mbVenueEliminee->delete();
        
        // Cas 0
        $messages = $this->mapAndStoreVenue($newVenue, $data, $etatVenueEliminee, $id400Venue, $id400VenueEliminee);
      }
      
      $codes = array  ($messages['msgVenue'] ? "I101" : "A102");
        
      if ($messages['msgVenue']) {
        $avertissement = $messages['msgVenue'];
      } else {
        $commentaire = "Séjour enregistré : $mbVenue->_id. Numéro dossier associé : $id400Venue->id400. Le séjour $id400VenueEliminee->id400 a été éliminé.";
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
  
  private function mapAndStoreVenue(&$newVenue, $data, $etatVenueEliminee, &$id400Venue, &$id400VenueEliminee) {
    $messages = array();
    // Mapping de la venue a éliminer
    $newVenue = $this->mappingVenue($data['venueEliminee'], $newVenue);
    // Mapping de la venue a garder
    $newVenue = $this->mappingVenue($data['venue'], $newVenue);
    
     // Evite de passer dans le sip handler
    $newVenue->_coms_from_hprim = 1;
    $messages['msgVenue'] = $newVenue->store();
    
    $id400Venue->object_id = $newVenue->_id;
    $id400Venue->last_update = mbDateTime();
    $messages['msgNumDosVenue'] = $id400Venue->store();
    
    if ($etatVenueEliminee != "préadmission") {
      $id400VenueEliminee->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_cancel').$data['idClient'];
      $id400VenueEliminee->object_id = $newVenue->_id;
      $id400VenueEliminee->last_update = mbDateTime();
      $messages['msgNumDosVenueEliminee'] = $id400VenueEliminee->store();
    }
    
    return $messages;
  }
}
?>