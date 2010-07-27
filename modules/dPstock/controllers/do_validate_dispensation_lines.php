<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$list = CValue::post("d", array());

foreach($list as $_id => $_qty) {
  $delivery = new CProductDelivery;
  $delivery->load($_id);
  $delivery->date_dispensation = mbDateTime();
  $delivery->order = 0;
  if ($msg = $delivery->store()) {
    CAppUI::setMsg($msg, UI_MSG_WARNING);
  }
  else {
    CAppUI::setMsg("Dispensation validée");
  }
}

echo CAppUI::getMsg();
CApp::rip();
