<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

$date = CValue::get("date", mbDate());
$type = CValue::get("type", "ambu_comp");

$sejour = new CSejour;
$where = array();
$where[] = "DATE(sejour.sortie_prevue) = '". $date ."'";
$where["sejour.annule"] = "= '0'";
if($type == "ambu_comp") {
  $where[] = "sejour.type = 'ambu' OR sejour.type = 'comp'";
} else {
  $where[] = "sejour.type != 'ambu' AND sejour.type != 'comp'";
}
$ljoin = array();
$ljoin["users"] = "users.user_id = sejour.praticien_id";
$order = "users.user_last_name, users.user_first_name, sejour.sortie_prevue";

$sejours = $sejour->loadGroupList($where, $order, null, null, $ljoin);

$listByPrat = array();

foreach ($sejours as $key => &$sejour) {
  $sejour->loadRefPraticien();
  $sejour->loadRefsAffectations();
  $sejour->loadRefPatient();
  $sejour->_ref_last_affectation->loadRefLit();
  $sejour->_ref_last_affectation->_ref_lit->loadCompleteView();
  
  $curr_prat = $sejour->praticien_id;
  $listByPrat[$curr_prat]["praticien"] =& $sejour->_ref_praticien;
  $listByPrat[$curr_prat]["sejours"][] =& $sejour;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"      , $date);
$smarty->assign("type"      , $type);
$smarty->assign("listByPrat", $listByPrat);
$smarty->assign("total"     , count($sejours));

$smarty->display("print_sorties.tpl");

?>