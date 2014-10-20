<?php 

/**
 * $Id$
 *  
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
$bris = new CBrisDeGlace();
$bris->date = CMbDT::dateTime();
$bris->user_id = CMediusers::get()->_id;
$bris->group_id = CGroups::loadCurrent()->_id;
$bris->comment = CValue::post("comment");
$bris->object_class = CValue::post("object_class");
$bris->object_id = CValue::post("object_id");
if ($msg = $bris->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}
else {
  CAppUI::setMsg($bris->_class."-store", UI_MSG_OK);
  CAppUI::js("afterSuccessB2G()");
}

echo CAppUI::getMsg();

CApp::rip();