<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsEdit();

$target_id = mbGetValueFromGet("target_id");
$target_class = mbGetValueFromGet("target_class");

$compte_rendu_id = mbGetValueFromGet("compte_rendu_id"   , 0);
$modele_id       = mbGetValueFromGet("modele_id"         , 0);
$praticien_id    = mbGetValueFromGet("praticien_id"      , 0);
$type            = mbGetValueFromGet("type"              , 0);
$pack_id         = mbGetValueFromGet("pack_id"           , 0);
$object_id       = mbGetValueFromGet("object_id"         , 0);

// Faire ici le test des différentes variables dont on a besoin

$compte_rendu = new CCompteRendu;
// Modification d'un document
if($compte_rendu_id) {
  $compte_rendu->load($compte_rendu_id);
} 

// Création à partir d'un modèle
else {
  $compte_rendu->load($modele_id);
  $compte_rendu->_id = null;
  $compte_rendu->chir_id = $praticien_id;
  $compte_rendu->function_id = null;
  $compte_rendu->object_id = $object_id;
  
  // Utilisation des headers/footers
  $compte_rendu->loadComponents();
  if ($compte_rendu->header_id) {
    $compte_rendu->source = $compte_rendu->_ref_header->source . "<hr />" . $compte_rendu->source;
  }
  
  if ($compte_rendu->footer_id) {
    $compte_rendu->source = $compte_rendu->source . "<hr />" . $compte_rendu->_ref_footer->source;
  }
  
  // On fournit la cible
  if ($target_id && $target_class){
    $compte_rendu->object_id = $target_id;
    $compte_rendu->object_class = $target_class;
  }
  
  // A partir d'un pack
  if ($pack_id) {
    $pack = new CPack;
    $pack->load($pack_id);
    $compte_rendu->nom = $pack->nom;
    $compte_rendu->object_class = $pack->object_class;
    //$compte_rendu->object_class = $pack->_object_class;
    $compte_rendu->source = $pack->_source;
  }
  $compte_rendu->updateFormFields();
}

$compte_rendu->loadRefsFwd();
$compte_rendu->_ref_object->loadRefsFwd();
$object =& $compte_rendu->_ref_object;

$medichir = new CMediusers;
if($compte_rendu->_ref_object->_class_name == "CConsultAnesth"){
  $praticien_id = $compte_rendu->_ref_object->_ref_consultation->_praticien_id;
} else {
  $praticien_id = $compte_rendu->_ref_object->_praticien_id;
}
$medichir->load($praticien_id);
//$medichir->load($compte_rendu->_ref_chir->user_id);

//Chargement des catégories
$listCategory = CFilesCategory::listCatClass($compte_rendu->object_class);

// Gestion du template
$templateManager = new CTemplateManager;

$object->fillTemplate($templateManager);
$templateManager->document = $compte_rendu->source;
$templateManager->loadHelpers($medichir->user_id, $compte_rendu->object_class);
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

$smarty = new CSmartyDP();

$smarty->assign("listCategory"   , $listCategory);
$smarty->assign("templateManager", $templateManager);
$smarty->assign("compte_rendu"   , $compte_rendu);
$smarty->assign("lists"          , $lists);

$smarty->display("edit_compte_rendu.tpl");

?>