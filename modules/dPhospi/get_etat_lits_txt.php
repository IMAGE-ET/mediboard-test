<?php

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Alexis Granger
*/

/*
Pour acceder à cette page ==>
http://localhost/mediboard/index.php?m=dPhospi&a=get_etat_lits_txt&dialog=1&suppressHeaders=1;  
*/

global $AppUI, $can, $m, $g;

// Date actuelle
$date = mbDateTime();

// Chargement des services
$services = new CService;
$services = $services->loadListWithPerms(PERM_READ);

// Affectation a la date $date
$affectation = new CAffectation();
$whereAffect["entree"] = "<= '$date'";
$whereAffect["sortie"] = ">= '$date'";
$whereAffect["sejour_id"] = "!= '0'";
$groupAffect = "sejour_id";

$affectations = $affectation->loadList($whereAffect,null,null,$groupAffect);

$list_affectations = array();

foreach($affectations as $key=>$_affectation){
   $_affectation->loadRefLit();
   $_affectation->_ref_lit->loadRefChambre();
   $_affectation->_ref_lit->_ref_chambre->loadRefsFwd();
   $_affectation->loadRefSejour();
   $_affectation->_ref_sejour->loadRefPraticien();
   $_affectation->_ref_sejour->loadRefPatient();
   
   $list_affectations[$key]["nom"]          = $_affectation->_ref_sejour->_ref_patient->nom;
   $list_affectations[$key]["prenom"]       = $_affectation->_ref_sejour->_ref_patient->prenom;
   $list_affectations[$key]["id"]           = $_affectation->_ref_sejour->_ref_patient->_id;
   $list_affectations[$key]["service"]      = $_affectation->_ref_lit->_ref_chambre->_ref_service->_id;
   $list_affectations[$key]["chambre"]      = $_affectation->_ref_lit->_ref_chambre->_id;
   $list_affectations[$key]["lit"]          = $_affectation->_ref_lit->_id;
   $list_affectations[$key]["sexe"]         = $_affectation->_ref_sejour->_ref_patient->sexe;
   $list_affectations[$key]["naissance"]    = mbTransformTime(null, $_affectation->_ref_sejour->_ref_patient->naissance, "%Y%m%d");
   $list_affectations[$key]["date_entree"]  = mbTransformTime(null, mbDate($_affectation->_ref_sejour->entree_reelle), "%Y%m%d"); 
   $list_affectations[$key]["heure_entree"] = mbTransformTime(null, mbTime($_affectation->_ref_sejour->entree_reelle), "%H%M");
   $list_affectations[$key]["date_sortie"]  = mbTransformTime(null, mbDate($_affectation->_ref_sejour->sortie_reelle), "%Y%m%d");
   $list_affectations[$key]["heure_sortie"] = mbTransformTime(null, mbTime($_affectation->_ref_sejour->sortie_reelle), "%H%M");
   $list_affectations[$key]["type"]         = $_affectation->_ref_sejour->type;
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list_affectations", $list_affectations);

$smarty->display("get_etat_lits_txt.tpl");


?>