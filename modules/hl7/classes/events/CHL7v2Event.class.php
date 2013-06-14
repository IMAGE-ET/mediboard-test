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
   * Generate exchange HL7v2
   *
   * @return CExchangeHL7v2
   */
  function generateExchange() {
    $exchange_hl7v2                  = new CExchangeHL7v2();
    $exchange_hl7v2->date_production = CMbDT::dateTime();
    $exchange_hl7v2->receiver_id     = $this->_receiver->_id;
    $exchange_hl7v2->group_id        = $this->_receiver->group_id;
    $exchange_hl7v2->sender_id       = $this->_sender ? $this->_sender->_id : null;
    $exchange_hl7v2->sender_class    = $this->_sender ? $this->_sender->_id : null;
    $exchange_hl7v2->version         = $this->version;
    $exchange_hl7v2->type            = $this->profil;
    $exchange_hl7v2->sous_type       = $this->transaction;
    $exchange_hl7v2->code            = $this->code;
    $exchange_hl7v2->object_id       = $this->object->_id;
    $exchange_hl7v2->object_class    = $this->object->_class;
    $exchange_hl7v2->store();

    return $this->_exchange_hl7v2 = $exchange_hl7v2;
  }

  /**
   * Update exchange HL7v2 with
   *
   * @return CExchangeHL7v2
   */
  function updateExchange() {
    $exchange_hl7v2                 = $this->_exchange_hl7v2;
    $exchange_hl7v2->_message       = $this->msg_hl7;
    $exchange_hl7v2->message_valide = $this->message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;
    $exchange_hl7v2->store();
    
    return $exchange_hl7v2;
  }
}