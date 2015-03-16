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
 * Syslog Receiver
 */
class CSyslogReceiver extends CInteropReceiver {
  /** @var integer Primary key */
  public $syslog_receiver_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec           = parent::getSpec();
    $spec->table    = "syslog_receiver";
    $spec->key      = "syslog_receiver_id";
    $spec->messages = array(
      "iti" => array("CSyslogITI")
    );

    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps             = parent::getBackProps();
    $backProps['echanges'] = "CSyslogExchange receiver_id";

    return $backProps;
  }
}
