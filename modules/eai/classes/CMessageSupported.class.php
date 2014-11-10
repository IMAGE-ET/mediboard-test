<?php

/**
 * Message supported
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CMessageSupported
 * Message supported
 */
class CMessageSupported extends CMbMetaObject {
  public $message_supported_id;
  
  public $message;
  public $active;
  public $profil;
  public $transaction;

  /** @var  CExchangeDataFormat */
  public $_data_format;

  public $_event = null;

  /**
   * @see parent::getSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "message_supported";
    $spec->key   = "message_supported_id";
    return $spec;
  }

  /**
   * @see parent::getProps
   */
  function getProps() {
    $props = parent::getProps();

    $props["object_id"]    = "ref notNull class|CInteropActor meta|object_class cascade";
    $props["object_class"] = "str notNull show|0";
    $props["message"]      = "str notNull";
    $props["active"]       = "bool default|0";
    $props["profil"]       = "str";
    $props["transaction"]  = "str";
    
    return $props;
  }

  /**
   * Load event by name
   *
   * @return mixed
   */
  function loadEventByName() {
    $classname = $this->message;

    if (preg_match_all('/ADT|QBP|ORU|QCN|QBP|ORM|SIU/', $classname)) {
      $classname = str_replace("CHL7Event", "CHL7v2Event", $classname);
    }

    return $this->_event = new $classname;
  }
}
