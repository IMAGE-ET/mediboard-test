<?php /* $Id: view_messages.php 7622 2009-12-16 09:08:41Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$ex_class_id = CValue::getOrSession("ex_class_id");

$ex_class = new CExClass;
$ex_class->load($ex_class_id);
$ex_class->loadRefsFields();

foreach($ex_class->_ref_fields as $_ex_field) {
  $_ex_field->getSpecObject();
}

$list_ex_class = $ex_class->loadList(null, "host_class, event");

/*$_ex_class = reset($list_ex_class);

$ex_class_field = new CExClassField;
$ex_class_field->ex_class_id = $_ex_class->_id;
$ex_class_field->name = "test5";
$ex_class_field->prop = "str";
mbTrace($ex_class_field->store());*/

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("ex_class", $ex_class);
$smarty->assign("list_ex_class", $list_ex_class);
$smarty->display("view_ex_class.tpl");
