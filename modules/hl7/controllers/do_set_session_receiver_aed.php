<?php

/**
 * $Id$
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$cn_receiver_guid = CValue::post("cn_receiver_guid");

if ($cn_receiver_guid == "none") {
  unset($_SESSION["cn_receiver_guid"]);
  return;
}
CValue::setSessionAbs("cn_receiver_guid", $cn_receiver_guid);