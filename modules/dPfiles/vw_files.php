<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsRead();

$keywords      = CValue::getOrSession("keywords", null);
$selClass      = CValue::getOrSession("selClass", null);
$selKey        = CValue::getOrSession("selKey"  , null);
$selView       = CValue::getOrSession("selView" , null);
$typeVue       = CValue::getOrSession("typeVue" , 0);
$file_id       = CValue::get("file_id"          , null);
$accordDossier = CValue::get("accordDossier"    , 0);
$reloadlist    = 0;

$file = new CFile;
$file->load($file_id);

$listCategory = CFilesCategory::listCatClass($selClass);
$affichageFile = array();
$object = null;
$canFile       = new CCanDo;

if($selClass && $selKey){
  // Chargement de l'objet
  $object = new $selClass;
  $object->load($selKey);
  $canFile = $object->canDo();
  $affichageFile = CFile::loadDocItemsByObject($object);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("affichageFile"  , $affichageFile);
$smarty->assign("canFile"        , $canFile     );
$smarty->assign("listCategory"   , $listCategory);
$smarty->assign("selClass"       , $selClass    );
$smarty->assign("selKey"         , $selKey      );
$smarty->assign("selView"        , $selView     );
$smarty->assign("file"           , $file        );
$smarty->assign("keywords"       , $keywords    );
$smarty->assign("object"         , $object      );
$smarty->assign("typeVue"        , $typeVue     );
$smarty->assign("reloadlist"     , $reloadlist  );
$smarty->assign("fileSel"        , null);
$smarty->assign("accordDossier"  , $accordDossier);
$smarty->display("vw_files.tpl");

?>