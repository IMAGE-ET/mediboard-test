<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$sender_id = CValue::get("sender_id");

$source_to_vw_sender = new CSourceToViewSender();
$source_to_vw_sender->sender_id = $sender_id;

$view_sender = new CViewSender();
$view_sender->load($sender_id);
$view_sender->loadRefSendersSource();

$smarty = new CSmartyDP();
$smarty->assign("source_to_vw_sender", $source_to_vw_sender);
$smarty->assign("view_sender"        , $view_sender);
$smarty->display("inc_form_source_to_view_sender.tpl");
