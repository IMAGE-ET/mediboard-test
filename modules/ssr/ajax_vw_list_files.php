<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision:
* @author 
*/

CCanDo::checkRead();
  
$object_id = CValue::get("object_id");
$object_class = CValue::get("object_class");

// Chargement de l'objet
$object = new $object_class;
$object->load($object_id);

// Chargement des fichiers
$object->loadRefsFiles();
foreach ($object->_ref_files as $_file) {
  $_file->loadRefCategory();
}
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->assign("count_object", count($object->_ref_files));
$smarty->display("inc_vw_list_files.tpl");

?>