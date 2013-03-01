<?php

/**
 * PMSI
 *
 * @category Hprimxml
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimXMLEvenementsPmsi
 * PMSI
 */

class CHPrimXMLEvenementsPmsi extends CHPrimXMLEvenementsServeurActivitePmsi {
  /**
   * Construct
   *
   * @return CHPrimXMLEvenementsPmsi
   */
  function __construct() {
    $this->sous_type = "evenementPMSI";
    $this->evenement = "evt_pmsi";

    parent::__construct("evenementPmsi", "msgEvenementsPmsi");
  }

  /**
   * Generate header message
   *
   * @return void
   */
  function generateEnteteMessage() {
    parent::generateEnteteMessage("evenementsPMSI");
  }

  /**
   * Generate content message
   *
   * @param CSejour $mbSejour Admit
   *
   * @return void
   */
  function generateFromOperation(CSejour $mbSejour) {
    $evenementsPMSI = $this->documentElement;

    $evenementPMSI = $this->addElement($evenementsPMSI, "evenementPMSI");

    // Ajout du patient
    $mbPatient = $mbSejour->_ref_patient;
    $patient = $this->addElement($evenementPMSI, "patient");
    $this->addPatient($patient, $mbPatient, false, true);
    
    // Ajout de la venue, c'est-à-dire le séjour
    $venue = $this->addElement($evenementPMSI, "venue");
    $this->addVenue($venue, $mbSejour, false, true);
    
    if ($mbSejour->type == "ssr") {
      // Ajout du contenu rhss
      $rhss = $this->addElement($evenementPMSI, "rhss");
      $this->addSsr($rhss, $mbSejour);
    }
    else {
      // Ajout de la saisie délocalisée
      $saisie = $this->addElement($evenementPMSI, "saisieDelocalisee");
      $this->addSaisieDelocalisee($saisie, $mbSejour);
    }

    // Traitement final
    $this->purgeEmptyElements();
  }

  /**
   * Get content XML
   *
   * @return array
   */
  function getContentsXML() {
    $data = array();
    $xpath = new CHPrimXPath($this);   
    
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