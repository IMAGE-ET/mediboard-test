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

// Récupération des paramètres
$filter->_date_min = mbGetValueFromGetOrSession("_date_min", mbDate());
$filter->_date_max = mbGetValueFromGetOrSession("_date_max", mbDate());
$filter->_etat_reglement = mbGetValueFromGetOrSession("_etat_reglement");
$filter->_etat_acquittement = mbGetValueFromGetOrSession("_etat_acquittement");

$type = $filter->mode_reglement = mbGetValueFromGetOrSession("mode_reglement", 0);
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

// Récupération des plages de dates de paiement
$sql = "SELECT consultation.date_reglement AS date," .
		"\n plageconsult.chir_id AS chir_id" .
		"\n FROM consultation" .
		"\n LEFT JOIN plageconsult" .
		"\n ON consultation.plageconsult_id = plageconsult.plageconsult_id";
if ($chir)
  $sql .= "\n WHERE chir_id = '$chir'";
else {
  $listPrat = new CMediusers();
  $listPrat = $listPrat->loadPraticiens(PERM_READ);
  $sql .= "\n WHERE chir_id ".$ds->prepareIn(array_keys($listPrat));
}
$sql .= "\n AND date_reglement >= '$filter->_date_min'";
$sql .= "\n AND date_reglement <= '$filter->_date_max'";
$sql .= "\n GROUP BY date_reglement";
$sql .= "\n ORDER BY date_reglement";

$listPlage = $ds->loadlist($sql);

mbTrace($listPlage);
mbTrace("toto");
// On charge les références des consultations qui nous interessent
$total["cheque"]["valeur"] = 0;
$total["CB"]["valeur"] = 0;
$total["especes"]["valeur"] = 0;
$total["tiers"]["valeur"] = 0;
$total["autre"]["valeur"] = 0;
$total["cheque"]["nombre"] = 0;
$total["CB"]["nombre"] = 0;
$total["especes"]["nombre"] = 0;
$total["tiers"]["nombre"] = 0;
$total["autre"]["nombre"] = 0;
$total["secteur1"] = 0;
$total["secteur2"] = 0;
$total["tarif"] = 0;
$total["nombre"] = 0;
foreach($listPlage as $key => $value) {
  $curr_chir = new CMediusers;
  $curr_chir->load($listPlage[$key]["chir_id"]);
  $listPlage[$key]["_ref_chir"] = $curr_chir;
  $where = array();
  $where["chir_id"] = "= '$curr_chir->user_id'";
  $where["date_reglement"] = "= '".$value["date"]."'";
  $where["chrono"] = ">= '".CConsultation::TERMINE."'";
  $where["annule"] = "= '0'";
  
  // Facture réglée par le patient
  if($filter->_etat_reglement == "non_reglee"){
    $where["date_reglement"] = " IS NULL";
  }
  // Facture non réglée par le patient
  if($filter->_etat_reglement == "reglee"){
    $where["date_reglement"] = " IS NOT NULL";
  }
  
  // Facture non acquittee
  if($filter->_etat_acquittement == "non_acquittee"){
    $where["reglement_AM"] = " = '0'";
  }
  // Facture acquittee
  if($filter->_etat_acquittement == "acquittee"){
    $where["reglement_AM"] = " = '1'";
  }
  
  /*if($etat != -1)
    $where["patient_regle"] = "= '$etat'";
  if($etat == 0)
    $where[] = "(secteur1 + secteur2) != 0";
  $where["secteur1"] = "IS NOT NULL";
*/
  
if($type){
  $where["mode_reglement"] = "= '$type'";
}
  $ljoin = array();
  $ljoin["plageconsult"] = "plageconsult.plageconsult_id = consultation.plageconsult_id";
  $listConsult = new CConsultation;
  $listConsult = $listConsult->loadList($where, "heure", null, null, $ljoin);
  $listPlage[$key]["_ref_consultations"] = $listConsult;
  $listPlage[$key]["total1"] = 0;
  $listPlage[$key]["total2"] = 0;
  foreach($listPlage[$key]["_ref_consultations"] as $key2 => $value2) {
    $listPlage[$key]["_ref_consultations"][$key2]->loadRefPatient();
    /*
    if($etat == -1 && $listPlage[$key]["_ref_consultations"][$key2]->date_reglement){
      $listPlage[$key]["total1"] += $value2->secteur1;
      $listPlage[$key]["total2"] += $value2->secteur2;
      $total[$value2->mode_reglement]["valeur"] += $value2->secteur1 + $value2->secteur2;
      $total[$value2->mode_reglement]["nombre"]++;
    }
    */
    //elseif($etat != -1){
      $listPlage[$key]["total1"] += $value2->secteur1;
      $listPlage[$key]["total2"] += $value2->secteur2;
      if($value2->mode_reglement) {
        $total[$value2->mode_reglement]["valeur"] += $value2->secteur1 + $value2->secteur2;
        $total[$value2->mode_reglement]["nombre"]++;
      }
    //}
  }
  $total["secteur1"] += $listPlage[$key]["total1"];
  $total["secteur2"] += $listPlage[$key]["total2"];
  $total["tarif"] += $listPlage[$key]["total1"] + $listPlage[$key]["total2"];
  $total["nombre"] += count($listPlage[$key]["_ref_consultations"]);
  if(!count($listPlage[$key]["_ref_consultations"]))
    unset($listPlage[$key]);
}

mbTrace("toto");
// Création du template
$smarty = new CSmartyDP();

$smarty->debugging = false;
$smarty->assign("filter"   , $filter);
$smarty->assign("aff"      , $aff);
$smarty->assign("_etat_reglement"     , $filter->_etat_reglement);
$smarty->assign("_etat_acquittement"     , $filter->_etat_acquittement);
$smarty->assign("type"     , $type);
$smarty->assign("chirSel"  , $chirSel);
$smarty->assign("listPlage", $listPlage);
$smarty->assign("total"    , $total);

$smarty->display("print_compta.tpl");

?>