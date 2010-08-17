<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author Sébastien Fillonneau
*/

$object = mbGetObjectFromGetOrSession("object_class", "object_id", "object_guid");

$file_category_id = CValue::getOrSession("file_category_id", null);
$file_rename      = CValue::getOrSession("file_rename", null);
$uploadok         = CValue::get("uploadok", 0);
$private          = CValue::get("private", 0);

$nb_files_upload = range(1, ($file_rename ? 1 : CAppUI::conf("dPfiles nb_upload_files")) ,true);
mbTrace($nb_files_upload);
$listCategory = CFilesCategory::listCatClass($object->_class_name);

$file = new CFile();
$file->private = $private;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object"          , $object);
$smarty->assign("file_category_id", $file_category_id);
$smarty->assign("uploadok"        , $uploadok);
$smarty->assign("nb_files_upload" , $nb_files_upload);
$smarty->assign("listCategory"    , $listCategory);
$smarty->assign("file_rename"     , $file_rename);
$smarty->assign("file"            , $file);
$smarty->display("upload_file.tpl");
?>