<?php

/**
 * Z99 - Change admit - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTZ99_FR
 * Z99 - Change admit
 */
class CHL7v2EventADTZ99_FR extends CHL7v2EventADT implements CHL7EventADTZ99 {
  function __construct($i18n = null) {
    parent::__construct($i18n);
        
    $this->code        = "Z99";
    $this->transaction = CPAMFR::getTransaction($this->code);
    $this->msg_codes   = array ( 
      array(
        $this->event_type, $this->code
      )
    );
  }
  
  function build($sejour) {
    parent::build($sejour);
    
    $patient = $sejour->_ref_patient;
    // Patient Identification
    $this->addPID($patient);
    
    // Patient Additional Demographic
    $this->addPD1($patient);
    
    // Doctors
    $this->addROLs($patient);
    
    // Next of Kin / Associated Parties
    $this->addNK1s($patient);
    
    // Patient Visit
    $this->addPV1($sejour);
    
    // Patient Visit - Additionale Info
    $this->addPV2($sejour);
    
    // Movement segment
    $this->addZBE($sejour);
    
    // Situation professionnelle
    // Si A01, A04, A05, A14
    $this->addZFP($sejour);
    
    // Compléments sur la rencontre
    // Si A01, A02, A03, A04, A05, A14, A21
    $this->addZFV($sejour);
    
    // Mouvement PMSI
    // Si A01, A02, A03, A04, A05, A14, 
    // Z80, Z81, Z82, Z83, Z84, Z85, Z86, Z87 
    $this->addZFM($sejour);
    
    // Complément démographique
    // Si A01, A04, A05, A14
    $this->addZFD($sejour);
  }
  
}

?>