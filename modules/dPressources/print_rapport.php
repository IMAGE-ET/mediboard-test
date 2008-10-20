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
$filter->_date_min = mbGetValueFromGetOrSession("_date_min",mbDate());
$filter->_date_max = mbGetValueFromGetOrSession("_date_max",mbDate());
$filter->prat_id = mbGetValueFromGetOrSession("prat_id");
$filter->paye = mbGetValueFromGetOrSession("type");

$prat_id = mbGetValueFromGet("prat_id", 0);
if(!$prat_id) {
  echo "Vous devez choisir un praticien valide";
  CApp::rip();
}
if($filter->_date_max > mbDate())
 $filter->_date_max = mbDate();
$filter->paye = mbGetValueFromGet("type", 0);
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