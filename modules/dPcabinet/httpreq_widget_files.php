<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision:
* @author Alexis Granger
*/

$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");
$only_files = CValue::get("only_files", 0);

// Chargement des fichiers
$object->loadRefsFiles();

foreach ($object->_ref_files as $_file) {
  $_file->loadRefCategory();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->display($only_files ? "inc_widget_list_files.tpl" : "inc_widget_vw_files.tpl");

?>