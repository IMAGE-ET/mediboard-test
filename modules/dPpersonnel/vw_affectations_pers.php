<?php

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision: 331 $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$can->needsRead();

$affectations = array();

$affectation = new CAffectationPersonnel();
// Récupération de la liste des classes disponibles
$listClasses = getInstalledClasses();


// Chargement de la liste des affectations pour le filtre
$filter = new CAffectationPersonnel();
$filter->object_id    = mbGetValueFromGet("object_id"   );
$filter->object_class = mbGetValueFromGet("object_class");
$filter->user_id = mbGetValueFromGet("user_id");

$filter->nullifyEmptyFields();


// Chargement des 50 dernieres affectations de personnel
$order = "affect_id DESC";
$limit = "0, 50";
$list_affectations = $filter->loadMatchingList($order, $limit);
foreach ($list_affectations as $_affectation) {
  $_affectation->loadRefs();
}

foreach($list_affectations as $key=>$value){
	$med = new CMediusers();
	$affectations[$value->_id]["user"] = $med->load($value->user_id);
    $affectations[$value->_id]["object"] = $value;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("affectation", $affectation);
$smarty->assign("filter", $filter);
$smarty->assign("listClasses",$listClasses);
$smarty->assign("affectations", $affectations);
$smarty->display("vw_affectations_pers.tpl");

?>