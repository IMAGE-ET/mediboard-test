<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$sender_id = CValue::get("sender_id");
$sender = new CViewSender();
$sender->offset = "0";
$sender->load($sender_id);
$sender->loadRefsNotes();

$smarty = new CSmartyDP();
$smarty->assign("sender", $sender);
$smarty->display("inc_form_view_sender.tpl");
