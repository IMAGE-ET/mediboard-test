<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$listByPrat = array();
$date = mbGetValueFromGet("date", mbDate());
$type = mbGetValueFromGet("type");
$sejour = new CSejour;
$where = array();
$where[] = "DATE(sejour.entree_prevue) = '". $date ."'";
$where["sejour.annule"] = "= '0'";

if ($type) {
  $where["type"] = " = '$type'";  
}

$ljoin = array();
$ljoin["users"] = "users.user_id = sejour.praticien_id";
$order = "users.user_last_name, users.user_first_name, sejour.entree_prevue";

$sejours = $sejour->loadGroupList($where, $order, null, null, $ljoin);

foreach ($sejours as $key => &$sejour) {
  $sejour->loadRefPraticien();
  $sejour->loadRefsAffectations();
  $sejour->loadRefPatient();
  $sejour->loadRefPrestation();
  $sejour->_ref_first_affectation->loadRefLit();
  $sejour->_ref_first_affectation->_ref_lit->loadCompleteView();
  
  $curr_prat = $sejour->praticien_id;
  $listByPrat[$curr_prat]["praticien"] =& $sejour->_ref_praticien;
  $listByPrat[$curr_prat]["sejours"][] =& $sejour;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"      , $date);
$smarty->assign("listByPrat", $listByPrat);
$smarty->assign("total"     , count($sejours));

$smarty->display("print_entrees.tpl");

?>