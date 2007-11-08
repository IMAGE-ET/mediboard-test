<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Romain Ollivier
*/

global $can, $m, $ajax;

$object_class = mbGetValueFromGet("object_class");
$object_id    = mbGetValueFromGet("object_id");

if (!$object_class || !$object_id) {
  return;
}

$object = new $object_class;
$object->load($object_id);
if (!$object->_id) {
  $AppUI->redirect("?ajax=$ajax&suppressHeaders=1&m=$m&a=object_not_found&object_classname=$object_class");
}

$object->loadComplete();

$canModule = CModule::getCanDo($object->_ref_module->mod_name);
$can->read = $canModule->read && $object->canRead();
$can->needsRead();

// If no template is defined, use generic
$template = is_file($object->_view_template) ?
  $object->_view_template : 
  "system/templates/CMbObject_view.tpl";

if($object->_class_name == "CSejour"){
  $object->loadNumDossier();
}
if($object->_class_name == "CPatient"){
  $object->loadIPP();
  $object->loadRefDossierMedical();
  $object->_ref_dossier_medical->loadRefsAntecedents();
  $object->_ref_dossier_medical->loadRefsAddictions();
  $object->_ref_dossier_medical->loadRefsTraitements();
  
}  
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canSante400", CModule::getCanDo("dPsante400"));

$smarty->assign("object", $object);

$smarty->display("../../$object->_complete_view_template");
?>