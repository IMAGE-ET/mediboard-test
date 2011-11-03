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
  function getContentsXML() {
    $data  = parent::getContentsXML();
    
    $xpath = new CHL7v2MessageXPath($this);
    
    
    
    return $data;
  }
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    // Traitement du message des erreurs
    $comment = $warning = "";
    
  } 
}

?>