<?php /* */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Alexis Granger
* @author Fabien Menager
**/

global $AppUI;

// Chargement de l'objet
$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");
$object = new $object_class;
$object->load($object_id);

// Chargement du praticien concerné et des praticiens disponibles
if ($AppUI->_ref_user->isPraticien()) {
  $praticien = $AppUI->_ref_user;
  $praticiens = null;
}
else {
	$praticien = new CMediusers();
	$praticien->load(CValue::getOrSession("praticien_id"));
	$praticiens = $praticien->loadPraticiens(PERM_EDIT);
}

$praticien->canDo();


// Chargement des objets relatifs a l'objet chargé
$templateClasses = $object->getTemplateClasses();

// Chargement des modeles de consultations du praticien
$order = "nom";

$wherePrat = array();
$whereFunc = array();

$modelesCompat = array();
$modelesNonCompat = array();

// Chargement des modeles pour chaque classe, pour les praticiens et leur fonction
foreach($templateClasses as $class => $id) {
  $modeles = CCompteRendu::loadAllModelesFor($praticien->_id, 'prat', $class);
  if ($id) {
    $modelesCompat[$class] = $modeles;
  } 
  else {
    $modelesNonCompat[$class] = $modeles;
  }
}


  // Création du template
$smarty = new CSmartyDP();

$smarty->assign("praticien", $praticien);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("target_id", $object->_id);
$smarty->assign("target_class", $object->_class_name);

$smarty->assign("modelesCompat", $modelesCompat);
$smarty->assign("modelesNonCompat", $modelesNonCompat);
$smarty->assign("modelesId", $templateClasses);

$smarty->display("modele_selector.tpl");

?>