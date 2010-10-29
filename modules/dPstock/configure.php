<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$group = new CGroups;
$groups_list = $group->loadList(null, "text");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("groups_list", $groups_list);
$smarty->display('configure.tpl');

?>