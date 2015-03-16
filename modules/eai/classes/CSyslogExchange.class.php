<?php

/**
 * $Id$
 *
 * @category eai
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Description
 */
class CSyslogExchange extends CEchangeXML {
  /** @var integer Primary key */
  public $syslog_exchange_id;

  static $messages = array(
    "iti"    => "CSyslogITI",
  );

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->table = "syslog_exchange";
    $spec->key   = "syslog_exchange_id";

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props                 = parent::getProps();
    $props["object_class"] = "str show|0";
    $props["receiver_id"]  = "ref class|CSyslogReceiver";
    $props["sender_class"] = "str show|0";

    $props["acquittement_content_id"] = "ref class|CContentAny show|0 cascade";
    $props["_acquittement"]           = "text";

    return $props;
  }

  /**
   * @see parent::getFamily()
   */
  function getFamily() {
    return self::$messages;
  }
}
