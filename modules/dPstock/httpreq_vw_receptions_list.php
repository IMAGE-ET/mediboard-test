<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkEdit();

$start    = intval(CValue::get("start", 0));
$keywords = CValue::get("keywords");

// Chargement des receptions de l'etablissement
$reception = new CProductReception();

$where = array(
  "group_id" => "='".CGroups::loadCurrent()->_id."'"
);
$receptions = $reception->seek($keywords, $where, "$start, 25", true, null, "date DESC");
$total = $reception->_totalSeek;

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