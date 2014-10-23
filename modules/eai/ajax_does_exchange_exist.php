<?php
/**
 * Show exchange
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

$exchange_id    = CValue::get("exchange_id");
$exchange_class = CValue::get("exchange_class");

$exchange = new $exchange_class;

$msg = "";
if ($exchange_id) {
  $exchange->load($exchange_id);

  if ($exchange->_id) {
    $msg = $exchange->_id;
  }
}

echo json_encode($msg);