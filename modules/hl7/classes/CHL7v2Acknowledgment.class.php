<?php

/**
 * Acknowledgment v2 HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2Acknowledgment 
 * Acknowledgment v2 HL7
 */
class CHL7v2Acknowledgment extends CHL7v2MessageXML implements CHL7Acknowledgment {
  var $_identifiant_acquitte = null;
  
  var $_ref_exchange_ihe     = null;
  
  function generateAcknowledgment() {
    
  }
}

?>