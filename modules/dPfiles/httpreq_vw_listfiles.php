<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

//$can->needsRead();
$ds = CSQLDataSource::get("std");
$selClass      = mbGetValueFromGetOrSession("selClass", null);
$selKey        = mbGetValueFromGetOrSession("selKey"  , null);
$typeVue       = mbGetValueFromGetOrSession("typeVue" , 0);
$accordDossier = mbGetValueFromGet("accordDossier"    , 0);
$reloadlist = 1;

// Liste des Class
$listClass = getChildClasses("CMbObject", array("_ref_files"));
$listCategory = CFilesCategory::listCatClass($selClass);


// Id de l'utilisateur courant
$user_id = $AppUI->user_id;

// Chargement de l'utilisateur courant
$userSel = new CMediusers;
$userSel->load($user_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

$etablissements = CMediusers::loadEtablissements(PERM_EDIT);

// Récupération des modèles
$whereCommon = array();
$whereCommon["object_id"] = "IS NULL";
$whereCommon[] = "`object_class` = '$selClass'";

$order = "nom";

// Création du template
$smarty = new CSmartyDP();

$object = null;

$canFile  = new CCanDo;
$praticienId = null;

if($selClass && $selKey){
  // Chargement de l'objet
  $object = new $selClass;
  $object->load($selKey);
  $canFile = $object->canDo();
  
  // To add the modele selector in the toolbar
  $object->updateFormFields();
  if ($selClass == 'CConsultation') {
    $praticienId = $object->_praticien_id;
  } 
  else if ($selClass == 'CConsultAnesth') {
    $praticienId = $object->_ref_consultation->_praticien_id;
  }
  else if ($selClass == 'CSejour') {
    $praticienId = $object->praticien_id;
  }
  else if ($selClass == 'COperation') {
    $praticienId = $object->chir_id;
  }
  else if ($userSel->isPraticien()) {
    $praticienId = $userSel->_id;
  }
  /////
  
  $affichageFile = CFile::loadDocItemsByObject($object);
  
  $smarty->assign("affichageFile",$affichageFile);
}

$smarty->assign("canFile"        , $canFile);

$smarty->assign("reloadlist"     , $reloadlist  ); 
$smarty->assign("listCategory"   , $listCategory);
$smarty->assign("praticienId"    , $praticienId );
$smarty->assign("selClass"       , $selClass    );
$smarty->assign("selKey"         , $selKey      );
$smarty->assign("object"         , $object      );
$smarty->assign("typeVue"        , $typeVue     );
$smarty->assign("accordDossier"  , $accordDossier);

switch($typeVue) {
  case 0 :
    $smarty->display("inc_list_view.tpl");
    break;
  case 1 :
    $smarty->display("inc_list_view_colonne.tpl");
    break;
}


?>
