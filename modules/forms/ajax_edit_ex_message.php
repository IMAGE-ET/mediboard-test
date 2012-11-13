<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$ex_message_id = CValue::get("ex_message_id");
$ex_group_id   = CValue::get("ex_group_id");

CExObject::$_locales_cache_enabled = false;
$ex_message = new CExClassMessage;

if ($ex_message->load($ex_message_id)) {
  $ex_message->loadRefsNotes();
}
else {
  $ex_message->ex_group_id = $ex_group_id;
}

$ex_message->loadRefPredicate();
$ex_message->loadRefExGroup()->loadRefExClass();
$ex_message->loadRefProperties();

$smarty = new CSmartyDP();
$smarty->assign("ex_message", $ex_message);
$smarty->display("inc_edit_ex_message.tpl");