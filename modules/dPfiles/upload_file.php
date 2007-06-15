<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $dPconfig;

$object_class     = mbGetValueFromGetOrSession("object_class");
$object_id        = mbGetValueFromGetOrSession("object_id");
$file_category_id = mbGetValueFromGetOrSession("file_category_id", null);
$uploadok         = mbGetValueFromGet("uploadok", 0);

$nb_files_upload = mbArrayCreateRange(1,$dPconfig["dPfiles"]["nb_upload_files"],true);

$object = new $object_class;
$object->load($object_id);
$listCategory = CFilesCategory::listCatClass($object_class);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object_class"    , $object_class);
$smarty->assign("object_id"       , $object_id);
$smarty->assign("file_category_id", $file_category_id);
$smarty->assign("upload_max_size" , ini_get("upload_max_filesize"));
$smarty->assign("uploadok"        , $uploadok);
$smarty->assign("nb_files_upload" , $nb_files_upload);
$smarty->assign("object"          , $object);
$smarty->assign("listCategory"    , $listCategory);

$smarty->display("upload_file.tpl");
?>