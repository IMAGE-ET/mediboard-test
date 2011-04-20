<?php /* $Id: view_messages.php 7622 2009-12-16 09:08:41Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$reference_class = CValue::get("reference_class");
$reference_id    = CValue::get("reference_id");

CValue::setSession('reference_class', $reference_class);
CValue::setSession('reference_id',    $reference_id);

$reference = new $reference_class;

if ($reference_id) {
	$reference->load($reference_id);
}

CExClassField::$_load_lite = true;

$ex_class = new CExClass;
$ex_classes = $ex_class->loadList();

$all_ex_objects = array();

foreach($ex_classes as $_ex_class) {
	$_ex_class->loadRefsGroups();
	
	CExObject::$_load_lite = true;
	
	//$_class_name = $_ex_class->getClassName();
	$_ex_object = new CExObject;
	$_ex_object->_ex_class_id = $_ex_class->_id;
	$_ex_object->setExClass();
	
	$where = array(
	  "(reference_class  = '$reference_class' AND reference_id = '$reference_id') OR 
		 (reference2_class = '$reference_class' AND reference2_id = '$reference_id')"
	);
	$_ex_objects = $_ex_object->loadList($where);
	
	CExObject::$_load_lite = false;
	
	foreach($_ex_objects as $_ex) {
		$_ex->_ex_class_id = $_ex_class->_id;
		$_ex->setExClass();
    //$_ex->_ref_ex_class->getGrid();
		
    $_ex->_ref_ex_class = $_ex_class;
		
    $_ex->getProps();
    $_ex->getSpecs();
		
		foreach($_ex->_ref_ex_class->_ref_groups as $_group) {
			$_group->loadRefsFields();
			foreach($_group->_ref_fields as $_field) {
				$_field->updateTranslation();
			}
		}
		
    $_ex->loadLogs();
		$_log = $_ex->_ref_first_log;
		$all_ex_objects["$_log->date $_ex->_id"] = $_ex;
	}
}

ksort($all_ex_objects);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("reference_class", $reference_class);
$smarty->assign("reference_id",    $reference_id);
$smarty->assign("reference",       $reference);
$smarty->assign("all_ex_objects",  $all_ex_objects);
$smarty->display("inc_list_ex_object.tpl");
