<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementspatients");

class CHPrimXMLMouvementPatient extends CHPrimXMLEvenementsPatients { 
  function __construct() {    
    $this->sous_type = "mouvementPatient";
            
    parent::__construct();
  }
  
  function generateFromOperation($mbVenue, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $mouvementPatient = $this->addElement($evenementPatient, "mouvementPatient");
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getMouvementPatientXML() {
    $xpath = new CMbXPath($this, true);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $mouvementPatient = $xpath->queryUniqueNode("hprim:mouvementPatient"  , $evenementPatient);

    $data['action']   = $this->getActionEvenement("hprim:mouvementPatient", $evenementPatient);
  
    $data['patient']  = $xpath->queryUniqueNode("hprim:patient", $mouvementPatient);
    $data['idSource'] = $this->getIdSource($data['patient']);
    $data['idCible']  = $this->getIdCible($data['patient']);
    
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
   * @param CHPrimXMLAcquittementsPatients $domAcquittement
   * @param CEchangeHprim $echange_hprim.
   * @param CPatient $newPatient
   * @param array $data
   * @return string acquittement 
   **/
  function mouvementPatient($domAcquittement, $echange_hprim, $newPatient, $data) {
  	if (($data['action'] != "création") && ($data['action'] != "modification") && ($data['action'] != "remplacement")) {
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E08");
      $doc_valid = $domAcquittement->schemaValidate();
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
      $echange_hprim->acquittement = $messageAcquittement;
      $echange_hprim->statut_acquittement = "erreur";
      $echange_hprim->store();
      
      return $messageAcquittement;
    }
    
    // Traitement de la venue
    $domVenuePatient = new CHPrimXMLVenuePatient();
    $messageAcquittement = $domVenuePatient->venuePatient($domAcquittement, $echange_hprim, $newPatient, $data);
    if ($echange_hprim->statut_acquittement != "OK") {
      return $messageAcquittement;
    }
    
    $domAcquittement = new CHPrimXMLAcquittementsPatients();
    $domAcquittement->identifiant = $data['identifiantMessage'];
    $domAcquittement->destinataire = $data['idClient'];
    $domAcquittement->destinataire_libelle = $data['libelleClient'];
    
     // Si CIP
    if (!CAppUI::conf('sip server')) {
      $mbVenue = new CSejour();
      
      // Mapping des mouvements
      $mbVenue = $this->mappingMouvements($data['mouvements'], $mbVenue);
      
      // Evite de passer dans le sip handler
      $mbVenue->_coms_from_hprim = 1;
      $msgVenue = $mbVenue->store();
      
      $mbVenue->loadLogs();
      $modified_fields = "";
      if (is_array($mbVenue->_ref_last_log->_fields)) {
        foreach ($mbVenue->_ref_last_log->_fields as $field) {
          $modified_fields .= "$field \n";
        }
      }
      $codes = array ($msgVenue ? "A103" : "I102");
      
      if ($msgVenue) {
        $avertissement = $msgVenue." ";
      } else {
        $commentaire = "Séjour modifiée : $mbVenue->_id. Les champs mis à jour sont les suivants : $modified_fields.";
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