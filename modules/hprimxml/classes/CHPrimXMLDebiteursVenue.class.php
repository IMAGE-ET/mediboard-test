<?php /* $Id: venuepatient.class.php 7500 2009-12-03 08:33:23Z lryo $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 7500 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

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
    $this->addPatient($patient, $mbVenue->_ref_patient, $referent, true);
    
    $venue = $this->addElement($debiteursVenue, "venue"); 
    // Ajout de la venue   
    $this->addVenue($venue, $mbVenue, $referent, true);

    // Ajout des débiteurs
    $debiteurs = $this->addElement($debiteursVenue, "debiteurs");
    $this->addDebiteurs($debiteurs, $mbVenue->_ref_patient, $referent);
    
    // Traitement final
    $this->purgeEmptyElements();
  }

  function getContentsXML() {
    $xpath = new CHPrimXPath($this);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $debiteursVenue = $xpath->queryUniqueNode("hprim:debiteursVenue", $evenementPatient);

    $data['action'] = $this->getActionEvenement("hprim:debiteursVenue", $evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $debiteursVenue);
    $data['venue'] = $xpath->queryUniqueNode("hprim:venue", $debiteursVenue);
    $data['employeurs'] = $xpath->queryUniqueNode("hprim:employeurs", $debiteursVenue);
    $data['debiteurs'] = $xpath->queryUniqueNode("hprim:debiteurs", $debiteursVenue);

    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['idSourceVenue'] = $this->getIdSource($data['venue']);
    $data['idCibleVenue'] = $this->getIdCible($data['venue']);
    
    return $data;
  }
  
  /**
   * Gestion des débiteurs d'une venue de patient
   * @param CHPrimXMLAcquittementsPatients $dom_acq
   * @param CEchangeHprim $echg_hprim
   * @param CPatient $newPatient
   * @param CSejour $newSejour
   * @param array $data
   * @return CHPrimXMLAcquittementsPatients $msgAcq 
   **/
  function debiteursVenue($dom_acq, $newPatient, $data, &$newVenue = null) {
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
    if (!CAppUI::conf('sip server')) { 
      $sender = $echg_hprim->_ref_sender;
      
      $avertissement = null;
      
      // Mapping des mouvements
      $newPatient = $this->mappingDebiteurs($data['debiteurs'], $newPatient);
      $newPatient->repair();
      
      // Notifier les autres destinataires
      $newPatient->_eai_initiateur_group_id = $sender->group_id;
      $msgPatient = $newPatient->store();
      
      $modified_fields = CEAIPatient::getModifiedFields($newPatient);
      
      $codes = array ($msgPatient ? "A003" : "I002");
      
      if ($msgPatient) {
        $avertissement = $msgPatient." ";
      } else {
        $commentaire = "Patient modifiée : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields.";
      }

      return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $commentaire, $newPatient);
    }
  }
}

