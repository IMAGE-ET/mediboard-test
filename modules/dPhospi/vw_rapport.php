<?php  /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: $
* @author Alexis Granger
*/


global $AppUI, $can, $m, $g;

$date = mbGetValueFromGetOrSession("date");

$med = new CMediusers();
$listPrat = $med->loadPraticiens();

$dateEntree = mbDateTime("23:59:00", $date);
$dateSortie = mbDateTime("00:01:00", $date);

$hierEntree = mbDate("- 1 day", $dateEntree);
$hierEntree = mbDateTime("23:59:00", $hierEntree);

// Chargement des services
$services = new CService;
$services = $services->loadListWithPerms(PERM_READ);

$totalHospi = 0;
$totalAmbulatoire = 0;
$totalMedecin = 0;


// Calcul du nombre d'affectations a la date $date
$affectation = new CAffectation();
$whereAffect["entree"] = "<= '$dateEntree'";
$whereAffect["sortie"] = ">= '$dateSortie'";
$whereAffect["sejour_id"] = "!= '0'";
$groupAffect = "sejour_id";

// Inialisation des tableaux de stockage
$total_service = array();
foreach($services as $key=>$_service){
	$total_service[$_service->nom] = 0;
}
$total_prat = array();
foreach($listPrat as $key=>$prat){
	$totalPrat[$prat->_view]["hospi"] = 0;
	$totalPrat[$prat->_view]["ambu"] = 0;
	$totalPrat[$prat->_view]["total"] = 0;
}

$list_affectations = $affectation->loadList($whereAffect,null,null,$groupAffect);

foreach($list_affectations as $key=>$_affectation){
   // Chargement des références nécessaire pour parcourir les affectations
   $_affectation->loadRefLit();
   $_affectation->_ref_lit->loadRefChambre();
   $_affectation->_ref_lit->_ref_chambre->loadRefsFwd();
   $_affectation->loadRefSejour();
   $_affectation->_ref_sejour->loadRefPraticien();
   
   // Stockage des informations liées aux services
   foreach($services as $key=>$_service){
	 if($_service->_id == $_affectation->_ref_lit->_ref_chambre->_ref_service->_id){
	   $total_service[$_service->nom]++;	
	 }
   }
   
   // Stockage des informations liées au praticiens
   foreach($listPrat as $key=>$_prat){
   	 // Cas d'un sejour de type Ambulatoire
     if($_prat->_id == $_affectation->_ref_sejour->_ref_praticien->_id && $_affectation->_ref_sejour->type == "ambu"){
	   $totalPrat[$_prat->_view]["ambu"]++;	
	   $totalAmbulatoire++;
	 } 
	 // Autres cas
   	 if($_prat->_id == $_affectation->_ref_sejour->_ref_praticien->_id && $_affectation->_ref_sejour->type == "comp"){
	   $totalPrat[$_prat->_view]["hospi"]++;
	   $totalHospi++;
	 }
	 // Total des hospitalisations (Ambu + autres)
	 if($_prat->_id == $_affectation->_ref_sejour->_ref_praticien->_id){
	   $totalPrat[$_prat->_view]["total"] = $totalPrat[$_prat->_view]["ambu"] + $totalPrat[$_prat->_view]["hospi"]; 	
	   $totalMedecin++;
	 }
   } 
}


// present de la veille
$affectationVeille = new CAffectation();
$whereVeille["entree"] = "<= '$hierEntree'";
$whereVeille["sortie"] = ">= '$dateSortie'";
$whereVeille["sejour_id"] = "!= '0'";
$groupVeille = "sejour_id";
$listPresentVeille = $affectationVeille->loadList($whereVeille,null,null,$groupVeille);

// entree du jour
$date_debut = mbDateTime("00:01:00",$date);
$date_fin = mbDateTime("23:59:00",$date);
$affectationEntreeJour = new CAffectation();
$whereEntree["entree"] = "BETWEEN '$date_debut' AND '$date_fin'";
$whereEntree["sejour_id"] = "!= '0'";
$groupEntree = "sejour_id";
$listEntreeJour = $affectationEntreeJour->loadList($whereEntree,null,null,$groupEntree);

// sorties du jour
$affectationSortieJour = new CAffectation();
$whereSortie["sortie"] = "BETWEEN '$date_debut' AND '$date_fin'";
$whereSortie["sejour_id"] = "!= '0'";
$groupSortie = "sejour_id";
$listSortieJour = $affectationSortieJour->loadList($whereSortie,null,null,$groupSortie);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("totalHospi",$totalHospi);
$smarty->assign("totalMedecin",$totalMedecin);
$smarty->assign("totalAmbulatoire",$totalAmbulatoire);
$smarty->assign("date",$date);
$smarty->assign("services", $services);
$smarty->assign("list_affectations",$list_affectations);
$smarty->assign("total_service", $total_service);
$smarty->assign("listPresentVeille", $listPresentVeille);
$smarty->assign("listSortieJour",$listSortieJour);
$smarty->assign("listEntreeJour",$listEntreeJour);
$smarty->assign("listPrat", $listPrat);
$smarty->assign("totalPrat",$totalPrat);
$smarty->display("vw_rapport.tpl");

?>