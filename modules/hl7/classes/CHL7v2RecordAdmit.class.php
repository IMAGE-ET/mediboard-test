<?php /* $Id:$ */

/**
 * Record admit, message XML
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2RecordAdmit 
 * Record admit, message XML HL7
 */

class CHL7v2RecordAdmit extends CHL7v2MessageXML {
  function getContentNodes() {
    $data  = parent::getContentNodes();
    
    $this->queryNodes("NK1", null, $data);
    
    $this->queryNodes("ROL", null, $data);    
    
    $this->queryNodes("PV1", null, $data); 
    
    $data["admitIdentifiers"] = $this->getAdmitIdentifiers();
    
    $this->queryNodes("PV2", null, $data); 
    
    $this->queryNodes("ZBE", null, $data);
    
    return $data;
  }
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    // Traitement du message des erreurs
    $comment = $warning = "";
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $exchange_ihe->_ref_sender->loadConfigValues();
    $sender       = $exchange_ihe->_ref_sender;
  } 
}

?>