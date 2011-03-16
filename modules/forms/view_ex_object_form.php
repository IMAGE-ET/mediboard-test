<?php /* $Id: view_messages.php 7622 2009-12-16 09:08:41Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

//CCanDo::checkAdmin();

$ex_class_id  = CValue::get("ex_class_id");
$ex_object_id = CValue::get("ex_object_id");
$object_guid  = CValue::get("object_guid");
$_element_id  = CValue::get("_element_id");
$readonly     = CValue::get("readonly");
$event        = CValue::get("event");

if (!$ex_class_id) {
  $msg = "Impossible d'afficher le formulaire sans conna�tre la classe de base";
  CAppUI::stepAjax($msg, UI_MSG_WARNING);
  trigger_error($msg, E_USER_ERROR);
  return;
}

$object = CMbObject::loadFromGuid($object_guid);
$object->loadComplete();

$ex_object = new CExObject;
$ex_object->setObject($object);
$ex_object->_ex_class_id = $ex_class_id;
$ex_object->setExClass();

list($grid, $out_of_grid, $groups) = $ex_object->_ref_ex_class->getGrid();

if ($ex_object_id) {
  $ex_object->load($ex_object_id);
}

// loadAllFwdRefs ne marche pas bien (a cause de la cl� primaire)
foreach($ex_object->_specs as $_field => $_spec) {
  if ($_spec instanceof CRefSpec && $_field != $ex_object->_spec->key) {
    $obj = new $_spec->class;
    $obj->load($ex_object->$_field);
    $ex_object->_fwd[$_field] = $obj;
  }
}

$fields = $ex_object->_ref_ex_class->loadRefsAllFields();

foreach($fields as $_field) {
  $_field->updateTranslation();
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("ex_object",    $ex_object);
$smarty->assign("ex_object_id", $ex_object_id);
$smarty->assign("ex_class_id",  $ex_class_id);
$smarty->assign("object_guid",  $object_guid);
$smarty->assign("object",       $object);
$smarty->assign("_element_id",  $_element_id);
$smarty->assign("event",        $event);
$smarty->assign("grid",         $grid);
$smarty->assign("out_of_grid",  $out_of_grid);
$smarty->assign("groups",       $groups);
$smarty->assign("readonly",     $readonly);
$smarty->display("view_ex_object_form.tpl");
