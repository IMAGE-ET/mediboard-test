<?php /* $Id: ajax_edit_token.php 16523 2012-09-05 09:04:59Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 16523 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$pref_id = CValue::getOrSession("pref_id");

$preference = new CPreferences();
$preference->load($pref_id);
$preference->loadRefsNotes();

$smarty = new CSmartyDP();
$smarty->assign("preference", $preference);
$smarty->display("inc_edit_pref.tpl");
