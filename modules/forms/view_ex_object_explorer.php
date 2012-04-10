<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$date_min = CValue::getOrSession("date_min", mbDate("-1 MONTH"));
$date_max = CValue::getOrSession("date_max", mbDate());

$group = new CGroups;
$groups = $group->loadListWithPerms(PERM_READ);

$field = new CExClassField;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->assign("groups", $groups);
$smarty->assign("field", $field);
$smarty->display("view_ex_object_explorer.tpl");
