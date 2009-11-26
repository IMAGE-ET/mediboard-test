<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $can, $AppUI;  
$can->needsEdit();

// Chargement de l'objet cible
$object_class = CValue::getOrSession("object_class");
$object = new $object_class;
if (!$object instanceof CMbObject) {
	trigger_error("object_class should be an CMbObject", E_USER_WARNING);
	return;
}

$object_id = CValue::getOrSession("object_id");
$object->load($object_id);
if (!$object->_id) {
	trigger_error("object of class '$object_class' could not be loaded with id '$object_id'", E_USER_WARNING);
	return;
}

$object->loadRefsDocs();
foreach($object->_ref_documents as $_document){
  $_document->loadRefCategory();
}

// Praticien concerné
if ($AppUI->_ref_user->isPraticien()) {
  $praticien = $AppUI->_ref_user;
}
else {
	$praticien = new CMediusers();
	$praticien->load(CValue::getOrSession("praticien_id"));
}

$praticien->loadRefFunction();
$praticien->_ref_function->loadRefGroup();
$praticien->canDo();

// Modèles du praticien
$modelesByOwner = array();
$packsByOwner = array();
if ($praticien->_can->edit) {
  $modelesByOwner = CCompteRendu::loadAllModelesFor($praticien->_id, 'prat', $object_class, "body");
  $packsByOwner = CPack::loadAllPacksFor($praticien->_id, 'user', $object_class);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("modelesByOwner", $modelesByOwner);
$smarty->assign("packsByOwner"  , $packsByOwner);
$smarty->assign("praticien"     , $praticien);
$smarty->assign("object"        , $object);
$smarty->assign("mode"          , CValue::get("mode"));
$smarty->assign("notext"        , "notext");
$smarty->display("inc_widget_documents.tpl");

?>