<?php

/**
 * $Id$
 *  
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Classe exchange XDS
 */
class CExchangeXDS extends CEchangeXML{
  /** @var integer Primary key */
  public $exchange_xds_id;

  static $messages = array(
    "consumer" => "CXDSConsumer",
    "producer" => "CXDSProducer"
  );

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "exchange_xds";
    $spec->key    = "exchange_xds_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["object_class"]  = "enum list|CSejour|COperation|CConsultation show|0";
    $props["receiver_id"]   = "ref class|CReceiverDMP";
    return $props;
  }

  /**
   * return the messages
   *
   * @return array
   */
  function getFamily() {
    return self::$messages;
  }
}