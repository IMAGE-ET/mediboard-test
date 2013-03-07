<?php

/**
 * Serveur états patient
 *
 * @category Hprimxml
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimXMLEvenementsServeurEtatsPatient
 * Serveur états patient
 */

class CHPrimXMLEvenementsServeurEtatsPatient extends CHPrimXMLEvenementsServeurActivitePmsi {
  var $actions = array(
    'création'     => "création",
    'remplacement' => "remplacement",
    'modification' => "modification",
    'suppression'  => "suppression",
    'information'  => "information",
  );

  /**
   * Construct
   *
   * @return CHPrimXMLEvenementsServeurEtatsPatient
   */
  function __construct() {
    $this->sous_type = "evenementServeurEtatsPatient";
    $this->evenement = "evt_serveuretatspatient";
    
    parent::__construct(null, "msgEvenementsServeurEtatsPatient");
  }

  /**
   * Generate header message
   *
   * @return void
   */
  function generateEnteteMessage() {
    parent::generateEnteteMessage("evenementsServeurEtatsPatient");
  }

  /**
   * Generate content message
   *
   * @param CSejour $sejour Admit
   *
   * @return void
   */
  function generateFromOperation(CSejour $sejour) {
    $evenementsServeurEtatsPatient = $this->documentElement;

    // Ajout du patient
    $mbPatient = $sejour->_ref_patient;
    $patient   = $this->addElement($evenementsServeurEtatsPatient, "patient");
    $this->addPatient($patient, $mbPatient, false, true);
    
    // Ajout de la venue, c'est-à-dire le séjour
    $venue = $this->addElement($evenementsServeurEtatsPatient, "venue");
    $this->addVenue($venue, $sejour, false, true);
    
    $dateObservation = $this->addElement($evenementsServeurEtatsPatient, "dateObservation");
    $this->addDateHeure($dateObservation, CMbDT::dateTime());
    
    // Ajout des diagnostics
    $Diagnostics = $this->addElement($evenementsServeurEtatsPatient, "Diagnostics");
    $this->addDiagnosticsEtat($Diagnostics, $sejour);
    
    // Traitement final
    $this->purgeEmptyElements();
  }

  /**
   * Get contents XML
   *
   * @return array
   */
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