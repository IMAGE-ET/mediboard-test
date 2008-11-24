<?php /* $Id: httpreq_vw_fdr_consult.php 3403 2008-02-11 12:43:48Z alexis_granger $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 3403 $
* @author Romain Ollivier
*/

global $can, $AppUI;  
$can->needsEdit();

// Chargement de l'objet cible
$object_class = mbGetValueFromGetOrSession("object_class");
$object = new $object_class;
if (!$object instanceof CMbObject) {
	trigger_error("object_class should be an CMbObject", E_USER_WARNING);
	return;
}

$object_id = mbGetValueFromGetOrSession("object_id");
$object->load($object_id);
if (!$object->_id) {
	trigger_error("object of class '$object_class' could not be loaded with id '$object_id'", E_USER_WARNING);
	return;
}

$object->loadRefsDocs();

// Praticien concerné
if ($AppUI->_ref_user->isPraticien()) {
  $praticien = $AppUI->_ref_user;
  $praticiens = null;
}
else {
	$praticien = new CMediusers();
	$praticien->load(mbGetValueFromGetOrSession("praticien_id"));
}

$praticien->loadRefFunction();
$praticien->_ref_function->loadRefGroup();
$praticien->canDo();

// Modèles du praticien
$modelesByOwner = array();
$packs = array();
if ($praticien->_can->edit) {
  $modelesByOwner = CCompteRendu::loadAllModelesFor($praticien->_id, 'prat', $object_class, "body");
  
  // Chargement des packs
  $pack = new CPack();
  $pack->object_class = $object_class;
  $pack->chir_id = $praticien->_id;
  $packs = $pack->loadMatchingList("nom");
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("modelesByOwner", $modelesByOwner);
$smarty->assign("packs"         , $packs);
$smarty->assign("praticien"     , $praticien);
$smarty->assign("object"        , $object);
$smarty->assign("mode"          , mbGetValueFromGet("mode"));

$smarty->display("inc_widget_documents.tpl");

?>