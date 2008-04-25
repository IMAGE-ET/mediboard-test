<?php

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision:
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;
require_once($AppUI->getModuleFile("dPhospi", "inc_vw_affectations"));

$can->needsRead();

$date = mbGetValueFromGetOrSession("date", mbDate()); 
$mode = mbGetValueFromGetOrSession("mode", 0);
$service_id   = mbGetValueFromGetOrSession("service_id");
$praticien_id = mbGetValueFromGetOrSession("praticien_id");
$sejour_id = mbGetValueFromGetOrSession("sejour_id",0);
// R�cup�ration du service � ajouter/�diter
$totalLits = 0;
// A passer en variable de configuration
$heureLimit = "16:00:00";

// R�cuperation du sejour s�lectionn�
$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefs();
$sejour->loadRefsPrescriptions();

// R�cup�ration de la liste des services
$where = array();
$where["group_id"] = "= '$g'";
$services = new CService;
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ, $where, $order);


// Chargement des s�jours � afficher
$service = new CService;
$groupSejourNonAffectes = array();
if($service_id == "NP") {
  
	// Liste des patients � placer

  $order = "entree_prevue ASC";
	  
  // Admissions de la veille
  $dayBefore = mbDate("-1 days", $date);
  $where = array(
	  "entree_prevue" => "BETWEEN '$dayBefore 00:00:00' AND '$date 00:00:00'",
	  "type" => "!= 'exte'",
	  "annule" => "= '0'"
	);
	  
	$groupSejourNonAffectes["veille"] = loadSejourNonAffectes($where, $order);
	  
	// Admissions du matin
	$where = array(
	  "entree_prevue" => "BETWEEN '$date 00:00:00' AND '$date ".mbTime("-1 second",$heureLimit)."'",
	  "type" => "!= 'exte'",
	  "annule" => "= '0'"
	);
	  
	$groupSejourNonAffectes["matin"] = loadSejourNonAffectes($where, $order);
	  
	// Admissions du soir
	$where = array(
	  "entree_prevue" => "BETWEEN '$date $heureLimit' AND '$date 23:59:59'",
	  "type" => "!= 'exte'",
	  "annule" => "= '0'"
	);
	  
	$groupSejourNonAffectes["soir"] = loadSejourNonAffectes($where, $order);
	  
	// Admissions ant�rieures
	$twoDaysBefore = mbDate("-2 days", $date);
	$where = array(
	  "entree_prevue" => "<= '$twoDaysBefore 23:59:59'",
	  "sortie_prevue" => ">= '$date 00:00:00'",
	  //"'$twoDaysBefore' BETWEEN entree_prevue AND sortie_prevue",
	  "annule" => "= '0'",
	  "type" => "!= 'exte'"
  );
	  
	$groupSejourNonAffectes["avant"] = loadSejourNonAffectes($where, $order);
  
} else {
  $service->load($service_id);
  loadServiceComplet($service, $date, $mode);
}

if($service->_id){
	foreach($service->_ref_chambres as &$_chambre){
		foreach($_chambre->_ref_lits as &$_lits){
			foreach($_lits->_ref_affectations as &$_affectation){
				$_affectation->_ref_sejour->loadRefsPrescriptions();
			}
		}
	}
}

              
// Chargement des documents du sejour
$sejour->loadRefsDocs();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("object"                 , $sejour);
$smarty->assign("mode"                   , $mode);
$smarty->assign("totalLits"              , $totalLits);
$smarty->assign("date"                   , $date);
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("isImedsInstalled"       , CModule::getActive("dPImeds"));
$smarty->assign("demain"                 , mbDate("+ 1 day", $date));
$smarty->assign("services"               , $services);
$smarty->assign("service"                , $service);
$smarty->assign("service_id"             , $service_id);
$smarty->assign("groupSejourNonAffectes" , $groupSejourNonAffectes);
$smarty->display("vw_idx_sejour.tpl");


?>