<?php /* $Id: evenementspmsi.class.php 9209 2010-06-15 13:10:19Z lryo $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 9209 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementsserveuractivitepmsi");

class CHPrimXMLEvenementsServeurEtatsPatient extends CHPrimXMLEvenementsServeurActivitePmsi {
  function __construct() {
    $this->sous_type = "evenementServeurEtatsPatient";
    $this->evenement = "evt_serveuretatspatient";
    
    parent::__construct(null, "msgEvenementsServeurEtatsPatient");
  }
  
  function generateEnteteMessage() {
    parent::generateEnteteMessage("evenementsServeurEtatsPatient");
  }
  
  function generateFromOperation(CSejour $mbSejour) {
    $evenementsServeurEtatsPatient = $this->documentElement;

    // Ajout du patient
    $mbPatient =& $mbSejour->_ref_patient;
    $patient = $this->addElement($evenementsServeurEtatsPatient, "patient");
    $this->addPatient($patient, $mbPatient, false, true);
    
    // Ajout de la venue, c'est-à-dire le séjour
    $venue = $this->addElement($evenementsServeurEtatsPatient, "venue");
    $this->addVenue($venue, $mbSejour);
    
    $dateObservation = $this->addElement($evenementsServeurEtatsPatient, "dateObservation");
    $this->addDateHeure($dateObservation, mbDateTime());
    
    // Ajout des diagnostics
    $Diagnostics = $this->addElement($evenementsServeurEtatsPatient, "Diagnostics");
    $this->addDiagnosticsEtat($Diagnostics, $mbSejour);
    
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getContentsXML() {
    $data = array();
    $xpath = new CHPrimXPath($this);   
    
    $evenementsServeurEtatsPatient = $xpath->queryUniqueNode("/hprim:evenementsServeurEtatsPatient");
    
    $data['patient']         = $xpath->queryUniqueNode("hprim:patient", $evenementsServeurEtatsPatient);
    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['venue']           = $xpath->queryUniqueNode("hprim:venue", $evenementsServeurEtatsPatient);
    $data['idSourceVenue']   = $this->getIdSource($data['venue']);
    $data['idCibleVenue']    = $this->getIdCible($data['venue']);
    
    return $data; 
  }
}
?>