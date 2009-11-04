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
     // Si CIP
    if (!CAppUI::conf('sip server')) {
      // Acquittement d'erreur : identifiants source et cible non fournis pour le venue / venueEliminee
      if (!$data['idSourceVenue'] && !$data['idCibleVenue'] && !$data['idSourceVenueEliminee'] && !$data['idCibleVenueEliminee']) {
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E100");
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
          
        $echange_hprim->message = $messagePatient;
        $echange_hprim->acquittement = $messageAcquittement;
        $echange_hprim->statut_acquittement = "erreur";
        $echange_hprim->store();
        
        return $messageAcquittement;
      }
      
    }
    
    $etat = CHPrimXMLEvenementsPatients::getEtatVenue($data['venueElimine']);
    // Cas de passage d'une pré-admission en admission
    if ($etat == "préadmission") {
      
    }
  }
}
?>