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
 * Class CHPrimSanteEvent
 * Event H'sante
 */
class CHPrimSanteEvent {
  public $event_type;
  public $object;
  public $last_log;
  public $type;
  public $type_liaison;
  public $version;

  /** @var CHPrimSanteMessage */
  public $message;

  public $msg_hpr;

  public $msg_codes = array();

  /** @var CReceiverHprimSante */
  public $_receiver;

  /** @var CInteropSender */
  public $_sender;

  public $_exchange_hpr;

  /**
   * Build HPR message
   *
   * @param CMbObject $object Object to use
   *
   * @return void
   */
  function build($object) {
  }

  /**
   * handle
   *
   * @param String $msg_hpr message
   *
   * @return DOMDocument
   */
  function handle($msg_hpr) {
    $this->message = new CHPrimSanteMessage();

    $this->message->parse($msg_hpr);

    return $this->message->toXML(get_class($this), true, CApp::$encoding);
  }

  /**
   * get the event class
   *
   * @param CHPrimSanteEvent $event event
   *
   * @return string
   */
  static function getEventClass($event) {
    $classname = "CHPrimSante".$event->type.$event->type_liaison;

    return $classname;
  }

  /**
   * get event
   *
   * @param String $message_name name message
   *
   * @return mixed
   */
  static function getEvent($message_name) {
    $event_class = "CHPrimSante{$message_name}";

    return new $event_class;
  }
}

