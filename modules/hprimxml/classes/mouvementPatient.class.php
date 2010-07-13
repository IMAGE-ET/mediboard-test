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
  var $actions = array(
    'création' => "création",
    'remplacement' => "remplacement",
    'modification' => "modification",
  );
  
  function __construct() {    
    $this->sous_type = "mouvementPatient";
            
    parent::__construct();
  }
  
  function generateFromOperation($newVenue, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $mouvementPatient = $this->addElement($evenementPatient, "mouvementPatient");
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
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
   * @param CHPrimXMLAcquittementsPatients $domAcquittement
   * @param CEchangeHprim $echange_hprim.
   * @param CPatient $newPatient
   * @param array $data
   * @return string acquittement 
   **/
  function mouvementPatient($domAcquittement, $echange_hprim, $newPatient, $data) {
    // Traitement de la venue
    $domVenuePatient = new CHPrimXMLVenuePatient();
    $newVenue = new CSejour();
    
    $messageAcquittement = $domVenuePatient->venuePatient($domAcquittement, $echange_hprim, $newPatient, $data, $newVenue);
    if ($echange_hprim->statut_acquittement != "OK") {
      return $messageAcquittement;
    }

    $domAcquittement = new CHPrimXMLAcquittementsPatients();
    $domAcquittement->identifiant = $data['identifiantMessage'];
    $domAcquittement->destinataire = $data['idClient'];
    $domAcquittement->destinataire_libelle = $data['libelleClient'];
    $domAcquittement->_sous_type_evt = $this->sous_type;
    
    // Si CIP
    if (!CAppUI::conf('sip server')) { 
      $dest_hprim = new CDestinataireHprim();
      $dest_hprim->nom = $data['idClient'];
      $dest_hprim->loadMatchingObject();
      
      $avertissement = null;
      
      // Mapping des mouvements
      $newVenue = $this->mappingMouvements($data['mouvements'], $newVenue);

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
      $codes = array ($msgVenue ? "A103" : "I102");
      
      if ($msgVenue) {
        $avertissement = $msgVenue." ";
      } else {
        $commentaire = "Séjour modifiée : $newVenue->_id. Les champs mis à jour sont les suivants : $modified_fields.";
      }
      
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : substr($commentaire, 0, 4000)); 
      $doc_valid = $domAcquittement->schemaValidate();
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
      $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
    }
    
    $echange_hprim->_acquittement = $messageAcquittement;
    $echange_hprim->date_echange = mbDateTime();
    $echange_hprim->setObjectIdClass("CSejour", $data['idCibleVenue']);
    $echange_hprim->store();

    return $messageAcquittement;
  } 
}
?>