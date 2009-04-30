<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("sip", "hprimxmlevenementspatients");

class CHPrimXMLVenuePatient extends CHPrimXMLEvenementsPatients { 
  function __construct() {            
    parent::__construct();
  }
  
  function generateFromOperation($mbPatient, $referent, $mbVenue) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $venuePatient = $this->addElement($evenementPatient, "venuePatient");
    $actionConversion = array (
      "create" => "création",
      "store"  => "modification",
      "delete" => "suppression"
    );
    $this->addAttribute($venuePatient, "action", $actionConversion[$mbPatient->_ref_last_log->type]);
    
    $patient = $this->addElement($venuePatient, "patient");
    // Ajout du patient   
    $this->addPatient($patient, $mbPatient, null, $referent);
    
    $venue = $this->addElement($venuePatient, "venue"); 
    // Ajout de la venue   
    $this->addVenue($venue, $mbVenue, null, $referent);
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function generateTypeEvenement($mbObject, $mbVenue, $referent = null, $initiateur = null) {
    $echg_hprim = new CEchangeHprim();
    $this->_date_production = $echg_hprim->date_production = mbDateTime();
    $echg_hprim->emetteur = $this->_emetteur;
    $echg_hprim->destinataire = $this->_destinataire;
    $echg_hprim->type = "evenementsPatients";
    $echg_hprim->sous_type = "venuePatient";
    $echg_hprim->message = utf8_encode($this->saveXML());
    if ($initiateur) {
      $echg_hprim->initiateur_id = $initiateur;
    }
    
    $echg_hprim->store();
    
    $this->_identifiant = str_pad($echg_hprim->_id, 6, '0', STR_PAD_LEFT);
            
    $this->generateEnteteMessageEvenementsPatients();
    $this->generateFromOperation($mbObject, $mbVenue, $referent);
    
    $doc_valid = $this->schemaValidate();
    $echg_hprim->message_valide = $doc_valid ? 1 : 0;

    $this->saveTempFile();
    $msgVenuePatient = utf8_encode($this->saveXML()); 
    
    $echg_hprim->message = $msgVenuePatient;
    
    $echg_hprim->store();
    
    return $msgVenuePatient;
  }
  
  function getVenuePatientXML() {
    $xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $venuePatient= $xpath->queryUniqueNode("hprim:venuePatient", $evenementPatient);

    $data['action'] = $this->getActionEvenement("hprim:venuePatient", $evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $venuePatient);
    $data['venue'] = $xpath->queryUniqueNode("hprim:voletMedical", $venuePatient);

    $data['idSource'] = $this->getIdSource($data['patient']);
    $data['idCible'] = $this->getIdCible($data['patient']);
    
    return $data;
  }
}

?>