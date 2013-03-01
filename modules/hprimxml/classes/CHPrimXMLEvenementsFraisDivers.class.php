<?php

/**
 * Frais divers
 *
 * @category Hprimxml
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimXMLEvenementsFraisDivers
 * Frais divers
 */
class CHPrimXMLEvenementsFraisDivers extends CHPrimXMLEvenementsServeurActivitePmsi {
  /**
   * Construct
   *
   * @return CHPrimXMLEvenementsFraisDivers
   */
  function __construct() {
    $this->sous_type = "evenementFraisDivers";
    $this->evenement = "evt_frais_divers";
    
    parent::__construct("evenementFraisDivers", "msgEvenementsFraisDivers");
  }

  /**
   * Generate header message
   *
   * @return void
   */
  function generateEnteteMessage() {
    parent::generateEnteteMessage("evenementsFraisDivers");
  }

  /**
   * Generate content message
   *
   * @param CSejour $mbSejour Admit
   *
   * @return void
   */
  function generateFromOperation(CSejour $mbSejour) {
    $evenementsFraisDivers = $this->documentElement;

    $evenementFraisDivers = $this->addElement($evenementsFraisDivers, "evenementFraisDivers");

    // Ajout du patient (light)
    $mbPatient =& $mbSejour->_ref_patient;
    $patient = $this->addElement($evenementFraisDivers, "patient");
    $this->addPatient($patient, $mbPatient, false, true);
    
    // Ajout de la venue, c'est-à-dire le séjour (light)
    $venue = $this->addElement($evenementFraisDivers, "venue");
    $this->addVenue($venue, $mbSejour, false, true);

    // Ajout des frais divers
    foreach ($mbSejour->_ref_frais_divers as $_mb_frais_divers) {
      $_mb_frais_divers->loadRefType();
      $_mb_frais_divers->loadRefExecutant();
      $_mb_frais_divers->loadExecution();
      
      $this->addFraisDivers($evenementFraisDivers, $_mb_frais_divers);
    }
    
    if ($mbSejour->_ref_consultations) {
      foreach ($mbSejour->_ref_consultations as $_consultation) {
        foreach ($_consultation->_ref_frais_divers as $_mb_frais_divers) {
          $_mb_frais_divers->loadRefType();
          $_mb_frais_divers->loadExecution();
          $_mb_frais_divers->loadRefExecutant();
          
          $this->addFraisDivers($evenementFraisDivers, $_mb_frais_divers);
        }
      }
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
    
    $evenementFraisDivers = $xpath->queryUniqueNode("/hprim:evenementsFraisDivers/hprim:evenementFraisDivers");
    
    $data['patient']         = $xpath->queryUniqueNode("hprim:patient", $evenementFraisDivers);
    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['venue']           = $xpath->queryUniqueNode("hprim:venue", $evenementFraisDivers);
    $data['idSourceVenue']   = $this->getIdSource($data['venue']);
    $data['idCibleVenue']    = $this->getIdCible($data['venue']);
    
    return $data; 
  }
}