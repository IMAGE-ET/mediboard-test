<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$user_id      = CValue::get("user_id");
$date         = CValue::get("date");
$object_class = CValue::get("object_class");
$fields       = CValue::get("fields");

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

if ($fields){
  $whereField = array();
  foreach($fields as $_field) {
    $whereField[] = "
      fields LIKE '$_field %' OR 
      fields LIKE '% $_field %' OR 
      fields LIKE '% $_field' OR 
      fields LIKE '$_field'";
  }
  $where[] = implode(" OR ", $whereField);
}

$count = count($user_log->countMultipleList($where, "date ASC", "object_id"));
echo $count;
CApp::rip();
