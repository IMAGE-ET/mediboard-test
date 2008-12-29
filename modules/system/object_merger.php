<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision$
* @author Fabien Ménager
*/

global $AppUI, $m;

$objects_id    = mbGetValueFromGet('objects_id'); // array
$objects_class = mbGetValueFromGet('objects_class');
$readonly_class = mbGetValueFromGet('readonly_class');

$objects = $unequal = array();
$result = $checkMerge = null;

if (class_exists($objects_class) && count($objects_id)) {
  foreach ($objects_id as $object_id) {
    $object = new $objects_class;
    
    // the CMbObject is loaded
    if (!$object->load($object_id)){
      $AppUI->setMsg("Chargement impossible de l'objet [$object_id]", UI_MSG_ERROR);
      continue;
    }

    $object->loadAllFwdRefs(true);
    $objects[] = $object;
    
    // On base le résultat sur patient1
    if (!$result) {
      $result = new $objects_class;
      $result->load($object_id);
      $result->loadAllFwdRefs(true);
      $result->_id = null;
    }
  }
  
  // checkMerge
  $checkMerge = $result->checkMerge($objects);
  
  // find unequal fields
  $db_fields = $result->getDBFields();
  foreach ($db_fields as $field => $value) {
    foreach ($objects as $key1 => $object1) {
      foreach ($objects as $key2 => $object2) {
        if ($object1->$field != $object2->$field) {
          $unequal[$field] = true;
        }
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("objects", $objects);
$smarty->assign("objects_class", $objects_class);
$smarty->assign("result",  $result);
$smarty->assign("unequal", $unequal);
$smarty->assign("checkMerge", $checkMerge);
$smarty->assign("list_classes", getInstalledClasses());
$smarty->assign("readonly_class", $readonly_class);

$merger_template = $result ? CAppUI::conf('root_dir')."/modules/{$result->_ref_module->mod_name}/templates/{$result->_class_name}_merger.tpl" : '';
$merger_template = is_readable($merger_template) ? $merger_template : "../../system/templates/object_merger.tpl";

$smarty->display($merger_template);

?>