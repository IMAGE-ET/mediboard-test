<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

// !! Attention, régression importante si ajout de type de paiement

global $AppUI, $can, $m;
$ds = CSQLDataSource::get("std");
$today = mbDate();
// Récupération des paramètres
$filter->_date_min = mbGetValueFromGetOrSession("_date_min", mbDate());
$filter->_date_max =mbGetValueFromGetOrSession("_date_max", mbDate());
$filter->_etat_paiement = mbGetValueFromGetOrSession("_etat_paiement", 0);
//Traduction pour le passage d'un enum en bool pour les requetes sur la base de donnee
if($filter->_etat_paiement == null) {
	$etat = -1;
} elseif ($filter->_etat_paiement == "impaye") {
	$etat = 0;
} elseif ($filter->_etat_paiement == "paye") {
	$etat = 1;
}
$type = $filter->type_tarif = mbGetValueFromGetOrSession("type_tarif", 0);
if($type == null) {
	$type = 0;
}
$aff = $filter->_type_affichage  = mbGetValueFromGetOrSession("_type_affichage" , 1);
//Traduction pour le passage d'un enum en bool pour les requetes sur la base de donnee
if($aff == "complete") {
	$aff = 1;
} elseif ($aff == "totaux"){
	$aff = 0;
}

$chir = mbGetValueFromGetOrSession("chir");
$chirSel = new CMediusers;
$chirSel->load($chir);


// Requète sur les plages de consultation considérées
$where = array();
$where[] = "date >= '$filter->_date_min'";
$where[] = "date <= '$filter->_date_max'";

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);
$where["chir_id"] = $ds->prepareIn(array_keys($listPrat), $chir);

$listPlage = new CPlageconsult;
$listPlage = $listPlage->loadList($where, "date, chir_id");

// On charge les références des consultations qui nous interessent
$total["cheque"]["valeur"]  = 0;
$total["CB"]["valeur"]      = 0;
$total["especes"]["valeur"] = 0;
$total["tiers"]["valeur"]   = 0;
$total["autre"]["valeur"]   = 0;
$total["cheque"]["nombre"]  = 0;
$total["CB"]["nombre"]      = 0;
$total["especes"]["nombre"] = 0;
$total["tiers"]["nombre"]   = 0;
$total["autre"]["nombre"]   = 0;
$total["secteur1"]          = 0;
$total["secteur2"]          = 0;
$total["tarif"]             = 0;
$total["nombre"]            = 0;
foreach($listPlage as $key => $value) {
  $listPlage[$key]->loadRefsFwd();
  //unset($listPlage[$key]->_ref_consultations);
  $where = array();
  $where["plageconsult_id"] = "= '".$value->plageconsult_id."'";
  $where["chrono"] = ">= '".CConsultation::TERMINE."'";
  $where["annule"] = "= '0'";
  $where[] = "tarif IS NOT NULL AND tarif <> ''";
  if($etat != -1)
    $where["paye"] = "= '$etat'";
  if($etat == 0)
    $where[] = "(secteur1 + secteur2) != 0";
  $where["secteur1"] = "IS NOT NULL";
  if($type)
    $where["type_tarif"] = "= '$type'";
  $listConsult = new CConsultation;
  $listConsult = $listConsult->loadList($where, "heure");
  $listPlage[$key]->_ref_consultations = $listConsult;
  $listPlage[$key]->total1 = 0;
  $listPlage[$key]->total2 = 0;
  foreach($listPlage[$key]->_ref_consultations as $key2 => $value2) {
    $listPlage[$key]->_ref_consultations[$key2]->loadRefPatient();
    if($etat == -1 && $listPlage[$key]->_ref_consultations[$key2]->paye){
      $listPlage[$key]->total1 += $value2->secteur1;
      $listPlage[$key]->total2 += $value2->secteur2;
      if(isset($total[$value2->type_tarif]["valeur"]))
        $total[$value2->type_tarif]["valeur"] += $value2->secteur1 + $value2->secteur2;
      else
        $total[$value2->type_tarif]["valeur"] = $value2->secteur1 + $value2->secteur2;
      if(isset($total[$value2->type_tarif]["nombre"]))
        $total[$value2->type_tarif]["nombre"]++;
      else
        $total[$value2->type_tarif]["nombre"] = 1;
    }
    elseif($etat != -1){
      $listPlage[$key]->total1 += $value2->secteur1;
      $listPlage[$key]->total2 += $value2->secteur2;
      if($value2->type_tarif) {
        if(isset($total[$value2->type_tarif]["valeur"]))
          $total[$value2->type_tarif]["valeur"] += $value2->secteur1 + $value2->secteur2;
        else
          $total[$value2->type_tarif]["valeur"] += $value2->secteur1 + $value2->secteur2;
        if(isset($total[$value2->type_tarif]["nombre"]))
          $total[$value2->type_tarif]["nombre"]++;
        else
          $total[$value2->type_tarif]["nombre"] = 1;
      }
    }
  }
  $total["secteur1"] += $listPlage[$key]->total1;
  $total["secteur2"] += $listPlage[$key]->total2;
  $total["tarif"]    += $listPlage[$key]->total1 + $listPlage[$key]->total2;
  $total["nombre"]   += count($listPlage[$key]->_ref_consultations);
  if(!count($listPlage[$key]->_ref_consultations))
    unset($listPlage[$key]);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("today"    , $today);
$smarty->assign("filter"   , $filter);
$smarty->assign("aff"      , $aff);
$smarty->assign("etat"     , $etat);
$smarty->assign("type"     , $type);
$smarty->assign("chirSel"  , $chirSel);
$smarty->assign("listPlage", $listPlage);
$smarty->assign("total"    , $total);

$smarty->display("print_rapport.tpl");

?>