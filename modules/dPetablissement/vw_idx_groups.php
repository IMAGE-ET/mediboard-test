<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Récupération du groupe selectionné
$group = new CGroups;
$group->load(CValue::getOrSession("group_id"));
$group->loadFunctions();
$group->loadRefsNotes();

// Récupération des fonctions
$groups = $group->loadListWithPerms(PERM_READ);
foreach ($groups as $_group) {
  $_group->loadFunctions();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("group" , $group);
$smarty->assign("groups", $groups);

$smarty->display("vw_idx_groups.tpl");

?>