<?php
/**
 * $Id: CHL7v2RecordObservationResultSet.class.php 16357 2012-08-10 08:18:37Z lryo $
 * 
 * @package    Mediboard
 * @subpackage hprim21
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 16357 $
 */

/**
 * Class CHprim21RecordPayment 
 * Record payment, message XML
 */
class CHprim21RecordPayment extends CHPrim21MessageXML {  
  function getContentNodes() {
    $data = array();
    
    $exchange_hpr = $this->_ref_exchange_hpr;
    $sender       = $exchange_hpr->_ref_sender;
    $sender->loadConfigValues();
    
    
    
    return $data;
  }
 
  function handle($ack, CMbObject $object, $data) {
    
  } 
}
