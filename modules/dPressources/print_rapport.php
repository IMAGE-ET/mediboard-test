<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPressource
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

//Recuperation des identifiants pour les filtres
$filter = new CPlageressource;
$filter->_date_min = CValue::getOrSession("_date_min",mbDate());
$filter->_date_max = CValue::getOrSession("_date_max",mbDate());
$filter->prat_id = CValue::getOrSession("prat_id");
$filter->paye = CValue::getOrSession("type");

$prat_id = CValue::get("prat_id", 0);
if(!$prat_id) {
  echo "Vous devez choisir un praticien valide";
  CApp::rip();
}
if($filter->_date_max > mbDate())
 $filter->_date_max = mbDate();
$filter->paye = CValue::get("type", 0);
$total = 0;

// Chargement du praticien
$prat = new CMediusers;
$prat->load($filter->prat_id);

// Chargement des plages de ressource
$plages = new CPlageressource;

$where["date"]     = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";
$where["prat_id"] = "= $filter->prat_id";
$where["paye"] = "= '$filter->paye'";
$order = "date";

$plages = $plages->loadList($where, $order);

foreach($plages as $key => $value) {
  $total += $value->tarif;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->debugging = false;

$smarty->assign("filter", $filter);
$smarty->assign("prat"  , $prat  );
$smarty->assign("plages", $plages);
$smarty->assign("total" , $total );

$smarty->display("print_rapport.tpl");