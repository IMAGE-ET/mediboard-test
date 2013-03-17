<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
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
