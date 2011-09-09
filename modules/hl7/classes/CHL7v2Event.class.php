<?php

/**
 * Event HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2Event 
 * Event HL7
 */
class CHL7v2Event {
  var $object        = null;
  var $last_log      = null;
  var $profil        = null;
  var $transaction   = null;
  var $code          = null;
  var $version       = null;
  
  var $message       = null;

  var $msg_codes     = array();
  
  var $_receiver     = null;
  var $_sender       = null;
  var $_exchange_ihe = null;  
  
  function __construct() {}
  
  function build($object) {
    // Traitement sur le mbObject
    $this->object   = $object;
    $this->last_log = $this->object->_ref_last_log;
    
    // Récupération de la version HL7 en fonction du receiver et de la transaction
    $this->version  = $this->_receiver->_configs[$this->transaction."_HL7_version"];
    
    // Génération de l'échange
    $this->generateExchange();
 
    // Création du message HL7
    $this->message          = new CHL7v2Message();
    $this->message->version = $this->version;
    $this->message->name    = $this->msg_codes[0].$this->msg_codes[1];
  }
  
  function flatten() {
    $msg_hl7 = $this->message->flatten();
    mbTrace($msg_hl7, "Message HL7 généré");
    $this->message->validate();
    mbTrace($this->message->errors, "Erreurs message HL7");
  }
  
  function generateExchange() {
    $exchange_ihe                  = new CExchangeIHE();
    $exchange_ihe->date_production = mbDateTime();
    $exchange_ihe->receiver_id     = $this->_receiver->_id;
    $exchange_ihe->group_id        = $this->_receiver->group_id;
    $exchange_ihe->sender_id       = $this->_sender ? $this->_sender->_id : null;
    $exchange_ihe->version         = $this->version;
    $exchange_ihe->type            = $this->profil;
    $exchange_ihe->sous_type       = $this->transaction;
    $exchange_ihe->code            = $this->code;
    $exchange_ihe->object_id       = $this->object->_id;
    $exchange_ihe->store();
    
    return $this->_exchange_ihe = $exchange_ihe;
  }
}

?>