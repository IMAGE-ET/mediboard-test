<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

$fileModule     = CModule::getInstalled("dPfiles");
$cptRenduModule = CModule::getInstalled("dPcompteRendu");

$canEditFiles = $fileModule->canEdit();
$canEditDoc   = $cptRenduModule->canEdit();

$selClass = mbGetValueFromGetOrSession("selClass", null);
$selKey   = mbGetValueFromGetOrSession("selKey"  , null);
$typeVue  = mbGetValueFromGetOrSession("typeVue" , 0);
$reloadlist = 1;

// Liste des Class
$listClass = getChildClasses("CMbObject", array("_ref_files"));

$listCategory = CFilesCategory::listCatClass($selClass);

// Création du template
$smarty = new CSmartyDP(1);

$object = null;

if($selClass && $selKey){
  // Chargement de l'objet
  $object = new $selClass;
  $object->load($selKey);
  
  $affichageFile = CFile::loadFilesAndDocsByObject($object);
  
  $smarty->assign("affichageFile",$affichageFile);
}

$canEditFileDoc = $canEditFiles || $canEditDoc;

$smarty->assign("canEditFileDoc" , $canEditFileDoc);
$smarty->assign("canEditFiles"   , $canEditFiles);
$smarty->assign("canEditDoc"     , $canEditDoc);
$smarty->assign("reloadlist"     , $reloadlist  ); 
$smarty->assign("listCategory"   , $listCategory);
$smarty->assign("selClass"       , $selClass    );
$smarty->assign("selKey"         , $selKey      );
$smarty->assign("object"         , $object      );
$smarty->assign("typeVue"        , $typeVue     );
if($typeVue==1){
  $smarty->display("inc_list_view_colonne.tpl");
}elseif($typeVue==2){
  $smarty->display("inc_list_view_gd_thumb.tpl");
}else{
  $smarty->display("inc_list_view.tpl");
}
?>
