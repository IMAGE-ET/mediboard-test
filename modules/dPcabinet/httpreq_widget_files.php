<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision:
* @author Alexis Granger
*/

global $AppUI, $can, $m;
  
$object_id = CValue::get("object_id");
$object_class = CValue::get("object_class");

// Chargement de l'objet
$object = new $object_class;
$object->load($object_id);

// Chargement des fichiers
$object->loadRefsFiles();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->display("inc_widget_vw_files.tpl");

?>