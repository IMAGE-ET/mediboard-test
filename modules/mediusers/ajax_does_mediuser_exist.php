<?php
/**
 * Show mediuser
 *
 * @category mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

$adeli = CValue::get("adeli");

$msg = "";
if ($adeli) {
  $mediuser = CMediusers::loadFromAdeli($adeli);

  if ($mediuser->_id) {
    $msg = $mediuser->_id;
  }
}

echo json_encode($msg);