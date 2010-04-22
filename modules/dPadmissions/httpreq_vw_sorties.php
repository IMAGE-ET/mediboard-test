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

// Type de tri
$selTri = CValue::getOrSession("selTri", "nom");
$order_col = CValue::getOrSession("order_col", "patient_id");
$order_way = CValue::getOrSession("order_way", "ASC");
$filter_function_id = CValue::getOrSession("filter_function_id");

// Mode => ambu ou comp
$mode = CValue::getOrSession("mode");

// Type d'affichage
$vue = CValue::getOrSession("vue", 0);

// Récupération des dates
$date = CValue::getOrSession("date", mbDate());

$date_actuelle = mbDateTime("00:00:00");
$date_demain = mbDateTime("00:00:00","+ 1 day");

$date_min = mbDateTime("00:00:00", $date);
$date_max = mbDateTime("23:59:00", $date);


$date_sortie = mbDateTime();

$now  = mbDate();

// Récupération des sorties du jour
$listSejour = new CSejour();
$limit1 = $date." 00:00:00";
$limit2 = $date." 23:59:59";
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$ljoin["users"] = "sejour.praticien_id = users.user_id";
$ljoin["users_mediboard"] = "sejour.praticien_id = users_mediboard.user_id";
$where["sejour.sortie"] = "BETWEEN '$limit1' AND '$limit2'";
if($mode != "autre") {
  $where["sejour.type"] = " = '$mode'";
} else {
  $where[] = "(type != 'comp' AND type != 'ambu')";
}
$where["sejour.annule"] = " = '0'";

// Afficher seulement les sorties non effectuées (sejour sans date de sortie reelle)
if($vue) {
  $where["sejour.sortie_reelle"] = "IS NULL";
}

if($order_col != "patient_id" && $order_col != "sortie_prevue" && $order_col != "praticien_id"){
	$order_col = "patient_id";	
}

$order = "sejour.type,";


if($order_col == "patient_id"){
  $order .= "patients.nom $order_way, patients.prenom, sejour.entree_prevue";
}
if($order_col == "sortie_prevue"){
  $order .= "sejour.sortie_prevue $order_way, patients.nom, patients.prenom";
}
if($order_col == "praticien_id"){
  $order .= "users.user_last_name $order_way, users.user_first_name";
}

if($filter_function_id){
	$where["users_mediboard.function_id"] = " = '$filter_function_id'";
}

$listSejour = $listSejour->loadGroupList($where, $order, null, null, $ljoin);

foreach($listSejour as $key => $sejour){
  $sejour->loadRefPatient();
  $sejour->loadRefPraticien();
  $sejour->loadRefsAffectations("sortie ASC");
  $sejour->loadRefEtabExterne();
  $sejour->loadNumDossier();
  $affectation =& $sejour->_ref_last_affectation;
  
  if($affectation->affectation_id){
  	$affectation->loadReflit();
  	$affectation->_ref_lit->loadCompleteView();
  }

  foreach($sejour->_ref_affectations as $key => $affect){
    $affect->loadRefLit();
    $affect->_ref_lit->loadCompleteView();
  }
}



// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date_min"      , $date_min);
$smarty->assign("date_max"      , $date_max);
$smarty->assign("order_col"     , $order_col);
$smarty->assign("order_way"     , $order_way);
$smarty->assign("selTri"        , $selTri);
$smarty->assign("canAdmissions" , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPatients"   , CModule::getCanDo("dPpatients"));
$smarty->assign("canPlanningOp" , CModule::getCanDo("dPplanningOp"));
$smarty->assign("date_demain"   , $date_demain);
$smarty->assign("date_actuelle" , $date_actuelle);
$smarty->assign("date"          , $date );
$smarty->assign("now"           , $now );
$smarty->assign("vue"           , $vue );
$smarty->assign("listSejour"    , $listSejour );
$smarty->assign("date_sortie"   , $date_sortie);
$smarty->assign("mode"          , $mode);

$smarty->display("inc_vw_sorties.tpl");

?>