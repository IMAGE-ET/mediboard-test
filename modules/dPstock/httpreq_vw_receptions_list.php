<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkEdit();

$start = intval(CValue::get("start", 0));

// Chargement des receptions de l'etablissement
$reception = new CProductReception();
$reception->group_id = CGroups::loadCurrent()->_id;
$receptions = $reception->loadMatchingList("date DESC", "$start, 25");
$total = $reception->countMatchingList();

foreach($receptions as $_reception){
	$_reception->countReceptionItems();
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("reception", $reception);
$smarty->assign("receptions", $receptions);
$smarty->assign("total", $total);
$smarty->assign("start", $start);
$smarty->display('inc_receptions_list.tpl');

?>