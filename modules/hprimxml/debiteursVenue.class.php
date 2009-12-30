<?php /* $Id: venuepatient.class.php 7500 2009-12-03 08:33:23Z lryo $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 7500 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementspatients");

class CHPrimXMLDebiteursVenue extends CHPrimXMLEvenementsPatients { 
  var $actions = array(
    'création' => "création",
    'remplacement' => "remplacement",
    'modification' => "modification",
  );
  
  function __construct() {    
    $this->sous_type = "debiteursVenue";
            
    parent::__construct();
  }
  
  function generateFromOperation($mbVenue, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $debiteursVenue = $this->addElement($evenementPatient, "debiteursVenue");
    $actionConversion = array (
      "create" => "création",
      "store"  => "modification",
      "delete" => "suppression"
    );
    $this->addAttribute($debiteursVenue, "action", $actionConversion[$mbVenue->_ref_last_log->type]);
    
    $patient = $this->addElement($debiteursVenue, "patient");
    // Ajout du patient   
    $this->addPatient($patient, $mbVenue->_ref_patient, null, $referent, true);
    
    $venue = $this->addElement($debiteursVenue, "venue"); 
    // Ajout de la venue   
    $this->addVenue($venue, $mbVenue, $referent, true);

    // Ajout des débiteurs
    $debiteurs = $this->addElement($debiteursVenue, "debiteurs");
    $this->addDebiteurs($debiteurs, $mbVenue->_ref_patient, $referent);
    
    // Traitement final
    $this->purgeEmptyElements();
  }

  function getDebiteursVenueXML() {
    $xpath = new CMbXPath($this, true);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $debiteursVenue = $xpath->queryUniqueNode("hprim:debiteursVenue", $evenementPatient);

    $data['action'] = $this->getActionEvenement("hprim:debiteursVenue", $evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $debiteursVenue);
    $data['venue'] = $xpath->queryUniqueNode("hprim:venue", $debiteursVenue);
    $data['employeurs'] = $xpath->queryUniqueNode("hprim:employeurs", $debiteursVenue);
    $data['debiteurs'] = $xpath->queryUniqueNode("hprim:debiteurs", $debiteursVenue);

    $data['idSource'] = $this->getIdSource($data['patient']);
    $data['idCible'] = $this->getIdCible($data['patient']);
    
    $data['idSourceVenue'] = $this->getIdSource($data['venue']);
    $data['idCibleVenue'] = $this->getIdCible($data['venue']);
    
    return $data;
  }
  
  /**
   * Gestion des débiteurs d'une venue de patient
   * @param CHPrimXMLAcquittementsPatients $domAcquittement
   * @param CEchangeHprim $echange_hprim
   * @param CPatient $newPatient
   * @param CSejour $newSejour
   * @param array $data
   * @return CHPrimXMLAcquittementsPatients $messageAcquittement 
   **/
  function debiteursVenue($domAcquittement, $echange_hprim, $newPatient, $data, &$newVenue = null) {
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
      $avertissement = null;
      
      // Mapping des mouvements
      $newPatient = $this->mappingDebiteurs($data['debiteurs'], $newPatient);
      $newPatient->repair();
      
      // Evite de passer dans le sip handler
      $newPatient->_coms_from_hprim = 1;
      $msgPatient = $newPatient->store();
      
      $newPatient->loadLogs();
      $modified_fields = "";
      if (is_array($newPatient->_ref_last_log->_fields)) {
        foreach ($newPatient->_ref_last_log->_fields as $field) {
          $modified_fields .= "$field \n";
        }
      }
      $codes = array ($msgPatient ? "A003" : "I002");
      
      if ($msgPatient) {
        $avertissement = $msgPatient." ";
      } else {
        $commentaire = "Patient modifiée : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields.";
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