<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$objects_class  = CValue::getOrSession('objects_class');
$readonly_class = CValue::get('readonly_class');
$objects_id     = CValue::get('objects_id');
if (!is_array($objects_id)) {
  $objects_id = explode("-", $objects_id);
}

CMbArray::removeValue("", $objects_id);

$objects = array();
$result = null;
$checkMerge = null;
$statuses = array();

if (class_exists($objects_class) && count($objects_id)) {
  foreach ($objects_id as $object_id) {
    /** @var CMbObject $object */
    $object = new $objects_class;
    
    // the CMbObject is loaded
    if (!$object->load($object_id)) {
      CAppUI::setMsg("Chargement impossible de l'objet [$object_id]", UI_MSG_ERROR);
      continue;
    }
    
    $object->loadView();
    $object->loadAllFwdRefs(true);
    
    $object->_selected = false;
    $object->_disabled = false;
    
    $objects[] = $object;
  }
  
  // Default préselection of first object
  $_selected = reset($objects);
  
  // selection of the first CSejour or CPatient with an ext ID
  if ($objects_class == "CSejour" || $objects_class == "CPatient") {
    $no_extid = array();
    $extid = array();
    
    foreach ($objects as $_object) {
      if (
          $_object instanceof CSejour && $_object->_NDA ||
          $_object instanceof CPatient && $_object->_IPP
      ) {
        $extid[] = $_object;
      }
      else {
        $no_extid[] = $_object;
      }
    }
    
    if (count($no_extid) < count($objects)) {
      // Selection disabled for idex less objects
      if (CAppUI::conf("merge_prevent_base_without_idex") == 1) {
        foreach ($no_extid as $_object) {
          $_object->_disabled = true;
        }

        $_selected = reset($extid);
      }
    }
  }
  
  // Selected object IS selected (!)
  $_selected->_selected = true;
  
  // Check merge
  /** @var CMbObject $result */
  $result = new $objects_class;
  $checkMerge = $result->checkMerge($objects);

  // Merge trivial fields
  foreach (array_keys($result->getPlainFields()) as $field) {
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

$classes = $readonly_class ? array() : CApp::getInstalledClasses();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("objects", $objects);
$smarty->assign("objects_class", $objects_class);
$smarty->assign("objects_id", $objects_id);
$smarty->assign("result",  $result);
$smarty->assign("statuses",  $statuses);
$smarty->assign("counts",  $counts);
$smarty->assign("checkMerge", $checkMerge);
$smarty->assign("list_classes", $classes);
$smarty->assign("alternative_mode", CAppUI::conf("alternative_mode"));
$smarty->assign("readonly_class", $readonly_class);

$smarty->display("object_merger.tpl");

?>