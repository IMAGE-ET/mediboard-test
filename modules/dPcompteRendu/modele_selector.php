<?php /* */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:  $
* @author Alexis Granger
* @author Fabien Menager
**/

global $AppUI;

$object_id    = mbGetValueFromGetOrSession("object_id");
$object_class = mbGetValueFromGetOrSession("object_class");
$praticien_id = mbGetValueFromGetOrSession("praticien_id");

$user_id      = $AppUI->user_id;

// Chargement de l'objet
$object = new $object_class;
$object->load($object_id);

// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($praticien_id);

// Chargement des objets relatifs a l'objet chargé
$templateClasses = $object->getTemplateClasses();

// Chargement des modeles de consultations du praticien
$order = "nom";

$wherePrat = array();
$whereFunc = array();
// Chargement des modeles pour chaque classe, pour les praticiens et leur fonction
foreach($templateClasses as $class => $id) {
  // Pour le praticien
  $wherePrat["chir_id"] = "= '$praticien->_id'";
  $wherePrat["object_class"] = "= '$class'";
	$modeles[$class]['prat'] = CCompteRendu::loadModeleByCat(null, $wherePrat, $order, true);

	// Pour la fonction du praticien
  $whereFunc["function_id"] = "= '$praticien->function_id'";
	$whereFunc["object_class"] = "= '$class'";
	$modeles[$class]['func'] = CCompteRendu::loadModeleByCat(null, $whereFunc, $order, true);
}


  // Création du template
$smarty = new CSmartyDP();

$smarty->assign("target_id", $object->_id);
$smarty->assign("target_class", $object->_class_name);
$smarty->assign("templateClasses", $templateClasses);
$smarty->assign("modeles", $modeles);

$smarty->display("modele_selector.tpl");

?>