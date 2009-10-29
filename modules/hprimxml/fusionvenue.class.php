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
    $this->addVenue($venue, $mbVenue, null, $referent);
      
    $venueElimine = $this->addElement($fusionVenue, "venueElimine");
    $mbVenueElimine = new CVenue();
    $mbVenueElimine->load($mbVenue->_merging);

    // Ajout du patient a eliminer
    $this->addPatient($venueElimine, $mbVenueElimine, null, $referent);
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getFusionVenueXML() {
    global $m;

    $xpath = new CMbXPath($this, true);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $fusionVenue = $xpath->queryUniqueNode("hprim:fusionVenue", $evenementPatient);

    $data['action']  = $this->getActionEvenement("hprim:fusionPatient", $evenementPatient);
	
    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $venuePatient);
    $data['idSource'] = $this->getIdSource($data['patient']);
    $data['idCible']  = $this->getIdCible($data['patient']);
    
    $data['venue']        = $xpath->queryUniqueNode("hprim:venue", $fusionVenue);
    $data['idSourceVenue'] = $this->getIdSource($data['venue']);
    $data['idCibleVenue']  = $this->getIdCible($data['venue']);
    
    $data['venueElimine'] = $xpath->queryUniqueNode("hprim:venueElimine", $fusionVenue);
    $data['idSourceVenueEliminee'] = $this->getIdSource($data['venue']);
    $data['idCibleVenueEliminee']  = $this->getIdCible($data['venue']);
		    
    return $data;
  }
  
  /**
   * Fusion and recording a stay with an num_dos in the system
   * @param CHPrimXMLAcquittementsPatients $domAcquittement
   * @param CEchangeHprim $echange_hprim
   * @param CPatient $newPatient
   * @param CSejour $newVenue
   * @param array $data
   * @return string acquittement 
   **/
  function fusionVenue($domAcquittement, $echange_hprim, $newPatient, $newVenue, $data) {
    
  }
}
?>