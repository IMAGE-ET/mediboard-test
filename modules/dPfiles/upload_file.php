<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author Sébastien Fillonneau
*/

$object = mbGetObjectFromGetOrSession("object_class", "object_id", "object_guid");

$file_category_id = CValue::getOrSession("file_category_id");
$_rename          = CValue::getOrSession("_rename");
$uploadok         = CValue::get("uploadok");
$private          = CValue::get("private");
$for_identite     = CValue::get("for_identite");

$listCategory = CFilesCategory::listCatClass($object->_class);

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