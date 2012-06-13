<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$ex_class_field_id = CValue::post("ex_class_field_id");

$ex_class_field = new CExClassField;
$ex_class_field->load($ex_class_field_id);

if (empty($_POST["value"])) {
  $_POST["value"] = $_POST[$ex_class_field->name];
}

$do = new CDoObjectAddEdit("CExClassFieldPredicate");
$do->doIt();
