<?php
/**
 * $Id: CHPrim21Acknowledgment.class.php 16236 2012-07-26 08:24:14Z phenxdesign $
 * 
 * @package    Mediboard
 * @subpackage hprim21
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 16236 $
 */

/**
 * Class CHPrim21Acknowledgment 
 * Acknowledgment HPR
 */
class CHPrim21Acknowledgment {
  var $event              = null;
  var $event_ack          = null;
  
  var $message            = null;
  var $dom_message        = null;
  
  var $message_control_id = null;
  var $ack_code           = null;
  var $mb_error_codes     = null;
  var $hpr_error_code     = null;
  var $severity           = null;
  var $comments           = null;
  var $object             = null;
  
  var $_ref_exchange_hpr  = null;
  var $_mb_error_code     = null;
    
  function __construct(CHPREvent $event = null) {
    $this->event = $event;
  }

  function handle($ack_hpr) {
    $this->message = new CHPrim21Message();
    $this->message->parse($ack_hpr);
    $this->dom_message = $this->message->toXML();
    
    return $this->dom_message;
  }
  
  function generateAcknowledgment($ack_code, $mb_error_codes, $hpr_error_code = null, $severity = "E", $comments = null, $object = null) {
    
  }
  
  function getStatutAcknowledgment() {
    
  }
}
