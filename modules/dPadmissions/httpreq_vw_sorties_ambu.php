<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

// Type d'affichage
$vue = mbGetValueFromGetOrSession("vue", 0);

// Récupération des dates
$date = mbGetValueFromGetOrSession("date", mbDate());

$date_actuelle = mbDateTime("00:00:00");
$date_demain = mbDateTime("00:00:00","+ 1 day");


$date_sortie = mbDateTime();

$now  = mbDate();

// Récupération des sorties du jour
$list = new CAffectation;
$limit1 = $date." 00:00:00";
$limit2 = $date." 23:59:59";
$ljoin["sejour"] = "sejour.sejour_id = affectation.sejour_id";
$ljoin["lit"] = "lit.lit_id = affectation.lit_id";
$ljoin["chambre"] = "chambre.chambre_id = lit.chambre_id";
$ljoin["service"] = "service.service_id = chambre.service_id";
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$where["sortie"] = "BETWEEN '$limit1' AND '$limit2'";
if($vue) {
  $where["effectue"] = "= '0'";
}
$order = "patients.nom, patients.prenom";
$where["type"] = "= 'ambu'";
$where["sejour.group_id"] = "= '$g'";
$listAmbu = $list->loadList($where, $order, null, null, $ljoin);
$tab_temp = array();
foreach($listAmbu as $key => $value) {
  // stockage dans un tableau temporaire  l'id du sejour en clé...
  $tab_temp[$value->sejour_id] = $value;
  
  $listAmbu[$key]->loadRefsFwd();
  if($listAmbu[$key]->_ref_next->affectation_id) {
    unset($listAmbu[$key]);
  } else {
    $listAmbu[$key]->_ref_sejour->loadRefsFwd();
    $listAmbu[$key]->_ref_sejour->loadNumDossier();
    $listAmbu[$key]->_ref_lit->loadCompleteView();
  }
}

//mbTrace($listAmbu);


$sejour = new CSejour();
$whereSejour["type"] = " = 'ambu'";
$whereSejour["sortie_prevue"] = "BETWEEN '$limit1' AND '$limit2'";
$whereSejour["annule"] = " = '0'";

if($vue) {
  $whereSejour["sortie_reelle"] = "IS NULL";
}
$listSejourAmbu = $sejour->loadList($whereSejour);
$listSejourA = array();
foreach($listSejourAmbu as $key=>$sejourAmbu){
    $sejourAmbu->loadRefPraticien();
    $sejourAmbu->loadRefPatient();
    $sejourAmbu->loadNumDossier();
    $listSejourA[$sejourAmbu->_id] = $sejourAmbu;
    if(array_key_exists($sejourAmbu->_id,$tab_temp)){
      unset($listSejourA[$sejourAmbu->_id]);
    }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date_demain", $date_demain);
$smarty->assign("date_actuelle", $date_actuelle);
$smarty->assign("date"     , $date );
$smarty->assign("now"      , $now );
$smarty->assign("vue"      , $vue );
$smarty->assign("listAmbu"       , $listAmbu );
$smarty->assign("listSejourA" , $listSejourA );
$smarty->assign("date_sortie", $date_sortie);
$smarty->display("inc_vw_sorties_ambu.tpl");

?>