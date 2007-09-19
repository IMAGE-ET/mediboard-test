<?php

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision: 331 $
* @author Alexis Granger
*/

global $can;

$can->needsRead();

// Récupération de la liste des classes disponibles
$classes = getInstalledClasses();

// Chargement de l'affectation sélectionnée
$affectation = new CAffectationPersonnel();
$affectation->load(mbGetValueFromGetOrSession("affect_id"));
mbTrace($affectation->getProps());

// Liste des classes disponibles
$classes = getInstalledClasses();

// Liste du personnel existant
$mediuser = new CMediusers();
$personnels = $mediuser->loadListFromType(array("Personnel"));

// Chargement de la liste des affectations pour le filtre
$filter = new CAffectationPersonnel();
$filter->object_id    = mbGetValueFromGetOrSession("object_id"   );
$filter->object_class = mbGetValueFromGetOrSession("object_class");
$filter->user_id = mbGetValueFromGet("user_id");
$filter->nullifyEmptyFields();

// Chargement des 50 dernieres affectations de personnel
$order = "affect_id DESC";
$limit = "0, 50";
$affectations = $filter->loadMatchingList($order, $limit);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("affectations", $affectations);
$smarty->assign("affectation", $affectation);
$smarty->assign("personnels", $personnels);
$smarty->assign("filter", $filter);
$smarty->assign("classes",$classes);
$smarty->display("vw_affectations_pers.tpl");
?>