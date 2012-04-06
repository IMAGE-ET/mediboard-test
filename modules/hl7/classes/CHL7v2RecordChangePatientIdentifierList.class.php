<?php /* $Id:$ */

/**
 * Change patient identifier list, message XML
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2RecordChangePatientIdentifierList 
 * Change patient identifier list, message XML HL7
 */

class CHL7v2RecordChangePatientIdentifierList extends CHL7v2MessageXML {
  function getContentNodes() {
    $data = parent::getContentNodes();

    $this->queryNode("MRG", null, $data, true);
       
    return $data;
  }
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    // Traitement du message des erreurs
    $comment = $warning = "";
    
    return $exchange_ihe->setAckAR($ack, "E140", $comment, $newPatient);
  }
}

?>