<?php /* $Id: view_messages.php 7622 2009-12-16 09:08:41Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

//CCanDo::checkAdmin();

$ex_class_id = CValue::get("ex_class_id");
$object_guid = CValue::get("object_guid");

$ex_class = new CExClass;
$ex_class->load($ex_class_id);
$ex_class->loadRefsFields();

$object = CMbObject::loadFromGuid($object_guid);

$ex_object = new CExObject;
$ex_object->setExClass($ex_class);
$ex_object->setObject($object);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("ex_class", $ex_class);
$smarty->assign("ex_object", $ex_object);
$smarty->assign("object", $object);
$smarty->display("view_ex_object_form.tpl");
