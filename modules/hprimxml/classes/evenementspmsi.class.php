<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementsserveuractivitepmsi");

class CHPrimXMLEvenementsPmsi extends CHPrimXMLEvenementsServeurActivitePmsi {
  function __construct() {
    $this->sous_type = "evenementPMSI";
    $this->evenement = "evt_pmsi";
		
    parent::__construct("evenementPmsi", "msgEvenementsPmsi");
  }
  
  function generateEnteteMessage() {
    parent::generateEnteteMessage("evenementsPMSI");
  }
  
  function generateFromOperation(CSejour $mbSejour) {
    $evenementsPMSI = $this->documentElement;

    $evenementPMSI = $this->addElement($evenementsPMSI, "evenementPMSI");

    // Ajout du patient
    $mbPatient =& $mbSejour->_ref_patient;
    $patient = $this->addElement($evenementPMSI, "patient");
    $this->addPatient($patient, $mbPatient, false, true);
    
    // Ajout de la venue, c'est-à-dire le séjour
    $venue = $this->addElement($evenementPMSI, "venue");
    $this->addVenue($venue, $mbSejour);
    
    if ($mbSejour->type == "ssr") {
      // Ajout du contenu rhss
      $rhss = $this->addElement($evenementPMSI, "rhss");
      $this->addSsr($rhss, $mbSejour);
    } else {
      // Ajout de la saisie délocalisée
      $saisie = $this->addElement($evenementPMSI, "saisieDelocalisee");
      $this->addSaisieDelocalisee($saisie, $mbSejour);
    }

    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getContentsXML() {
    $data = array();
    $xpath = new CMbXPath($this, true);   
    
    $evenementPMSI = $xpath->queryUniqueNode("/hprim:evenementsPMSI/hprim:evenementPMSI");
    
    $data['patient']         = $xpath->queryUniqueNode("hprim:patient", $evenementPMSI);
    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['venue']           = $xpath->queryUniqueNode("hprim:venue", $evenementPMSI);
    $data['idSourceVenue']   = $this->getIdSource($data['venue']);
    $data['idCibleVenue']    = $this->getIdCible($data['venue']);
    
    return $data; 
  }
}
?>