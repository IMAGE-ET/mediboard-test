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
class CSyslogITI extends CInteropNorm {
  /** @var array */
  static $evenements = array(
    "iti9"  => "CSyslogITI9",
    "iti21" => "CSyslogITI21",
    "iti22" => "CSyslogITI22",
    "iti41" => "CSyslogITI41"
  );

  /**
   * @see parent::__construct
   */
  function __construct() {
    $this->type   = "iti";
    $this->domain = "Syslog";

    parent::__construct();
  }

  /**
   * @see parent::getEvenements()
   */
  function getEvenements() {
    return self::$evenements;
  }
}
