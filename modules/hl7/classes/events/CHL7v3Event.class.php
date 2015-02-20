<?php

/**
 * Event HL7v3
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v3Event
 * Event HL7v3
 */
class CHL7v3Event extends CHL7Event {
  /** @var  CHL7v3MessageXML $dom */
  public $dom;

  /** @var string */
  public $interaction_id = null;
  public $version        = "2008";

  /** @var string */
  public $_event_name;

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

    // Génération de l'échange
    $this->generateExchange();

    $this->dom = new CHL7v3MessageXML("utf-8", $this->version);
  }

  /**
   * Generate exchange HL7v3
   *
   * @return CExchangeHL7v3
   */
  function generateExchange() {
    $exchange_hl7v3                  = new CExchangeHL7v3();
    $exchange_hl7v3->date_production = CMbDT::dateTime();
    $exchange_hl7v3->receiver_id     = $this->_receiver->_id;
    $exchange_hl7v3->group_id        = $this->_receiver->group_id;
    $exchange_hl7v3->sender_id       = $this->_sender ? $this->_sender->_id : null;
    $exchange_hl7v3->sender_class    = $this->_sender ? $this->_sender->_id : null;
    $exchange_hl7v3->type            = $this->event_type;
    $exchange_hl7v3->sous_type       = $this->interaction_id;
    $exchange_hl7v3->object_id       = $this->object->_id;
    $exchange_hl7v3->object_class    = $this->object->_class;
    $exchange_hl7v3->store();

    return $this->_exchange_hl7v3 = $exchange_hl7v3;
  }

  /**
   * Update exchange HL7v3 with
   *
   * @param Bool $validate Validate message
   *
   * @return CExchangeHL7v3
   */
  function updateExchange($validate = true) {
    $exchange_hl7v3                 = $this->_exchange_hl7v3;
    $exchange_hl7v3->_message       = $this->message;
    $exchange_hl7v3->message_valide = $validate ? $this->dom->schemaValidate() : true;
    $exchange_hl7v3->store();

    return $exchange_hl7v3;
  }
}