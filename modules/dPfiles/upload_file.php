<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $dPconfig;

$file_class       = mbGetValueFromGetOrSession("file_class");
$file_object_id   = mbGetValueFromGetOrSession("file_object_id");
$file_category_id = mbGetValueFromGetOrSession("file_category_id", null);
$uploadok         = mbGetValueFromGet("uploadok", 0);

$nb_files_upload = mbArrayCreateRange(1,$dPconfig["dPfiles"]["nb_upload_files"],true);

$object = new $file_class;
$object->load($file_object_id);
$listCategory = CFilesCategory::listCatClass($file_class);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("file_class"      , $file_class);
$smarty->assign("file_object_id"  , $file_object_id);
$smarty->assign("file_category_id", $file_category_id);
$smarty->assign("upload_max_size" , ini_get("upload_max_filesize"));
$smarty->assign("uploadok"        , $uploadok);
$smarty->assign("nb_files_upload" , $nb_files_upload);
$smarty->assign("object"          , $object);
$smarty->assign("listCategory"    , $listCategory);

$smarty->display("upload_file.tpl");
?>