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
}
