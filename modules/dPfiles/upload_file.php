<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $can;

$object_class     = CValue::getOrSession("object_class");
$object_id        = CValue::getOrSession("object_id");
$file_category_id = CValue::getOrSession("file_category_id", null);
$file_rename      = CValue::getOrSession("file_rename", null);
$uploadok         = CValue::get("uploadok", 0);

$nb_files_upload = CMbArray::createRange(1, ($file_rename ? 1 : CAppUI::conf("dPfiles nb_upload_files")) ,true);

$object = new $object_class;
$object->load($object_id);
$listCategory = CFilesCategory::listCatClass($object_class);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object_class"    , $object_class);
$smarty->assign("object_id"       , $object_id);
$smarty->assign("file_category_id", $file_category_id);
$smarty->assign("uploadok"        , $uploadok);
$smarty->assign("nb_files_upload" , $nb_files_upload);
$smarty->assign("object"          , $object);
$smarty->assign("listCategory"    , $listCategory);
$smarty->assign("file_rename"     , $file_rename);

$smarty->display("upload_file.tpl");
?>