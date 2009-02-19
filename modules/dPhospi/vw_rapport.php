<?php  /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: $
* @author Alexis Granger
*/


global $AppUI, $can, $m, $g;

$date = mbGetValueFromGetOrSession("date");

$reel = mbGetValueFromGetOrSession("rapport_reel", 1);
if($reel) {
  $champ_entree = "entree_reelle";
  $champ_sortie = "sortie_reelle";
} else {
  $champ_entree = "entree_prevue";
  $champ_sortie = "sortie_prevue";
}

// Chargement des praticiens
$med = new CMediusers();
$listPrat = $med->loadPraticiens(PERM_READ);

$dateEntree = mbDateTime("23:59:00", $date);
$dateSortie = mbDateTime("00:01:00", $date);

$hierEntree = mbDate("- 1 day", $dateEntree);
$hierEntree = mbDateTime("23:59:00", $hierEntree);

// Chargement des services
$services = new CService;
$services = $services->loadListWithPerms(PERM_READ, null, "nom");

// Initialisations
$totalHospi = 0;
$totalAmbulatoire = 0;
$totalMedecin = 0;
$total_prat = array();
foreach($listPrat as $key => $prat){
  $totalPrat[$prat->_id]["prat"]  = $prat;
	$totalPrat[$prat->_id]["hospi"] = 0;
	$totalPrat[$prat->_id]["ambu"]  = 0;
	$totalPrat[$prat->_id]["total"] = 0;
}

$sejour = new CSejour;
$whereSejour = array();
$whereSejour[$champ_entree] = "<= '$dateEntree'";
if($reel){
  $whereSejour[] = "(sortie_reelle >= '$dateSortie') OR (sortie_reelle IS NULL && sortie_prevue >= '$dateSortie')";
} else {
  $whereSejour[$champ_sortie] = ">= '$dateSortie'";
}
$whereSejour["annule"]        = "= '0'";
$listSejours = $sejour->loadList($whereSejour);
   
// Stockage des informations liées au praticiens
foreach($listSejours as $_sejour) {
  $_sejour->loadRefPraticien(1);
    foreach($listPrat as $key=>$_prat){
      // Cas d'un sejour de type Ambulatoire
      if($_prat->_id == $_sejour->_ref_praticien->_id && $_sejour->type == "ambu"){
        $totalPrat[$_prat->_id]["ambu"]++;    
      $totalAmbulatoire++;
    } 
    // Autres cas
    if($_prat->_id == $_sejour->_ref_praticien->_id && $_sejour->type == "comp"){
      $totalPrat[$_prat->_id]["hospi"]++;
      $totalHospi++;
    }
    // Total des hospitalisations (Ambu + autres)
    if($_prat->_id == $_sejour->_ref_praticien->_id){
      $totalPrat[$_prat->_id]["total"] = $totalPrat[$_prat->_id]["ambu"] + $totalPrat[$_prat->_id]["hospi"];     
      $totalMedecin++;
    }
  }
} 

// Calcul des patients par service

// Calcul du nombre d'affectations a la date $date
$affectation = new CAffectation();
$whereAffect["entree"] = "<= '$dateEntree'";
$whereAffect["sortie"] = ">= '$dateSortie'";
$whereAffect["sejour_id"] = "!= '0'";
$groupAffect = "sejour_id";

$total_service = array();
foreach($services as $key=>$_service){
	$total_service[$_service->_id]["service"] = $_service;
	$total_service[$_service->_id]["total"]   = 0;
}

$list_affectations = $affectation->loadList($whereAffect,null,null,$groupAffect);


foreach($list_affectations as $key=>$_affectation){
  // Chargement des références nécessaire pour parcourir les affectations
  $_affectation->loadRefLit();
  $_affectation->_ref_lit->loadRefChambre();
  $_affectation->_ref_lit->_ref_chambre->loadRefsFwd();
  $_affectation->loadRefSejour();
  $_affectation->_ref_sejour->loadRefPraticien(1);

  // Stockage des informations liées aux services
  foreach($services as $key=>$_service){
    if($_service->_id == $_affectation->_ref_lit->_ref_chambre->_ref_service->_id && $_affectation->_ref_sejour->annule == 0){
      $total_service[$_service->_id]["total"]++;    
    }
  }
}

$date_debut = mbDateTime("00:01:00",$date);
$date_fin = mbDateTime("23:59:00",$date);

// present du jour
$sejourJour = new CSejour();
$whereJour = array();
$whereJour[$champ_entree] = "<= '$date_fin'";
$whereJour[$champ_sortie] = ">= '$date_debut'";
$whereJour["annule"] = "= '0'";
$whereJour["type"] = "= 'comp'";
$countPresentJour = $sejourJour->countList($whereJour);

// present de la veille
$sejourVeille = new CSejour();
$whereVeille = array();
$whereVeille[$champ_entree] = "<= '$hierEntree'";
$whereVeille[$champ_sortie] = ">= '$dateSortie'";
$whereVeille["annule"] = "= '0'";
$whereVeille["type"] = "= 'comp'";
$countPresentVeille = $sejourVeille->countList($whereVeille);

// entree du jour
$sejourEntreeJour = new CSejour();
$whereEntree = array();
$whereEntree[$champ_entree] = "BETWEEN '$date_debut' AND '$date_fin'";
$whereEntree["annule"] = "= '0'";
$whereEntree["type"] = "= 'comp'";
$countEntreeJour = $sejourEntreeJour->countList($whereEntree);

// sorties du jour
$sejourSortieJour = new CSejour();
$whereSortie = array();
$whereSortie[$champ_sortie] = "BETWEEN '$date_debut' AND '$date_fin'";
$whereSortie["annule"] = "= '0'";
$whereSortie["type"] = "= 'comp'";
$countSortieJour = $sejourSortieJour->countList($whereSortie);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date",$date);
$smarty->assign("reel",$reel);
$smarty->assign("totalHospi",$totalHospi);
$smarty->assign("totalMedecin",$totalMedecin);
$smarty->assign("totalAmbulatoire",$totalAmbulatoire);
$smarty->assign("services", $services);
$smarty->assign("list_affectations",$list_affectations);
$smarty->assign("total_service", $total_service);
$smarty->assign("countPresentVeille", $countPresentVeille);
$smarty->assign("countSortieJour",$countSortieJour);
$smarty->assign("countEntreeJour",$countEntreeJour);
$smarty->assign("countPresentJour", $countPresentJour);
$smarty->assign("listPrat", $listPrat);
$smarty->assign("totalPrat",$totalPrat);
$smarty->display("vw_rapport.tpl");

?>