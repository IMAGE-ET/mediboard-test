<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementsserveuractivitepmsi");

class CHPrimXMLEvenementsServeurActes extends CHPrimXMLEvenementsServeurActivitePmsi {
  function __construct() {
		$this->sous_type = "evenementServeurActe";
    $this->evenement = "evt_serveuractes";
		
		parent::__construct("serveurActes", "msgEvenementsServeurActes");
  }

	function generateEnteteMessage() {
    $evenementsServeurActes = $this->addElement($this, "evenementsServeurActes", null, "http://www.hprim.org/hprimXML");
    $this->addAttribute($evenementsServeurActes, "version", CAppUI::conf('hprimxml evt_serveuractes version'));
		
    $this->addEnteteMessage($evenementsServeurActes);
  }
  
  function generateFromOperation($mbOp) {
    $evenementsServeurActes = $this->documentElement;

    $evenementServeurActe = $this->addElement($evenementsServeurActes, "evenementServeurActe");
    $this->addDateTimeElement($evenementServeurActe, "dateAction");

    // Ajout du patient
    $mbPatient =& $mbOp->_ref_sejour->_ref_patient;
    $patient = $this->addElement($evenementServeurActe, "patient");
    $this->addPatient($patient, $mbPatient, false, true);
    
    // Ajout de la venue, c'est-à-dire le séjour
    $mbSejour =& $mbOp->_ref_sejour;
    $venue = $this->addElement($evenementServeurActe, "venue");
		$this->addVenue($venue, $mbSejour, null, true);
    
    // Ajout de l'intervention
    $intervention = $this->addElement($evenementServeurActe, "intervention");
    $this->addIntervention($intervention, $mbOp);
    
    // Ajout des actes CCAM
    $actesCCAM = $this->addElement($evenementServeurActe, "actesCCAM");
    foreach ($mbOp->_ref_actes_ccam as $mbActe) {
      $this->addActeCCAM($actesCCAM, $mbActe, $mbOp);
    }

    // Traitement final
    $this->purgeEmptyElements();
  }
	
	function getServeurActesXML() {
		$data = array();    
    $xpath = new CMbXPath($this, true);		

    $entete = $xpath->queryUniqueNode("/hprim:evenementsServeurActes/hprim:enteteMessage");
		
    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $entete);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $entete);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='système']", $agents, false);
    $this->destinataire = $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);
		
    $evenementServeurActe = $xpath->queryUniqueNode("/hprim:evenementsServeurActes/hprim:evenementServeurActe");
		
    $data['patient']         = $xpath->queryUniqueNode("hprim:patient", $evenementServeurActe);
		$data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
		
		$data['venue']         = $xpath->queryUniqueNode("hprim:patient", $evenementServeurActe);
		$data['idSourceVenue'] = $this->getIdSource($data['venue']);
    $data['idCibleVenue']  = $this->getIdCible($data['venue']);
		
		$data['intervention']  = $xpath->queryUniqueNode("hprim:intervention", $evenementServeurActe);
		
    $data['actesCCAM']     = $xpath->queryUniqueNode("hprim:actesCCAM", $evenementServeurActe);    
    
    return $data;
  }
	
	/**
   * Enregistrement des actes CCAM
   * @param CHPrimXMLAcquittementsServeurActes $domAcquittement
   * @param CEchangeHprim $echange_hprim
   * @param CPatient $newPatient
   * @param array $data
   * @return CHPrimXMLAcquittementsServeurActes $messageAcquittement 
   **/
  function serveurActes($domAcquittement, &$echange_hprim, &$newPatient, $data) {  
	  mbTrace($domAcquittement, "acquittement", true);
	  mbTrace($data, "data", true);
	}
}
?>