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

$moduleFiles = CModule::getInstalled("dPfiles");
$canEditFiles = $moduleFiles->canEdit();

$cat_id   = mbGetValueFromGetOrSession("cat_id"  , 0);
$selClass = mbGetValueFromGetOrSession("selClass", null);
$selKey   = mbGetValueFromGetOrSession("selKey"  , null);
$typeVue  = mbGetValueFromGetOrSession("typeVue" , 0);
$reloadlist = 1;

// Liste des Class
$listClass = getChildClasses("CMbObject", array("_ref_files"));

$listCategory = CFilesCategory::listCatClass($selClass);

if($cat_id != 0){
   $tabCat = array_keys($listCategory);
   $accordion_open = array_search($cat_id , $tabCat);
  if($accordion_open!==""){
    $accordion_open++;
  };
}else{
  $accordion_open = null;
}

// Création du template
$smarty = new CSmartyDP(1);

$object = null;

if($selClass && $selKey){
  $object = new $selClass;
  $object->load($selKey);
  $object->loadRefsFiles();

  // Classement des fichiers
  $affichageFile = array();
  $affichageFile[0] = array();
  $affichageFile[0]["name"] = "Aucune Catégorie";
  $affichageFile[0]["file"] = array();
  foreach($listCategory as $keyCat => $curr_Cat){
    $affichageFile[$keyCat]["name"] = $curr_Cat->nom;
    $affichageFile[$keyCat]["file"] = array();
  }
  foreach($object->_ref_files as $keyFile =>$FileData){
    $affichageFile[$FileData->file_category_id]["file"][]=$FileData;
  }
  $smarty->assign("affichageFile",$affichageFile);
}

$smarty->assign("canEditFiles"   , $canEditFiles);
$smarty->assign("reloadlist"     , $reloadlist  );
$smarty->assign("accordion_open" , $accordion_open);
$smarty->assign("cat_id"         , $cat_id      ); 
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
