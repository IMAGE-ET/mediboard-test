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

$sender_source_id = CValue::get("sender_source_id");
$sender_source    = new CViewSenderSource();
$sender_source->load($sender_source_id);
$sender_source->loadRefsNotes();
$sender_source->loadRefSourceFTP();

$smarty = new CSmartyDP();
$smarty->assign("sender_source", $sender_source);
$smarty->display("inc_form_view_sender_source.tpl");
