<?php /* $Id: print_bilan_fse.php 6134 2009-04-21 10:40:31Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 6134 $
* @author Thomas Despoix 
*/

global $can;

$can->needsEdit();

$filter = new CLmLot();
$filter->_date_min = CValue::getOrSession("_date_min", mbDate());
$filter->_date_max = CValue::getOrSession("_date_max", mbDate("+1 day"));
$filter->S_LOT_CPS  = CValue::getOrSession("S_LOT_CPS", -1);
$filter->S_LOT_ETAT = CValue::getOrSession("S_LOT_ETAT");

// Chargement des lots
$where = array();
$where["S_LOT_DATE"] = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";

if ($filter->S_LOT_CPS) {
  $where["S_LOT_CPS"] = "= '$filter->S_LOT_CPS'";
}

if ($filter->S_LOT_ETAT) {
  $where["S_LOT_ETAT"] = "= '$filter->S_LOT_ETAT'";
}

$lot = new CLmLOT();
$lots = $lot->loadList($where);

// Chargement des fichiers
$where = array();
$fichier_ids = array_unique(CMbArray::pluck($lots, "S_LOT_FIC"));
$where["S_FIC_NUMERO"] = CSQLDataSource::prepareIn($fichier_ids);
$fichier = new CLmFichier();
$fichiers = $fichier->loadList($where);

// Rangement des lots dans les fichiers par date
$days = array();
foreach ($lots as &$_lot) {
	$fichiers[$_lot->S_LOT_FIC]->_back["lots"][$_lot->_id] = $_lot;
  $_lot->_fwd["S_LOT_FIC"] = $fichiers[$_lot->S_LOT_FIC];
	if ($_lot->S_LOT_ETAT == "7" || $_lot->S_LOT_ETAT == "13") {
		$fichiers[$_lot->S_LOT_FIC]->_resend_fixable = "1";
	}
	
  $days[$fichiers[$_lot->S_LOT_FIC]->S_FIC_DATE][$_lot->S_LOT_FIC] = $fichiers[$_lot->S_LOT_FIC];
}

ksort($days);

// Chargement du praticien 
$prat = new CMediusers();
$prat->loadFromIdCPS($filter->S_LOT_CPS);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("lot", $lot);
$smarty->assign("fichier", $fichier);
$smarty->assign("days", $days);
$smarty->assign("lots", $lots);
$smarty->assign("fichiers", $fichiers);
$smarty->assign("prat", $prat);
$smarty->display("print_bilan_lot.tpl");

?>