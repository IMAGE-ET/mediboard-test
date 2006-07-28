<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("dPfiles", "filescategory"));
require_once($AppUI->getModuleClass("dPfiles", "files"        ));

if (!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$selClass = mbGetValueFromGetOrSession("selClass", null);
$selKey   = mbGetValueFromGetOrSession("selKey"  , null);
$typeVue  = mbGetValueFromGetOrSession("typeVue" , 0);

// Liste des Class
$listClass = getChildClasses("CMbObject", array("_ref_files"));

$listCategory = CFilesCategory::listCatClass($selClass);

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$object = null;

if($selClass && $selKey){
  $object = new $selClass;
  $object->load($selKey);
  $object->loadRefsFiles();

  // Classement des fichiers
  $affichageFile = array();
  $affichageFile[""] = array();
  $affichageFile[""]["name"] = "Aucune Catégorie";
  $affichageFile[""]["file"] = array();
  foreach($listCategory as $keyCat => $curr_Cat){
    $affichageFile[$keyCat]["name"] = $curr_Cat->nom;
    $affichageFile[$keyCat]["file"] = array();
  }
  foreach($object->_ref_files as $keyFile =>$FileData){
    $affichageFile[$FileData->file_category_id]["file"][]=$FileData;
  }
  $smarty->assign("affichageFile",$affichageFile);
}
  
$smarty->assign("listCategory", $listCategory);
$smarty->assign("selClass"    , $selClass    );
$smarty->assign("selKey"      , $selKey      );
$smarty->assign("object"      , $object      );
$smarty->assign("typeVue"     , $typeVue     );
if($typeVue==1){
  $smarty->display("inc_list_view_colonne.tpl");
}elseif($typeVue==2){
  $smarty->display("inc_list_view_gd_thumb.tpl");
}else{
  $smarty->display("inc_list_view.tpl");
}
?>
