<?php

/**
 * $Id$
 *
 * @category Hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Classe CHPREventERR
 * Represents a ERR message structure (see chapter 2.14.1)
 */
class CHPrimSanteEventERR extends CHPrimSanteEvent {
  /**
   * construct
   *
   * @param CHPrimSanteEvent $trigger_event trigger event
   */
  function __construct(CHPrimSanteEvent $trigger_event) {
    $this->event_type  = "ERR";
    $this->version     = $trigger_event->message->version;

    $this->msg_codes   = array (
      array(
        $trigger_event->type, $trigger_event->type_liaison
      )
    );

    $this->_exchange_hpr = $trigger_event->_exchange_hpr;
    $this->_receiver     = $trigger_event->_exchange_hpr->_ref_receiver;
    $this->_sender       = $trigger_event->_exchange_hpr->_ref_sender;
  }

  /**
   * @see parent::build
   */
  function build($object) {
    // Création du message HPR
    $this->message          = new CHPrimSanteMessage();
    $this->message->version = $this->version;
    $this->message->name    = $this->event_type;

    // Message Header
    $this->addH();

    // Errors
    foreach ($object->errors as $_error) {

      $object->_error = $_error;

      if ($_error->type_error == "T" && $this->_exchange_hpr->statut_acquittement != "T") {
        $this->_exchange_hpr->statut_acquittement = $_error->type_error;
      }
      else if ($_error->type_error == "P") {
        $this->_exchange_hpr->statut_acquittement = $_error->type_error;
      }

      $this->addERR($object);
    }

    // Validation error

    // Message Footer
    $this->addL();
  }

  /**
   * create the segment
   *
   * @param String             $name   name
   * @param CHL7v2SegmentGroup $parent parent
   *
   * @return CHPrimSanteEventERR
   */
  function createSegment($name, CHL7v2SegmentGroup $parent) {
    $class = "CHPrimSanteSegment$name";

    if (class_exists($class)) {
      $segment = new $class($parent);
    }
    else {
      $segment = new self($parent);
    }

    $segment->name = substr($name, 0, 3);

    return $segment;
  }

  /**
   * H - Represents an HPR H message segment (Message Header)
   *
   * @return void
   */
  function addH() {
    $H = $this->createSegment("H", $this->message);
    $H->build($this);
  }

  /**
   * ERR - Represents an HPR ERR message segment (Error)
   *
   * @param CHPrimSanteAcknowledgment $acknowledgment acknowledgment
   * @param CHPrimSanteError          $error          error
   *
   * @return void
   */
  function addERR(CHPrimSanteAcknowledgment $acknowledgment, $error = null) {
    $ERR = $this->createSegment("ERR", $this->message);
    $ERR->acknowledgment = $acknowledgment;
    $ERR->build($this);
  }

  /**
   * L - Represents an HPR L message segment (Message Footer)
   *
   * @return void
   */
  function addL() {
    $L = $this->createSegment("L", $this->message);
    $L->build($this);
  }

  /**
   * flatten
   *
   * @return void
   */
  function flatten() {
    $this->msg_hpr = $this->message->flatten();
    $this->message->validate();
  }
}

