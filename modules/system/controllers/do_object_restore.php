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

CCanDo::checkAdmin();

// FIXME la restauration n'est pas totale car seulement le premier user_log de l'objet (date ASC) est utilisé

$user_id      = CValue::post("user_id");
$date         = CValue::post("date");
$object_class = CValue::post("object_class");
$fields       = CValue::post("fields");
$do_it        = CValue::post("do_it");

CValue::setSession("user_id", $user_id);
CValue::setSession("object_class", $object_class);
CValue::setSession("date", $date);

$user_log = new CUserLog;

$where = array(
  "object_class" => "= '$object_class'"
);

if ($user_id) {
  $where["user_id"] = " = '$user_id'";
}

if ($date) {
  $where["date"] = ">= '$date'";
}

$where["type"] = "= 'store'";

if ($fields) {
  $whereField = array();
  foreach ($fields as $_field) {
    $whereField[] = "
      fields LIKE '$_field %' OR 
      fields LIKE '% $_field %' OR 
      fields LIKE '% $_field' OR 
      fields LIKE '$_field'";
  }
  $where[] = implode(" OR ", $whereField);
}

$logs = $user_log->loadList($where, "date ASC", null, "object_id");

foreach ($logs as $_log) {
  $_log->loadTargetObject();
  $_log->getOldValues();
}

foreach ($logs as $_log) {
  foreach ($_log->_old_values as $_field => $_value) {
    if (count($fields) == 0 || in_array($_field, $fields)) {
      $_log->_ref_object->$_field = $_value;
    }
  }
  
  if ($do_it) {
    $_log->_ref_object->repair();
    if ($msg = $_log->_ref_object->store()) {
      CAppUI::setMsg($msg);
    }
  }
}
