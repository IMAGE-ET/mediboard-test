<?php /* $Id:$ */

/**
 * Record observation result set, message XML
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2RecordObservationResultSet 
 * Record observation result set, message XML
 */

class CHL7v2RecordObservationResultSet extends CHL7v2MessageXML {
  function getContentNodes() {
    $data = parent::getContentNodes();
    
    

    return $data;
  }
 
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    
    mbLog($data);
  }
}
?>