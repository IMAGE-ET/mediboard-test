<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision:
* @author Alexis Granger
*/

$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");  

// Chargement des fichiers
$object->loadRefsFiles();
foreach ($object->_ref_files as $_file) {
  $_file->loadRefCategory();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->display("inc_widget_vw_files.tpl");

?>