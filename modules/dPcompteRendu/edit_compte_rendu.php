<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("dPcompteRendu", "compteRendu"));
require_once($AppUI->getModuleClass("dPcompteRendu", "pack"));
require_once($AppUI->getModuleClass("dPcompteRendu", "listeChoix"));
require_once($AppUI->getModuleClass("dPcompteRendu", "templatemanager"));
require_once($AppUI->getModuleClass("dPcompteRendu", "aidesaisie"));

if(!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$compte_rendu_id = mbGetValueFromGet("compte_rendu_id"   , 0);
$modele_id       = mbGetValueFromGet("modele_id"         , 0);
$praticien_id    = mbGetValueFromGet("praticien_id"      , 0);
$type            = mbGetValueFromGet("type"              , 0);
$pack_id         = mbGetValueFromGet("pack_id"           , 0);
$object_id       = mbGetValueFromGet("object_id"         , 0);

// Faire ici le test des différentes variables dont on a besoin

$compte_rendu = new CCompteRendu;
if($compte_rendu_id) {
  $compte_rendu->load($compte_rendu_id);
} else {
  $compte_rendu->load($modele_id);
  $compte_rendu->compte_rendu_id = null;
  $compte_rendu->chir_id = $praticien_id;
  $compte_rendu->function_id = null;
  $compte_rendu->object_id = $object_id;
  if($pack_id) {
    $pack = new CPack;
    $pack->load($pack_id);
    $compte_rendu->nom = $pack->nom;
    $compte_rendu->type = $pack->_type;
    $compte_rendu->source = $pack->_source;
  }
  $compte_rendu->updateFormFields();
}
$compte_rendu->loadRefsFwd();
$compte_rendu->_ref_object->loadRefsFwd();
$object =& $compte_rendu->_ref_object;

$medichir = new CMediusers;
$medichir->load($compte_rendu->chir_id);

// Gestion du template
$templateManager = new CTemplateManager;

$object->fillTemplate($templateManager);

$templateManager->document = $compte_rendu->source;
$templateManager->loadHelpers($medichir->user_id, $compte_rendu->type);
$templateManager->loadLists($medichir->user_id);
$templateManager->applyTemplate($compte_rendu);

$where = array();
$where[] = "(chir_id = '$medichir->user_id' OR function_id = '$medichir->function_id')";
$order = "chir_id, function_id";
$chirLists = new CListeChoix;
$chirLists = $chirLists->loadList($where, $order);
$lists = $templateManager->getUsedLists($chirLists);

$templateManager->initHTMLArea();

// Création du template
require_once($AppUI->getSystemClass("smartydp"));

$smarty = new CSmartyDP(1);

$smarty->assign("templateManager", $templateManager);
$smarty->assign("compte_rendu"   , $compte_rendu);
$smarty->assign("lists"          , $lists);

$smarty->display("edit_compte_rendu.tpl");

?>