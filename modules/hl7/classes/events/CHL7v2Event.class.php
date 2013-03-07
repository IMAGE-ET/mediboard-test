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
class CHL7v2Event extends CHL7Event {
  /**
   * Get segment terminator
   *
   * @param string $st The key of the value to get
   *
   * @return mixed
   */
  private function getSegmentTerminator($st) {
    $terminators = array(
      "CR"   => "\r",
      "LF"   => "\n",
      "CRLF" => "\r\n",
    );
    
    return CValue::read($terminators, $st, CHL7v2Message::DEFAULT_SEGMENT_TERMINATOR);
  }

  /**
   * Build event
   *
   * @param CMbObject $object Object
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($object) {
    // Traitement sur le mbObject
    $this->object   = $object;
    $this->last_log = $object->loadLastLog();
    
    // Récupération de la version HL7 en fonction du receiver et de la transaction
    $this->version  = $this->_receiver->_configs[$this->transaction."_HL7_version"];
    
    // Génération de l'échange
    $this->generateExchange();
 
    $terminator = $this->getSegmentTerminator($this->_receiver->_configs["ER7_segment_terminator"]);
    
    // Création du message HL7
    $message = new CHL7v2Message($this->version);
    $message->segmentTerminator = $terminator;
    $message->name              = $this->msg_codes;
   
    $this->message = $message;
  }

  /**
   * Handle event
   *
   * @param string $msg_hl7 HL7 message
   *
   * @return DOMDocument|void
   */
  function handle($msg_hl7) {
    $this->message = new CHL7v2Message();

    if ($this->_data_format) {
      $strict = $this->_data_format->_configs_format->strict_segment_terminator;
      $this->message->strict_segment_terminator = $strict;
      
      if ($strict) {
        $terminator = $this->getSegmentTerminator($this->_data_format->_configs_format->segment_terminator);
        $this->message->segmentTerminator = $terminator;
      }
    }

    $this->message->parse($msg_hl7);

    return $this->message->toXML(get_class($this), false, CApp::$encoding);
  }

  /**
   * Get the message as a string
   *
   * @return string
   */
  function flatten() {
    $this->msg_hl7 = $this->message->flatten();
    $this->message->validate();
    
    $this->updateExchange();
    
    return $this->msg_hl7;
  }

  /**
   * Generate exchange IHE
   *
   * @return CExchangeIHE
   */
  function generateExchange() {
    $exchange_ihe                  = new CExchangeIHE();
    $exchange_ihe->date_production = CMbDT::dateTime();
    $exchange_ihe->receiver_id     = $this->_receiver->_id;
    $exchange_ihe->group_id        = $this->_receiver->group_id;
    $exchange_ihe->sender_id       = $this->_sender ? $this->_sender->_id : null;
    $exchange_ihe->sender_class    = $this->_sender ? $this->_sender->_id : null;
    $exchange_ihe->version         = $this->version;
    $exchange_ihe->type            = $this->profil;
    $exchange_ihe->sous_type       = $this->transaction;
    $exchange_ihe->code            = $this->code;
    $exchange_ihe->object_id       = $this->object->_id;
    $exchange_ihe->object_class    = $this->object->_class;
    $exchange_ihe->store();

    return $this->_exchange_ihe = $exchange_ihe;
  }

  /**
   * Update exchange IHE with
   *
   * @return CExchangeIHE
   */
  function updateExchange() {
    $exchange_ihe                 = $this->_exchange_ihe;
    $exchange_ihe->_message       = $this->msg_hl7;
    $exchange_ihe->message_valide = $this->message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;
    $exchange_ihe->store();
    
    return $exchange_ihe;
  }
}