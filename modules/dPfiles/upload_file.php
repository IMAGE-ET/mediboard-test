<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author Sébastien Fillonneau
*/

$object = mbGetObjectFromGetOrSession("object_class", "object_id", "object_guid");

$file_category_id = CValue::getOrSession("file_category_id", null);
$_rename          = CValue::getOrSession("_rename", null);
$uploadok         = CValue::get("uploadok", 0);
$private          = CValue::get("private", 0);
$for_identite     = CValue::get("for_identite", 0);

$listCategory = CFilesCategory::listCatClass($object->_class_name);

$file = new CFile();
$file->private = $private;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object"          , $object);
$smarty->assign("file_category_id", $file_category_id);
$smarty->assign("uploadok"        , $uploadok);
$smarty->assign("listCategory"    , $listCategory);
$smarty->assign("_rename"         , $_rename);
$smarty->assign("for_identite"    , $for_identite);
$smarty->assign("file"            , $file);
$smarty->display("upload_file.tpl");
?>