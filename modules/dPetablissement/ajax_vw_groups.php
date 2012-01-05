<?php /* $Id: ajax_vw_groups.php $ */

/**
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision: 11630 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Récupération du groupe selectionné
$group = new CGroups;
$group->load(CValue::getOrSession("group_id"));
$group->loadFunctions();
$group->loadRefsNotes();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("group" , $group);

$smarty->display("inc_vw_groups.tpl");

?>