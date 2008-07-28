<?php /* $Id: httpreq_vw_fdr_consult.php 3403 2008-02-11 12:43:48Z alexis_granger $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 3403 $
* @author Romain Ollivier
*/

global $can;
  
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

// Modèles de l'utilisateur
$praticien = new CMediusers();
$praticien->load(mbGetValueFromGetOrSession("praticien_id"));
$modelesByOwner = array();
$packs = array();
if ($praticien->user_id) {
  $modelesByOwner = CCompteRendu::loadAllModelesForPrat($praticien->_id, $object_class, "body");
  
  // Chargement des packs
  $pack = new CPack();
  $pack->object_class = $object_class;
  $pack->chir_id = $praticien->_id;
  $packs = $pack->loadMatchingList("nom");
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("modelesByOwner", $modelesByOwner);
$smarty->assign("packs", $packs);
$smarty->assign("praticien_id"  , $praticien->_id);
$smarty->assign("object"        , $object);
$smarty->assign("mode"         , mbGetValueFromGet("mode"));

$smarty->display("inc_widget_documents.tpl");

?>