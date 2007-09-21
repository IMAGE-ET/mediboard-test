<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

// Type de tri
$selTri = mbGetValueFromGetOrSession("selTri", "nom");
$order_col = mbGetValueFromGetOrSession("order_col", "_nomPatient");
$order_way = mbGetValueFromGetOrSession("order_way", "ASC");
// Type d'affichage
$vue = mbGetValueFromGetOrSession("vue", 0);

// Récupération des dates
$date = mbGetValueFromGetOrSession("date", mbDate());

$date_actuelle = mbDateTime("00:00:00");
$date_demain = mbDateTime("00:00:00","+ 1 day");

$date_min = mbDateTime("00:00:00", $date);
$date_max = mbDateTime("23:59:00", $date);


$date_sortie = mbDateTime();

$now  = mbDate();

// Récupération des sorties du jour
$listSejourAmbu = new CSejour();
$limit1 = $date." 00:00:00";
$limit2 = $date." 23:59:59";
$ljoinAmbu["patients"] = "sejour.patient_id = patients.patient_id";
$ljoinAmbu["users"] = "sejour.praticien_id = users.user_id";
$whereAmbu["sortie_prevue"] = "BETWEEN '$limit1' AND '$limit2'";
$whereAmbu["type"] = " = 'ambu'";
$whereAmbu["group_id"] = "= '$g'";
$whereAmbu["annule"] = " = '0'";

if($vue) {
  $ljoinAmbu["affectation"] = "sejour.sejour_id = affectation.sejour_id";
  $whereAmbu["effectue"] = "= '0'";
}

if($order_col != "_nomPatient" && $order_col != "sortie_prevue" && $order_col != "_nomPraticien"){
	$order_col = "_nomPatient";	
}


if($order_col == "_nomPatient"){
  $orderAmbu = "patients.nom $order_way, patients.prenom, sejour.entree_prevue";
}
if($order_col == "sortie_prevue"){
  $orderAmbu = "sejour.entree_prevue $order_way, patients.nom, patients.prenom";
}
if($order_col == "_nomPraticien"){
  $orderAmbu = "users.user_last_name $order_way, users.user_first_name";
}

$listSejourAmbu = $listSejourAmbu->loadList($whereAmbu, $orderAmbu, null, null, $ljoinAmbu);

foreach($listSejourAmbu as $key => $sejour){
  $sejour->loadRefPatient();
  $sejour->loadRefPraticien();
  $sejour->loadRefsAffectations();
  $sejour->loadNumDossier();
  $affectation =& $sejour->_ref_last_affectation;
  
  if($affectation->affectation_id){
  	$affectation->loadReflit();
  	$affectation->_ref_lit->loadCompleteView();
  }
 
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);

$smarty->assign("order_col", $order_col);
$smarty->assign("order_way", $order_way);
$smarty->assign("selTri"   , $selTri);

$smarty->assign("date_demain", $date_demain);
$smarty->assign("date_actuelle", $date_actuelle);
$smarty->assign("date"     , $date );
$smarty->assign("now"      , $now );
$smarty->assign("vue"      , $vue );
$smarty->assign("listSejourAmbu"       , $listSejourAmbu );
$smarty->assign("date_sortie", $date_sortie);
$smarty->display("inc_vw_sorties_ambu.tpl");

?>