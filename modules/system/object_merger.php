<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $m;

$objects_class = CValue::get('objects_class');
$readonly_class = CValue::get('readonly_class');
$objects_id    = CValue::get('objects_id');
if (!is_array($objects_id)) {
	$objects_id = explode("-", $objects_id);
}

$objects = array();
$result = null;
$checkMerge = null;
$statuses = array();
if (class_exists($objects_class) && count($objects_id)) {
  foreach ($objects_id as $object_id) {
    $object = new $objects_class;
    
    // the CMbObject is loaded
    if (!$object->load($object_id)){
      CAppUI::setMsg("Chargement impossible de l'objet [$object_id]", UI_MSG_ERROR);
      continue;
    }

    $object->loadAllFwdRefs(true);
    $objects[] = $object;
  }
  
  // Check merge
	$result = new $objects_class;
  $checkMerge = $result->checkMerge($objects);

  // Merge trivial fields
  foreach (array_keys($result->getDBFields()) as $field) {
    $values = CMbArray::pluck($objects, $field);
    CMbArray::removeValue("", $values);

    // No values
    if (!count($values)) {
      $statuses[$field] = "none";
			continue;
		}
		
    $result->$field = reset($values);

    // One unique value
    if (count($values) == 1) {
      $statuses[$field] = "unique";
      continue;
    }

    // Multiple values
    $statuses[$field] = count(array_unique($values)) == 1 ? "duplicate" : "multiple";
  }


  $result->updateFormFields();
  $result->loadAllFwdRefs(true);
}

// Count statuses
$counts = array(
  "none"      => 0,
  "unique"    => 0,
  "duplicate" => 0,
  "multiple"  => 0,
);

foreach ($statuses as $status) {
	$counts[$status]++;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("objects", $objects);
$smarty->assign("objects_class", $objects_class);
$smarty->assign("objects_id", $objects_id);
$smarty->assign("result",  $result);
$smarty->assign("statuses",  $statuses);
$smarty->assign("counts",  $counts);
$smarty->assign("checkMerge", $checkMerge);
$smarty->assign("list_classes", CApp::getInstalledClasses());
$smarty->assign("alternative_mode", CAppUI::conf("alternative_mode"));
$smarty->assign("readonly_class", $readonly_class);

$merger_template = $result ? "../../{$result->_ref_module->mod_name}/templates/{$result->_class_name}_merger.tpl" : '';
$merger_template = is_readable($merger_template) ? $merger_template : "../../system/templates/object_merger.tpl";

$smarty->display($merger_template);

?>