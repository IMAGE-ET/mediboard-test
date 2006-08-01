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

$cat_id   = mbGetValueFromGetOrSession("cat_id"  , 0);
$selClass = mbGetValueFromGetOrSession("selClass", null);
$keywords = mbGetValueFromGetOrSession("keywords", null);
$selKey   = mbGetValueFromGetOrSession("selKey"  , null);
$selView  = mbGetValueFromGetOrSession("selView" , null);
$file_id  = mbGetValueFromGet("file_id" , null);
$typeVue  = mbGetValueFromGetOrSession("typeVue" , 0);


$file = new CFile;
$file->load($file_id);

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
require_once($AppUI->getSystemClass("smartydp"));
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

$smarty->assign("accordion_open" , $accordion_open);
$smarty->assign("cat_id"      , $cat_id      );
$smarty->assign("listCategory", $listCategory);
$smarty->assign("selClass"    , $selClass    );
$smarty->assign("selKey"      , $selKey      );
$smarty->assign("selView"     , $selView     );
$smarty->assign("file"        , $file        );
$smarty->assign("keywords"    , $keywords    );
$smarty->assign("object"      , $object      );
$smarty->assign("typeVue"     , $typeVue     );

$smarty->display("vw_files.tpl");

?>
