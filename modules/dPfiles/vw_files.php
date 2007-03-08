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

$canEditFile   = false;
$selClass      = mbGetValueFromGetOrSession("selClass", null);
$keywords      = mbGetValueFromGetOrSession("keywords", null);
$selKey        = mbGetValueFromGetOrSession("selKey"  , null);
$selView       = mbGetValueFromGetOrSession("selView" , null);
$typeVue       = mbGetValueFromGetOrSession("typeVue" , 0);
$file_id       = mbGetValueFromGet("file_id"          , null);
$accordDossier = mbGetValueFromGet("accordDossier"    , 0);
$reloadlist = 0;

$file = new CFile;
$file->load($file_id);

$listCategory = CFilesCategory::listCatClass($selClass);

// Création du template
$smarty = new CSmartyDP();

$object = null;

if($selClass && $selKey){
  // Chargement de l'objet
  $object = new $selClass;
  $object->load($selKey);
  $canEditFile = $object->canEdit();
  $affichageFile = CFile::loadFilesAndDocsByObject($object);

  $smarty->assign("affichageFile",$affichageFile);
}

$smarty->assign("canEditFile"    , $canEditFile);
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