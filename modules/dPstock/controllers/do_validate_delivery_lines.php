<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$list = explode("-", CValue::post("list", ""));
CMbArray::removeValue("", $list);

foreach ($list as $_id) {
  $delivery = new CProductDelivery;
  $delivery->load($_id);
  $delivery->date_delivery = CMbDT::dateTime();
  if ($msg = $delivery->store()) {
    CAppUI::setMsg($msg, UI_MSG_WARNING);
  }
  else {
    CAppUI::setMsg("Réception terminée");
  }
}

echo CAppUI::getMsg();
CApp::rip();
